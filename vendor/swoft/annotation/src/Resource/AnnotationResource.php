<?php declare(strict_types=1);

namespace Swoft\Annotation\Resource;

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ReflectionClass;
use ReflectionException;
use SplFileInfo;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\AnnotationRegister;
use Swoft\Annotation\Contract\LoaderInterface;
use Swoft\Stdlib\Helper\ComposerHelper;
use Swoft\Stdlib\Helper\DirectoryHelper;
use Swoft\Stdlib\Helper\ObjectHelper;
use function class_exists;
use function file_exists;
use function get_included_files;
use function in_array;
use function is_dir;
use function realpath;
use function sprintf;
use function str_replace;
use function strpos;

/**
 * Annotation resource
 *
 * @since 2.0
 */
class AnnotationResource extends Resource
{
    /**
     * Default excluded psr4 prefixes
     */
    public const DEFAULT_EXCLUDED_PSR4_PREFIXES = [
        'Psr\\',
        'Monolog\\',
        'PHPUnit\\',
        'Symfony\\'
    ];

    /**
     * @var ClassLoader
     */
    private $classLoader;

    /**
     * @var string
     */
    private $basePath = '';

    /**
     * @var callable
     */
    private $notifyHandler;

    /**
     * @var string
     */
    private $loaderClassSuffix = 'php';

    /**
     * @var string
     */
    private $loaderClassName = 'AutoLoader';

    /**
     * Skip listed file names when parsing annotations
     *
     * @var array
     */
    private $excludedFilenames = [
        'Swoft.php' => 1,
    ];

    /**
     * Scans containing these namespace prefixes will be excluded
     *
     * @var array
     * eg. ['Psr\\', 'PHPUnit\\', 'Symfony\\']
     */
    private $excludedPsr4Prefixes;

    /**
     * Can disable AutoLoader class before load component classes.
     * eg. [ Swoft\Console\AutoLoader::class  => 1 ]
     *
     * @var array
     */
    private $disabledAutoLoaders = [];

    /**
     * Only scan namespace. Default is scan all
     *
     * @var array
     */
    private $onlyNamespaces = [];

    /**
     * @var bool
     */
    private $inPhar = false;

    /**
     * Included files
     *
     * @var array
     */
    private $includedFiles;

    /**
     * AnnotationResource constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        // Init $excludedPsr4Prefixes
        $this->excludedPsr4Prefixes = self::DEFAULT_EXCLUDED_PSR4_PREFIXES;

        // Can set property by array
        ObjectHelper::init($this, $config);

        $this->registerLoader();
        $this->classLoader = ComposerHelper::getClassLoader();

        $this->includedFiles = get_included_files();
    }

    /**
     * Load annotation resource by find ClassLoader
     *
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function load(): void
    {
        $prefixDirsPsr4 = $this->classLoader->getPrefixesPsr4();

        foreach ($prefixDirsPsr4 as $ns => $paths) {
            // Only scan namespaces
            if ($this->onlyNamespaces && !in_array($ns, $this->onlyNamespaces, true)) {
                $this->notify('excludeNs', $ns);
                continue;
            }

            // It is excluded psr4 prefix
            if ($this->isExcludedPsr4Prefix($ns)) {
                AnnotationRegister::registerExcludeNs($ns);
                $this->notify('excludeNs', $ns);
                continue;
            }

            // Find package/component loader class
            foreach ($paths as $path) {
                $loaderFile = $this->getAnnotationClassLoaderFile($path);
                if (!file_exists($loaderFile)) {
                    $this->notify('noLoaderFile', $this->clearBasePath($path), $loaderFile);
                    continue;
                }

                $loaderClass = $this->getAnnotationLoaderClassName($ns);
                if (!class_exists($loaderClass)) {
                    $this->notify('noLoaderClass', $loaderClass);
                    continue;
                }

                $loaderObject = new $loaderClass();
                if (!$loaderObject instanceof LoaderInterface) {
                    $this->notify('invalidLoader', $loaderFile);
                    continue;
                }

                $this->notify('findLoaderClass', $this->clearBasePath($loaderFile));

                // If is disable, will skip scan annotation classes
                if (!isset($this->disabledAutoLoaders[$loaderClass])) {
                    AnnotationRegister::registerAutoLoaderFile($loaderFile);
                    $this->notify('addLoaderClass', $loaderClass);
                    $this->loadAnnotation($loaderObject);
                }

                // Storage auto loader to register
                AnnotationRegister::addAutoLoader($ns, $loaderObject);
            }
        }
    }

    /**
     * @param string $filePath
     *
     * @return string
     */
    public function clearBasePath(string $filePath): string
    {
        if ($this->basePath) {
            $basePath = ($this->inPhar ? 'phar://' : '') . $this->basePath;

            return str_replace($basePath, '{PROJECT}', $filePath);
        }

        return $filePath;
    }

    /**
     * @param string $namespace
     *
     * @return bool
     */
    public function isExcludedPsr4Prefix(string $namespace): bool
    {
        foreach ($this->excludedPsr4Prefixes as $prefix) {
            if (0 === strpos($namespace, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load annotations from an component loader config.
     *
     * @param LoaderInterface $loader
     *
     * @throws AnnotationException
     * @throws ReflectionException
     */
    private function loadAnnotation(LoaderInterface $loader): void
    {
        $nsPaths = $loader->getPrefixDirs();

        foreach ($nsPaths as $ns => $path) {
            $iterator = DirectoryHelper::recursiveIterator($path);

            /* @var SplFileInfo $splFileInfo */
            foreach ($iterator as $splFileInfo) {
                $filePath = $splFileInfo->getPathname();
                // $splFileInfo->isDir();
                if (is_dir($filePath)) {
                    continue;
                }

                $fileName  = $splFileInfo->getFilename();
                $extension = $splFileInfo->getExtension();

                if ($this->loaderClassSuffix !== $extension || strpos($fileName, '.') === 0) {
                    continue;
                }

                // It is exclude filename
                if (isset($this->excludedFilenames[$fileName])) {
                    AnnotationRegister::registerExcludeFilename($fileName);
                    continue;
                }

                $suffix    = sprintf('.%s', $this->loaderClassSuffix);
                $pathName  = str_replace([$path, '/', $suffix], ['', '\\', ''], $filePath);
                $className = sprintf('%s%s', $ns, $pathName);

                // Fix repeat included file bug
                $autoload = in_array($filePath, $this->includedFiles, true);

                // Will filtering: interfaces and traits
                if (!class_exists($className, !$autoload)) {
                    $this->notify('noExistClass', $className);
                    continue;
                }

                // Parse annotation
                $this->parseAnnotation($ns, $className);
            }
        }
    }

    /**
     * @return ClassLoader
     */
    public function getClassLoader(): ClassLoader
    {
        return $this->classLoader;
    }

    /**
     * @param ClassLoader $classLoader
     */
    public function setClassLoader(ClassLoader $classLoader): void
    {
        $this->classLoader = $classLoader;
    }

    /**
     * @return string
     */
    public function getLoaderClassName(): string
    {
        return $this->loaderClassName;
    }

    /**
     * @param string $loaderClassName
     */
    public function setLoaderClassName(string $loaderClassName): void
    {
        $this->loaderClassName = $loaderClassName;
    }

    /**
     * Parser annotation
     *
     * @param string $namespace
     * @param string $className
     *
     * @throws AnnotationException
     * @throws ReflectionException
     */
    private function parseAnnotation(string $namespace, string $className): void
    {
        // Annotation reader
        $reflectionClass = new ReflectionClass($className);

        // Fix ignore abstract
        if ($reflectionClass->isAbstract()) {
            return;
        }
        $oneClassAnnotation = $this->parseOneClassAnnotation($reflectionClass);

        if (!empty($oneClassAnnotation)) {
            AnnotationRegister::registerAnnotation($namespace, $className, $oneClassAnnotation);
        }
    }

    /**
     * Parse an class annotation
     *
     * @param ReflectionClass $reflectionClass
     *
     * @return array
     * @throws AnnotationException
     * @throws ReflectionException
     */
    private function parseOneClassAnnotation(ReflectionClass $reflectionClass): array
    {
        // Annotation reader
        $reader    = new AnnotationReader();
        $className = $reflectionClass->getName();

        $oneClassAnnotation = [];
        $classAnnotations   = $reader->getClassAnnotations($reflectionClass);

        // Register annotation parser
        foreach ($classAnnotations as $classAnnotation) {
            if ($classAnnotation instanceof AnnotationParser) {
                $this->registerParser($className, $classAnnotation);

                return [];
            }
        }

        // Class annotation
        if (!empty($classAnnotations)) {
            $oneClassAnnotation['annotation'] = $classAnnotations;
            $oneClassAnnotation['reflection'] = $reflectionClass;
        }

        // Property annotation
        $reflectionProperties = $reflectionClass->getProperties();
        foreach ($reflectionProperties as $reflectionProperty) {
            $propertyName        = $reflectionProperty->getName();
            $propertyAnnotations = $reader->getPropertyAnnotations($reflectionProperty);

            if (!empty($propertyAnnotations)) {
                $oneClassAnnotation['properties'][$propertyName]['annotation'] = $propertyAnnotations;
                $oneClassAnnotation['properties'][$propertyName]['reflection'] = $reflectionProperty;
            }
        }

        // Method annotation
        $reflectionMethods = $reflectionClass->getMethods();
        foreach ($reflectionMethods as $reflectionMethod) {
            $methodName        = $reflectionMethod->getName();
            $methodAnnotations = $reader->getMethodAnnotations($reflectionMethod);

            if (!empty($methodAnnotations)) {
                $oneClassAnnotation['methods'][$methodName]['annotation'] = $methodAnnotations;
                $oneClassAnnotation['methods'][$methodName]['reflection'] = $reflectionMethod;
            }
        }

        $parentReflectionClass = $reflectionClass->getParentClass();
        if ($parentReflectionClass !== false) {
            $parentClassAnnotation = $this->parseOneClassAnnotation($parentReflectionClass);
            if (!empty($parentClassAnnotation)) {
                $oneClassAnnotation['parent'] = $parentClassAnnotation;
            }
        }

        return $oneClassAnnotation;
    }

    /**
     * Register annotation parser
     *
     * @param string           $parserClassName
     * @param AnnotationParser $annotationParser
     */
    private function registerParser(string $parserClassName, AnnotationParser $annotationParser): void
    {
        $annotationClass = $annotationParser->getAnnotation();

        AnnotationRegister::registerParser($annotationClass, $parserClassName);
    }

    /**
     * Register annotation loader
     */
    private function registerLoader(): void
    {
        AnnotationRegistry::registerLoader(function (string $class) {
            if (class_exists($class)) {
                return true;
            }

            return false;
        });
    }

    /**
     * Get annotation loader file
     *
     * @param string $path
     *
     * @return string
     */
    private function getAnnotationClassLoaderFile(string $path): string
    {
        $path = $this->inPhar ? $path : (string)realpath($path);

        return sprintf('%s/%s.%s', $path, $this->loaderClassName, $this->loaderClassSuffix);
    }

    /**
     * Get the class name of annotation loader
     *
     * @param string $namespace
     *
     * @return string
     */
    private function getAnnotationLoaderClassName(string $namespace): string
    {
        return sprintf('%s%s', $namespace, $this->loaderClassName);
    }

    /**
     * @return array
     */
    public function getExcludedPsr4Prefixes(): array
    {
        return $this->excludedPsr4Prefixes;
    }

    /**
     * @param array $excludedPsr4Prefixes
     */
    public function setExcludedPsr4Prefixes(array $excludedPsr4Prefixes): void
    {
        $this->excludedPsr4Prefixes = $excludedPsr4Prefixes;
    }

    /**
     * @return array
     */
    public function getExcludedFilenames(): array
    {
        return $this->excludedFilenames;
    }

    /**
     * @param array $excludedFilenames
     */
    public function setExcludedFilenames(array $excludedFilenames): void
    {
        $this->excludedFilenames = $excludedFilenames;
    }

    /**
     * @return array
     */
    public function getDisabledAutoLoaders(): array
    {
        return $this->disabledAutoLoaders;
    }

    /**
     * @param array $disabledAutoLoaders
     */
    public function setDisabledAutoLoaders(array $disabledAutoLoaders): void
    {
        $this->disabledAutoLoaders = $disabledAutoLoaders;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * @return array
     */
    public function getOnlyNamespaces(): array
    {
        return $this->onlyNamespaces;
    }

    /**
     * @param array $onlyNamespaces
     */
    public function setOnlyNamespaces(array $onlyNamespaces): void
    {
        $this->onlyNamespaces = $onlyNamespaces;
    }

    /**
     * @return bool
     */
    public function isInPhar(): bool
    {
        return $this->inPhar;
    }

    /**
     * @param bool $inPhar
     */
    public function setInPhar(bool $inPhar): void
    {
        $this->inPhar = $inPhar;
    }

    /**
     * Notify operation
     *
     * @param string $type
     * @param mixed  ...$target
     */
    public function notify(string $type, ...$target): void
    {
        if ($this->notifyHandler) {
            ($this->notifyHandler)($type, ...$target);
        }
    }

    /**
     * @param callable $notifyHandler
     */
    public function setNotifyHandler($notifyHandler): void
    {
        $this->notifyHandler = $notifyHandler;
    }
}
