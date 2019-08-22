<?php declare(strict_types=1);


namespace SwoftTest\Breaker\Testing;

use Exception;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Breaker\Annotation\Mapping\Breaker;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoole\Coroutine;

/**
 * Class BreakerBean
 *
 * @since 2.0
 *
 * @Bean()
 */
class BreakerBean
{
    /**
     * @Breaker(fallback="fallMethod")
     *
     * @param string $name
     * @param int    $count
     *
     * @return string
     * @throws Exception
     */
    public function method(string $name, int $count): string
    {
        if ($count < 100) {
            return sprintf('method-%d', $count);
        }

        throw new Exception('Breaker test');
    }

    /**
     * @Breaker(fallback="fallMethod")
     *
     * @param string $name
     * @param int    $count
     *
     * @return string
     * @throws Exception
     */
    public function method2(string $name, int $count): string
    {
        if ($count < 100) {
            Coroutine::sleep(1);
            return sprintf('method-%d', $count);
        }

        throw new Exception('Breaker test');
    }

    /**
     * @Breaker()
     *
     * @param int $count
     *
     * @return string
     */
    public function timeout(int $count): string
    {
        Coroutine::sleep(4);

        return 'timeout';
    }

    /**
     * @Breaker(fallback="timeoutFallback")
     *
     * @param int $count
     *
     * @return string
     */
    public function timeoutFall(int $count): string
    {
        Coroutine::sleep(4);

        return 'timeout';
    }

    /**
     * @param string $name
     * @param int    $count
     *
     * @return string
     */
    public function fallMethod(string $name, int $count): string
    {
        return sprintf('fallback-%s-%d', $name, $count);
    }

    /**
     * @param int $count
     *
     * @return string
     */
    public function timeoutFallback(int $count): string
    {
        return 'timeout-fallback';
    }
}