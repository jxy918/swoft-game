<?php declare(strict_types=1);

namespace Swoft\Bean\Definition\Parser;

use function array_merge;
use function count;
use function get_class;
use InvalidArgumentException;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\Annotation\Parser\ParserInterface;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\Bean\Definition\MethodInjection;
use Swoft\Bean\Definition\ObjectDefinition;
use Swoft\Bean\Definition\PropertyInjection;

/**
 * Class AnnotationParser
 *
 * @since 2.0
 */
class AnnotationObjParser extends ObjectParser
{
    /**
     * All load annotations
     *
     * @var array
     *
     * @example
     * [
     *    'loadNamespace' => [
     *        'className' => [
     *             'annotation' => [
     *                  new ClassAnnotation(),
     *                  new ClassAnnotation(),
     *                  new ClassAnnotation(),
     *             ]
     *             'reflection' => new ReflectionClass(),
     *             'properties' => [
     *                  'propertyName' => [
     *                      'annotation' => [
     *                          new PropertyAnnotation(),
     *                          new PropertyAnnotation(),
     *                          new PropertyAnnotation(),
     *                      ]
     *                     'reflection' => new ReflectionProperty(),
     *                  ]
     *             ],
     *            'methods' => [
     *                  'methodName' => [
     *                      'annotation' => [
     *                          new MethodAnnotation(),
     *                          new MethodAnnotation(),
     *                          new MethodAnnotation(),
     *                      ]
     *                     'reflection' => new ReflectionFunctionAbstract(),
     *                  ]
     *            ],
     *        ]
     *    ]
     * ]
     */
    private $annotations = [];

    /**
     * Annotation parser
     *
     * @var array
     *
     * @example
     * [
     *    'annotationClassName' => 'annotationParserClassName',
     * ]
     */
    private $parsers = [];

    /**
     * Parse annotations
     *
     * @param array $annotations
     * @param array $parsers
     *
     * @return array
     * @throws AnnotationException
     */
    public function parseAnnotations(array $annotations, array $parsers): array
    {
        $this->parsers     = $parsers;
        $this->annotations = $annotations;

        foreach ($this->annotations as $loadNameSpace => $classes) {
            foreach ($classes as $className => $classOneAnnotations) {
                $this->parseOneClassAnnotations($className, $classOneAnnotations);
            }
        }

        return [$this->definitions, $this->objectDefinitions, $this->classNames, $this->aliases];
    }

    /**
     * Parse class all annotations
     *
     * @param string $className
     * @param array  $classOneAnnotations
     *
     * @throws AnnotationException
     */
    private function parseOneClassAnnotations(string $className, array $classOneAnnotations): void
    {
        // Check class annotation tag
        if (!isset($classOneAnnotations['annotation'])) {
            throw new AnnotationException(
                sprintf('Property or method(%s) with `@xxx` must be define class annotation', $className)
            );
        }

        // Parse class annotations
        $classAnnotations = $classOneAnnotations['annotation'];
        $reflectionClass  = $classOneAnnotations['reflection'];

        $classAry = [
            $className,
            $reflectionClass,
            $classAnnotations
        ];

        $objectDefinition = $this->parseClassAnnotations($classAry);

        // Parse property annotations
        $propertyInjects        = [];
        $propertyAllAnnotations = $classOneAnnotations['properties'] ?? [];
        foreach ($propertyAllAnnotations as $propertyName => $propertyOneAnnotations) {
            $proAnnotations = $propertyOneAnnotations['annotation'] ?? [];
            $propertyInject = $this->parsePropertyAnnotations($classAry, $propertyName, $proAnnotations);
            if ($propertyInject) {
                $propertyInjects[$propertyName] = $propertyInject;
            }
        }

        // Parse method annotations
        $methodInjects        = [];
        $methodAllAnnotations = $classOneAnnotations['methods'] ?? [];
        foreach ($methodAllAnnotations as $methodName => $methodOneAnnotations) {
            $methodAnnotations = $methodOneAnnotations['annotation'] ?? [];

            $methodInject = $this->parseMethodAnnotations($classAry, $methodName, $methodAnnotations);
            if ($methodInject) {
                $methodInjects[$methodName] = $methodInject;
            }
        }

        if (!$objectDefinition) {
            return;
        }

        if (!empty($propertyInjects)) {
            $objectDefinition->setPropertyInjections($propertyInjects);
        }

        if (!empty($methodInjects)) {
            $objectDefinition->setMethodInjections($methodInjects);
        }

        // Object definition and class name
        $name         = $objectDefinition->getName();
        $aliase       = $objectDefinition->getAlias();
        $classNames   = $this->classNames[$className] ?? [];
        $classNames[] = $name;

        $this->classNames[$className]   = array_unique($classNames);
        $this->objectDefinitions[$name] = $objectDefinition;

        if (!empty($aliase)) {
            $this->aliases[$aliase] = $name;
        }
    }

    /**
     * @param array $classAry
     *
     * @return ObjectDefinition|null
     */
    private function parseClassAnnotations(array $classAry): ?ObjectDefinition
    {
        [, , $classAnnotations] = $classAry;

        $objectDefinition = null;
        foreach ($classAnnotations as $annotation) {
            $annotationClass = get_class($annotation);
            if (!isset($this->parsers[$annotationClass])) {
                continue;
            }

            $parserClassName  = $this->parsers[$annotationClass];
            $annotationParser = $this->getAnnotationParser($classAry, $parserClassName);

            $data = $annotationParser->parse(Parser::TYPE_CLASS, $annotation);
            if (empty($data)) {
                continue;
            }

            if (count($data) !== 4) {
                throw new InvalidArgumentException(sprintf('%s annotation parse must be 4 size', $annotationClass));
            }

            [$name, $className, $scope, $alias] = $data;
            $name = empty($name) ? $className : $name;

            if (empty($className)) {
                throw new InvalidArgumentException(sprintf('%s with class name can not be empty', $annotationClass));
            }

            // Multiple coverage
            $objectDefinition = new ObjectDefinition($name, $className, $scope, $alias);
        }

        return $objectDefinition;
    }

    /**
     * @param array  $classAry
     * @param string $propertyName
     * @param array  $propertyAnnotations
     *
     * @return PropertyInjection|null
     */
    private function parsePropertyAnnotations(
        array $classAry,
        string $propertyName,
        array $propertyAnnotations
    ): ?PropertyInjection {

        $propertyInjection = null;
        foreach ($propertyAnnotations as $propertyAnnotation) {
            $annotationClass = get_class($propertyAnnotation);
            if (!isset($this->parsers[$annotationClass])) {
                continue;
            }

            $parserClassName  = $this->parsers[$annotationClass];
            $annotationParser = $this->getAnnotationParser($classAry, $parserClassName);

            $annotationParser->setPropertyName($propertyName);
            $data = $annotationParser->parse(Parser::TYPE_PROPERTY, $propertyAnnotation);

            if (empty($data)) {
                continue;
            }

            if (count($data) !== 2) {
                throw new InvalidArgumentException('Return array with property annotation parse must be 2 size');
            }

            $definitions = $annotationParser->getDefinitions();
            if ($definitions) {
                $this->definitions = $this->mergeDefinitions($this->definitions, $definitions);
            }

            // Multiple coverage
            [$propertyValue, $isRef] = $data;
            $propertyInjection = new PropertyInjection($propertyName, $propertyValue, $isRef);
        }

        return $propertyInjection;
    }

    /**
     * Parse method annotations
     *
     * @param array  $classAry
     * @param string $methodName
     * @param array  $methodAnnotations
     *
     * @return MethodInjection|null
     */
    private function parseMethodAnnotations(
        array $classAry,
        string $methodName,
        array $methodAnnotations
    ): ?MethodInjection {
        $methodInject = null;

        foreach ($methodAnnotations as $methodAnnotation) {
            $annotationClass = get_class($methodAnnotation);
            if (!isset($this->parsers[$annotationClass])) {
                continue;
            }

            $parserClassName  = $this->parsers[$annotationClass];
            $annotationParser = $this->getAnnotationParser($classAry, $parserClassName);

            $annotationParser->setMethodName($methodName);
            $data = $annotationParser->parse(Parser::TYPE_METHOD, $methodAnnotation);

            if (empty($data)) {
                continue;
            }

            $definitions = $annotationParser->getDefinitions();
            if ($definitions) {
                $this->definitions = $this->mergeDefinitions($this->definitions, $definitions);
            }
        }

        return $methodInject;
    }

    /**
     * @param array $definitions
     * @param array $appendDefinitions
     *
     * @return array
     */
    private function mergeDefinitions(array $definitions, array $appendDefinitions): array
    {
        return array_merge($definitions, $appendDefinitions);
    }

    /**
     * Get annotation parser
     *
     * @param array  $classAry
     * @param string $parserClassName
     *
     * @return ParserInterface
     */
    private function getAnnotationParser(array $classAry, string $parserClassName): ParserInterface
    {
        [$className, $reflectionClass, $classAnnotations] = $classAry;

        /* @var ParserInterface $annotationParser */
        $annotationParser = new $parserClassName($className, $reflectionClass, $classAnnotations);

        return $annotationParser;
    }
}
