<?php declare(strict_types=1);

namespace Swoft;

use Swoft;
use Swoft\Concern\SwoftTrait;
use Swoft\Contract\ApplicationInterface;
use Swoft\Contract\SwoftInterface;
use Swoft\Helper\SwoftHelper;
use Swoft\Processor\AnnotationProcessor;
use Swoft\Processor\ApplicationProcessor;
use Swoft\Processor\BeanProcessor;
use Swoft\Processor\ConfigProcessor;
use Swoft\Processor\ConsoleProcessor;
use Swoft\Processor\EnvProcessor;
use Swoft\Processor\EventProcessor;
use Swoft\Processor\Processor;
use Swoft\Processor\ProcessorInterface;
use Swoft\Stdlib\Helper\ComposerHelper;
use Swoft\Stdlib\Helper\FSHelper;
use Swoft\Stdlib\Helper\ObjectHelper;
use Swoft\Stdlib\Helper\Str;
use Swoft\Log\Helper\CLog;
use function define;
use function defined;
use function dirname;
use const IN_PHAR;

/**
 * Swoft application
 *
 * @since 2.0
 */
class SwoftApplication implements SwoftInterface, ApplicationInterface
{
    use SwoftTrait;

    /**
     * Base path
     *
     * @var string
     */
    protected $basePath = '';

    /**
     * Env file
     *
     * @var string
     */
    protected $envFile = '@base/.env';

    /**
     * Application path
     *
     * @var string
     */
    protected $appPath = '@base/app';

    /**
     * Default bean file
     *
     * @var string
     */
    protected $beanFile = '@app/bean.php';

    /**
     * Config path
     *
     * @var string
     */
    protected $configPath = '@base/config';

    /**
     * Runtime path
     *
     * @var string
     */
    protected $runtimePath = '@base/runtime';

    /**
     * @var string
     */
    protected $resourcePath = '@base/resource';

    /**
     * @var ApplicationProcessor
     */
    private $processor;

    /**
     * Can disable processor class before handle.
     * eg.
     * [
     *  Swoft\Processor\ConsoleProcessor::class => 1,
     * ]
     *
     * @var array
     */
    private $disabledProcessors = [];

    /**
     * Can disable AutoLoader class before handle.
     * eg.
     * [
     *  Swoft\Console\AutoLoader::class  => 1,
     * ]
     *
     * @var array
     */
    private $disabledAutoLoaders = [];

    /**
     * Scans containing these namespace prefixes will be excluded.
     *
     * @var array
     * eg.
     * [
     *  'PHPUnit\\',
     * ]
     */
    private $disabledPsr4Prefixes = [];

    /**
     * Get the application version
     * @return string
     */
    public static function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Class constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        // Check runtime env
        SwoftHelper::checkRuntime();

        // Storage as global static property.
        Swoft::$app = $this;

        // Before init
        $this->beforeInit();

        // Init console logger
        $this->initCLogger();

        // Can setting properties by array
        if ($config) {
            ObjectHelper::init($this, $config);
        }

        // Init application
        $this->init();

        CLog::info('Project path is <info>%s</info>', $this->basePath);

        // After init
        $this->afterInit();
    }

    protected function beforeInit(): void
    {
        // Check phar env
        if (!defined('IN_PHAR')) {
            define('IN_PHAR', false);
        }
    }

    protected function init(): void
    {
        // Init system path aliases
        $this->findBasePath();
        $this->setSystemAlias();

        $processors = $this->processors();

        $this->processor = new ApplicationProcessor($this);
        $this->processor->addFirstProcessor(...$processors);
    }

    protected function afterInit(): void
    {
        // If run in phar package
        if (IN_PHAR) {
            $runtimePath = Swoft::getAlias($this->runtimePath);
            $this->setRuntimePath(Str::rmPharPrefix($runtimePath));
        }

        // Do something ...
        // $this->disableProcessor(ConsoleProcessor::class, EnvProcessor::class);
    }

    private function findBasePath()
    {
        if ($this->basePath) {
            return;
        }

        // Get bash path from current class file.
        $filePath = ComposerHelper::getClassLoader()->findFile(static::class);
        $filePath = FSHelper::conv2abs($filePath, false);

        $this->basePath = dirname($filePath, 2);
    }

    /**
     * Run application
     */
    public function run(): void
    {
        if (!$this->beforeRun()) {
            return;
        }

        $this->processor->handle();
    }

    /**
     * @param string[] $classes
     */
    public function disableAutoLoader(string ...$classes)
    {
        foreach ($classes as $class) {
            $this->disabledAutoLoaders[$class] = 1;
        }
    }

    /**
     * @param string ...$classes
     */
    public function disableProcessor(string ...$classes)
    {
        foreach ($classes as $class) {
            $this->disabledProcessors[$class] = 1;
        }
    }

    /**
     * Add first processors
     *
     * @param Processor[] $processors
     *
     * @return bool
     */
    public function addFirstProcessor(Processor ...$processors): bool
    {
        return $this->processor->addFirstProcessor(...$processors);
    }

    /**
     * Add last processors
     *
     * @param Processor[] $processors
     *
     * @return true
     */
    public function addLastProcessor(Processor ...$processors): bool
    {
        return $this->processor->addLastProcessor(...$processors);
    }

    /**
     * Add processors
     *
     * @param int         $index
     * @param Processor[] $processors
     *
     * @return true
     */
    public function addProcessor(int $index, Processor ...$processors): bool
    {
        return $this->processor->addProcessor($index, ... $processors);
    }

    /**
     * @return ProcessorInterface[]
     */
    protected function processors(): array
    {
        return [
            new EnvProcessor($this),
            new ConfigProcessor($this),
            new AnnotationProcessor($this),
            new BeanProcessor($this),
            new EventProcessor($this),
            new ConsoleProcessor($this),
        ];
    }

    /**
     * @return array
     */
    public function getDisabledProcessors(): array
    {
        return $this->disabledProcessors;
    }

    /**
     * @return array
     */
    public function getDisabledAutoLoaders(): array
    {
        return $this->disabledAutoLoaders;
    }

    /**
     * @return array
     */
    public function getDisabledPsr4Prefixes(): array
    {
        return $this->disabledPsr4Prefixes;
    }

    /**
     * @param string $beanFile
     */
    public function setBeanFile(string $beanFile): void
    {
        $this->beanFile = $beanFile;
    }

    /**
     * @return string
     */
    public function getBeanFile(): string
    {
        return $this->beanFile;
    }

    /**
     * @param string $relativePath
     *
     * @return string
     */
    public function getPath(string $relativePath): string
    {
        return $this->basePath . '/' . $relativePath;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @return string
     */
    public function getAppPath(): string
    {
        return $this->appPath;
    }

    /**
     * @return string
     */
    public function getRuntimePath(): string
    {
        return $this->runtimePath;
    }

    /**
     * @return string
     */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    /**
     * @param string $envFile
     */
    public function setEnvFile(string $envFile): void
    {
        $this->envFile = $envFile;
    }

    /**
     * @param string $appPath
     */
    public function setAppPath(string $appPath): void
    {
        $this->appPath = $appPath;

        Swoft::setAlias('@app', $appPath);
    }

    /**
     * @param string $configPath
     */
    public function setConfigPath(string $configPath): void
    {
        $this->configPath = $configPath;

        Swoft::setAlias('@config', $configPath);
    }

    /**
     * @param string $basePath
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;

        Swoft::setAlias('@base', $basePath);
    }

    /**
     * @param string $runtimePath
     */
    public function setRuntimePath(string $runtimePath): void
    {
        $this->runtimePath = $runtimePath;

        Swoft::setAlias('@runtime', $runtimePath);
    }

    /**
     * Get console logger config
     *
     * @return array
     */
    public function getCLoggerConfig(): array
    {
        return [
            'name'    => 'swoft',
            'enable'  => true,
            'output'  => true,
            'levels'  => '',
            'logFile' => ''
        ];
    }

    /**
     * @return string
     */
    public function getResourcePath(): string
    {
        return $this->resourcePath;
    }

    /**
     * @param string $resourcePath
     */
    public function setResourcePath(string $resourcePath): void
    {
        $this->resourcePath = $resourcePath;
    }

    /**
     * Init console logger
     */
    private function initCLogger(): void
    {
        // Console logger config
        $config = $this->getCLoggerConfig();

        // Init console log
        CLog::init($config);
    }

    /**
     * Set base path
     */
    private function setSystemAlias(): void
    {
        $basePath     = $this->getBasePath();
        $appPath      = $this->getAppPath();
        $configPath   = $this->getConfigPath();
        $runtimePath  = $this->getRuntimePath();
        $resourcePath = $this->getResourcePath();

        Swoft::setAlias('@base', $basePath);
        Swoft::setAlias('@app', $appPath);
        Swoft::setAlias('@config', $configPath);
        Swoft::setAlias('@runtime', $runtimePath);
        Swoft::setAlias('@resource', $resourcePath);

        CLog::info('Set alias @base=%s', $basePath);
        CLog::info('Set alias @app=%s', $appPath);
        CLog::info('Set alias @config=%s', $configPath);
        CLog::info('Set alias @runtime=%s', $runtimePath);
    }
}
