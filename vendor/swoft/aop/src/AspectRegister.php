<?php declare(strict_types=1);


namespace Swoft\Aop;

/**
 * Class AspectRegister
 *
 * @since 2.0
 */
class AspectRegister
{
    /**
     * @var array
     */
    private static $aspects = [];

    /**
     * Point execution
     */
    public const POINT_EXECUTION = 'execution';

    /**
     * Point bean
     */
    public const POINT_BEAN = 'bean';

    /**
     * Point annotation
     */
    public const POINT_ANNOTATION = 'annotation';

    /**
     * After advice
     */
    public const ADVICE_AFTER = 'after';

    /**
     * Before advice
     */
    public const ADVICE_BEFORE = 'before';

    /**
     * Around advice
     */
    public const ADVICE_AROUND = 'around';

    /**
     * After throwing advice
     */
    public const ADVICE_AFTERTHROWING = 'afterThrowing';

    /**
     * After returning advice
     */
    public const ADVICE_AFTERRETURNING = 'afterReturning';

    /**
     * Register aspect
     *
     * @param string $className
     * @param int    $order
     */
    public static function registerAspect(string $className, int $order): void
    {
        self::$aspects[$className]['order'] = $order;
    }

    /**
     * Register point execution
     *
     * @param string $type
     * @param string $className
     * @param array  $include
     * @param array  $exclude
     */
    public static function registerPoint(string $type, string $className, array $include, array $exclude): void
    {
        if (!isset(self::$aspects[$className])) {
            return;
        }

        self::$aspects[$className]['point'][$type] = [
            'include' => $include,
            'exclude' => $exclude,
        ];
    }

    /**
     * Register advice
     *
     * @param string $type
     * @param string $className
     * @param string $methodName
     */
    public static function registerAdvice(string $type, string $className, string $methodName): void
    {
        if (!isset(self::$aspects[$className])) {
            return;
        }
        self::$aspects[$className]['advice'][$type] = [$className, $methodName];
    }

    /**
     * @return array
     */
    public static function getAspects(): array
    {
        return self::$aspects;
    }
}