<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Aop;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Collector\AspectCollector;
use \Throwable;

/**
 * @Bean()
 */
class Aop implements AopInterface
{
    /**
     * @var array
     */
    private $map = [];

    /**
     * @var array
     */
    private $aspects = [];

    /**
     * @return void
     */
    public function init()
    {
        // Register aspects by aspect annotation collector
        $this->register(AspectCollector::getCollector());
    }

    /**
     * Execute origin method by aop
     *
     * @param object $target Origin object
     * @param string $method The execution method
     * @param array $params The parameters of execution method
     * @return mixed
     * @throws \ReflectionException
     * @throws Throwable
     */
    public function execute($target, string $method, array $params)
    {
        $class = \get_class($target);

        // If doesn't have any advices, then execute the origin method
        if (!isset($this->map[$class][$method]) || empty($this->map[$class][$method])) {
            return $target->$method(...$params);
        }

        // Apply advices's functionality
        $advices = $this->map[$class][$method];
        return $this->doAdvice($target, $method, $params, $advices);
    }

    /**
     * @param object $target  Origin object
     * @param string $method  The execution method
     * @param array  $params  The parameters of execution method
     * @param array  $advices The advices of this object method
     * @return mixed
     * @throws \ReflectionException|Throwable
     */
    public function doAdvice($target, string $method, array $params, array $advices)
    {
        $result = null;
        $advice = \array_shift($advices);

        try {

            // Around
            if (isset($advice['around']) && ! empty($advice['around'])) {
                $result = $this->doPoint($advice['around'], $target, $method, $params, $advice, $advices);
            } else {
                // Before
                if ($advice['before'] && ! empty($advice['before'])) {
                    // The result of before point will not effect origin object method
                    $this->doPoint($advice['before'], $target, $method, $params, $advice, $advices);
                }
                if (0 === \count($advices)) {
                    $result = $target->$method(...$params);
                } else {
                    $this->doAdvice($target, $method, $params, $advices);
                }
            }

            // After
            if (isset($advice['after']) && ! empty($advice['after'])) {
                $this->doPoint($advice['after'], $target, $method, $params, $advice, $advices, $result);
            }
        } catch (Throwable $t) {
            if (isset($advice['afterThrowing']) && ! empty($advice['afterThrowing'])) {
                return $this->doPoint($advice['afterThrowing'], $target, $method, $params, $advice, $advices, null, $t);
            }

            throw $t;
        }

        // afterReturning
        if (isset($advice['afterReturning']) && ! empty($advice['afterReturning'])) {
            return $this->doPoint($advice['afterReturning'], $target, $method, $params, $advice, $advices, $result);
        }

        return $result;
    }

    /**
     * Do pointcut
     *
     * @param array  $pointAdvice the pointcut advice
     * @param object $target      Origin object
     * @param string $method      The execution method
     * @param array  $args        The parameters of execution method
     * @param array  $advice      the advice of pointcut
     * @param array  $advices     The advices of this object method
     * @param mixed  $return
     * @param Throwable $catch    The  Throwable object caught
     * @return mixed
     * @throws \ReflectionException
     */
    private function doPoint(
        array $pointAdvice,
        $target,
        string $method,
        array $args,
        array $advice,
        array $advices,
        $return = null,
        Throwable $catch = null
    ) {
        list($aspectClass, $aspectMethod) = $pointAdvice;

        $reflectionClass = new \ReflectionClass($aspectClass);
        $reflectionMethod = $reflectionClass->getMethod($aspectMethod);
        $reflectionParameters = $reflectionMethod->getParameters();

        // Bind the param of method
        $aspectArgs = [];
        foreach ($reflectionParameters as $reflectionParameter) {
            $parameterType = $reflectionParameter->getType();
            if ($parameterType === null) {
                $aspectArgs[] = null;
                continue;
            }

            // JoinPoint object
            $type = $parameterType->__toString();
            if ($type === JoinPoint::class) {
                $aspectArgs[] = new JoinPoint($target, $method, $args, $return, $catch);
                continue;
            }

            // ProceedingJoinPoint object
            if ($type === ProceedingJoinPoint::class) {
                $aspectArgs[] = new ProceedingJoinPoint($target, $method, $args, $advice, $advices);
                continue;
            }

            // Throwable object
            if ($catch && $catch instanceof $type) {
                $aspectArgs[] = $catch;
                continue;
            }
            $aspectArgs[] = null;
        }

        $aspect = \bean($aspectClass);

        return $aspect->$aspectMethod(...$aspectArgs);
    }

    /**
     * Match aop
     *
     * @param string $beanName    Bean name
     * @param string $class       Class name
     * @param string $method      Method name
     * @param array  $annotations The annotations of method
     */
    public function match(string $beanName, string $class, string $method, array $annotations)
    {
        foreach ($this->aspects as $aspectClass => $aspect) {
            if (!isset($aspect['point'], $aspect['advice'])) {
                continue;
            }

            // Include
            $pointBeanInclude = $aspect['point']['bean']['include'] ?? [];
            $pointAnnotationInclude = $aspect['point']['annotation']['include'] ?? [];
            $pointExecutionInclude = $aspect['point']['execution']['include'] ?? [];

            // Exclude
            $pointBeanExclude = $aspect['point']['bean']['exclude'] ?? [];
            $pointAnnotationExclude = $aspect['point']['annotation']['exclude'] ?? [];
            $pointExecutionExclude = $aspect['point']['execution']['exclude'] ?? [];

            $includeMath = $this->matchBeanAndAnnotation([$beanName], $pointBeanInclude) || $this->matchBeanAndAnnotation($annotations, $pointAnnotationInclude) || $this->matchExecution($class, $method, $pointExecutionInclude);

            $excludeMath = $this->matchBeanAndAnnotation([$beanName], $pointBeanExclude) || $this->matchBeanAndAnnotation($annotations, $pointAnnotationExclude) || $this->matchExecution($class, $method, $pointExecutionExclude);

            if ($includeMath && ! $excludeMath) {
                $this->map[$class][$method][] = $aspect['advice'];
            }
        }
    }

    /**
     * Register aspects
     *
     * @param array $aspects
     */
    public function register(array $aspects)
    {
        $temp = \array_column($aspects, 'order');
        \array_multisort($temp, SORT_ASC, $aspects);
        $this->aspects = $aspects;
    }

    /**
     * Match bean and annotation
     *
     * @param array $pointAry
     * @param array $classAry
     * @return bool
     */
    private function matchBeanAndAnnotation(array $pointAry, array $classAry): bool
    {
        $intersectAry = \array_intersect($pointAry, $classAry);
        if (empty($intersectAry)) {
            return false;
        }

        return true;
    }

    /**
     * Match execution
     *
     * @param string $class
     * @param string $method
     * @param array  $executions
     * @return bool
     */
    private function matchExecution(string $class, string $method, array $executions): bool
    {
        foreach ($executions as $execution) {
            $executionAry = \explode('::', $execution);
            if (\count($executionAry) < 2) {
                continue;
            }

            // Class
            list($executionClass, $executionMethod) = $executionAry;
            if ($executionClass !== $class) {
                continue;
            }

            // Method
            $reg = '/^(?:' . $executionMethod . ')$/';
            if (\preg_match($reg, $method)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAspects(): array
    {
        return $this->aspects;
    }

    /**
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }
}
