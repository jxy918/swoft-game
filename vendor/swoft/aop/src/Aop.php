<?php declare(strict_types=1);

namespace Swoft\Aop;

use function array_column;
use function array_intersect;
use function array_multisort;
use function count;
use function explode;
use function preg_match;
use SwoftTest\Component\Testing\Aop\RegAop;

/**
 * Class AopRegister
 *
 * @since 2.0
 */
class Aop
{
    /**
     * Ignore methods
     */
    public const IGNORE_METHODS = [
        'init'        => 1,
        '__call'      => 1,
        '__get'       => 1,
        '__set'       => 1,
        '__isset'     => 1,
        '__toString'  => 1,
        '__destruct'  => 1,
        '__construct' => 1,
    ];

    /**
     * Class mapping for aspect
     *
     * @var array
     */
    private static $mapping = [];

    /**
     * Register bean aop
     *
     * @param array  $beanNames Bean and alias name
     * @param string $className
     * @param string $method
     * @param array  $methodAnnotations
     */
    public static function register(array $beanNames, string $className, string $method, array $methodAnnotations): void
    {
        $aspects = AspectRegister::getAspects();

        // Sort aspect by order
        $temp = array_column($aspects, 'order');
        array_multisort($temp, SORT_ASC, $aspects);

        foreach ($aspects as $aspectClass => $aspect) {
            if (!isset($aspect['point'], $aspect['advice'])) {
                continue;
            }

            // Include
            $pointBeanInclude       = $aspect['point']['bean']['include'] ?? [];
            $pointAnnotationInclude = $aspect['point']['annotation']['include'] ?? [];
            $pointExecutionInclude  = $aspect['point']['execution']['include'] ?? [];

            // Exclude
            $pointBeanExclude       = $aspect['point']['bean']['exclude'] ?? [];
            $pointAnnotationExclude = $aspect['point']['annotation']['exclude'] ?? [];
            $pointExecutionExclude  = $aspect['point']['execution']['exclude'] ?? [];

            // Is include
            $isIncludeBean       = self::isBeanOrAnnotation($beanNames, $pointBeanInclude);
            $isIncludeAnnotation = self::isBeanOrAnnotation($methodAnnotations, $pointAnnotationInclude);
            $isIncludeExecution  = self::isExecution($className, $method, $pointExecutionInclude);

            // Is exclude
            $isExcludeBean       = self::isBeanOrAnnotation($beanNames, $pointBeanExclude);
            $isExcludeAnnotation = self::isBeanOrAnnotation($methodAnnotations, $pointAnnotationExclude);
            $isExcludeExecution  = self::isExecution($className, $method, $pointExecutionExclude);

            $isInclude = $isIncludeBean || $isIncludeAnnotation || $isIncludeExecution;
            $isExclude = $isExcludeBean || $isExcludeAnnotation || $isExcludeExecution;

            if ($isInclude && !$isExclude) {
                self::$mapping[$className][$method][$aspectClass] = $aspect['advice'];
            }
        }
    }

    /**
     * Get match  aspect
     *
     * @param string $className
     * @param string $method
     *
     * @return array
     */
    public static function match(string $className, string $method): array
    {
        // Ignore methods
        if (isset(self::IGNORE_METHODS[$method])) {
            return [];
        }

        return self::$mapping[$className][$method] ?? [];
    }

    /**
     * Is aop proxy class
     *
     * @param string $className
     *
     * @return bool
     */
    public static function matchClass(string $className): bool
    {
        // Ignore methods
        return isset(self::$mapping[$className]);
    }

    /**
     * Is bean or annotation
     *
     * @param array $pointAry
     * @param array $classAry
     *
     * @return bool
     */
    private static function isBeanOrAnnotation(array $pointAry, array $classAry): bool
    {
        $intersectAry = array_intersect($pointAry, $classAry);

        return $intersectAry ? true : false;
    }

    /**
     * Is execution
     *
     * @param string $class
     * @param string $method
     * @param array  $executions
     *
     * @return bool
     */
    private static function isExecution(string $class, string $method, array $executions): bool
    {
        foreach ($executions as $execution) {
            $executionAry = explode('::', $execution);
            if (count($executionAry) < 2) {
                continue;
            }

            // Class
            [$executionClass, $executionMethod] = $executionAry;

            // Class reg
            $classReg = '/' . $executionClass . '/';

            if ($executionClass !== $class && !preg_match($classReg, $class)) {
                continue;
            }

            // Method
            $reg = '/^(?:' . $executionMethod . ')$/';
            if (preg_match($reg, $method)) {
                return true;
            }
        }

        return false;
    }
}