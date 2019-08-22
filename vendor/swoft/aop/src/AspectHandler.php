<?php declare(strict_types=1);


namespace Swoft\Aop;

use ReflectionException;
use ReflectionType;
use Swoft\Aop\Concern\AopTrait;
use Swoft\Aop\Point\JoinPoint;
use Swoft\Aop\Point\ProceedingJoinPoint;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Stdlib\Reflections;
use Throwable;
use function array_shift;

/**
 * Class Handler
 *
 * @Bean(scope=Bean::PROTOTYPE, alias="aspectHandler")
 * @since 2.0
 */
class AspectHandler
{
    /**
     * Method target
     *
     * @var AopTrait
     */
    private $target;

    /**
     * @var string
     */
    private $className = '';

    /**
     * Method name
     *
     * @var string
     */
    private $methodName = '';

    /**
     * Method args
     *
     * @var array
     */
    private $args = [];

    /**
     * @var array
     */
    private $argsMap = [];

    /**
     * All aspect to do
     *
     * @var array
     */
    private $aspects = [];

    /**
     * Current aspect
     *
     * @var array
     */
    private $aspect;

    /**
     * @var Throwable
     */
    private $throwable;

    /**
     * Invoke aspect
     *
     * @return mixed
     * @throws ReflectionException
     * @throws ContainerException
     * @throws Throwable
     */
    public function invokeAspect()
    {
        $around = $this->aspect['around'] ?? [];
        $after  = $this->aspect['after'] ?? [];
        $afRetn = $this->aspect['afterReturning'] ?? [];
        $afThw  = $this->aspect['afterThrowing'] ?? [];


        $result = null;
        try {
            if (!empty($around)) {
                // Invoke around advice
                $result = $this->invokeAdvice($around);
            } else {
                // Invoke target and before advice
                $result = $this->invokeTarget();
            }

        } catch (Throwable $e) {
            $this->throwable = $e;
        }

        // Invoke after advice
        if (!empty($after)) {
            $this->invokeAdvice($after);
        }

        // Invoke afterThrowing Or afterReturn
        if (!empty($this->throwable)) {
            if (!empty($afThw)) {
                // Invoke afterThrowing advice
                return $this->invokeAdvice($afThw, $this->throwable);
            }
            throw $this->throwable;
        } else {
            // Invoke afterReturning advice
            if (!empty($afRetn)) {
                $result = $this->invokeAdvice($afRetn, null, $result);
            }
        }

        return $result;
    }

    /**
     * Invoke target and before advice
     *
     * @param array $params
     *
     * @return mixed
     * @throws Throwable
     */
    public function invokeTarget(array $params = [])
    {
        $before = $this->aspect['before'] ?? [];

        // Invoke before advice
        if (!empty($before)) {
            $this->invokeAdvice($before);
        }

        // Invoke next aspect
        if (!empty($this->aspects)) {
            $nextAspect      = $this->nextHandler();
            $result          = $nextAspect->invokeAspect();
            $this->throwable = $nextAspect->throwable;

            return $result;
        }

        $args = empty($params) ? $this->args : $params;

        // Invoke target
        return $this->target->__invokeTarget($this->methodName, $args);
    }

    /**
     * @param AopTrait $target
     */
    public function setTarget($target): void
    {
        $this->target = $target;
    }

    /**
     * @param string $methodName
     */
    public function setMethodName(string $methodName): void
    {
        $this->methodName = $methodName;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    /**
     * @param array $aspects
     */
    public function setAspects(array $aspects): void
    {
        $this->aspect  = array_shift($aspects);
        $this->aspects = $aspects;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    /**
     * @param array $argsMap
     */
    public function setArgsMap(array $argsMap): void
    {
        $this->argsMap = $argsMap;
    }

    /**
     * Invoke advice
     *
     * @param array     $aspectAry
     * @param Throwable $catch
     * @param mixed     $return
     *
     * @return mixed
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function invokeAdvice(array $aspectAry, Throwable $catch = null, $return = null)
    {
        [$aspectClass, $aspectMethod] = $aspectAry;

        // Reflection data from cache
        $rftAry = Reflections::get($aspectClass);
        $params = $rftAry['methods'][$aspectMethod]['params'] ?? [];

        $aspectArgs = [];
        foreach ($params as $param) {
            /* @var ReflectionType $reflectType */
            // [, $reflectType] = $param;
            $reflectType = $param[1];
            if ($reflectType === null) {
                $aspectArgs[] = null;
                continue;
            }

            // JoinPoint object
            $type = $reflectType->getName();
            if ($type === JoinPoint::class) {
                $aspectArgs[] = $this->getJoinPoint($catch, $return);
                continue;
            }

            // ProceedingJoinPoint object
            if ($type === ProceedingJoinPoint::class) {
                $aspectArgs[] = $this->getProceedingJoinPoint($catch, $return);
                continue;
            }

            if ($type == Throwable::class) {
                $aspectArgs[] = $catch;
            }

            $aspectArgs[] = null;
        }

        $aspect = \bean($aspectClass);
        return $aspect->$aspectMethod(...$aspectArgs);
    }

    /**
     * New proceeding join point
     *
     * @param Throwable|null $catch
     * @param mixed          $return
     *
     * @return ProceedingJoinPoint
     */
    private function getProceedingJoinPoint(Throwable $catch = null, $return = null): ProceedingJoinPoint
    {
        $pgp = new ProceedingJoinPoint($this->className, $this->target, $this->methodName, $this->args, $this->argsMap);
        $pgp->setHandler($this);

        if ($catch) {
            $pgp->setCatch($catch);
        }

        if ($return) {
            $pgp->setReturn($return);
        }

        return $pgp;
    }

    /**
     * New join point
     *
     * @param Throwable|null $catch
     * @param mixed          $return
     *
     * @return JoinPoint
     */
    private function getJoinPoint(Throwable $catch = null, $return = null): JoinPoint
    {
        $joinPoint = new JoinPoint($this->className, $this->target, $this->methodName, $this->args, $this->argsMap);
        if ($catch) {
            $joinPoint->setCatch($catch);
        }

        if ($return) {
            $joinPoint->setReturn($return);
        }

        return $joinPoint;
    }

    /**
     * Next aspect handler
     *
     * @return AspectHandler
     */
    private function nextHandler(): AspectHandler
    {
        $aspect = clone  $this;

        // Next aspect data
        $aspect->aspect  = array_shift($this->aspects);
        $aspect->aspects = $this->aspects;

        return $aspect;
    }
}