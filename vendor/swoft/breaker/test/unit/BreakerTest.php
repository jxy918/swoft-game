<?php declare(strict_types=1);


namespace SwoftTest\Breaker\Unit;


use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Breaker\Breaker;
use Swoft\Breaker\BreakerManager;
use Swoft\Breaker\Exception\BreakerException;
use SwoftTest\Breaker\Testing\BreakerBean;
use Swoole\Coroutine;

/**
 * Class BreakerTest
 *
 * @since 2.0
 */
class BreakerTest extends TestCase
{
    /**
     * @throws BreakerException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws Exception
     */
    public function testBreaker()
    {
        $breaker = $this->getBreaker(BreakerBean::class, 'method');

        $fallback = $this->getBreakerBean()->method('swoft', 100);
        $this->assertTrue($breaker->isClose());

        $this->getBreakerBean()->method('swoft', 100);
        $this->getBreakerBean()->method('swoft', 100);

        $this->assertTrue($breaker->isOpen());

        $result = $this->getBreakerBean()->method('swoft', 1);
        $this->assertEquals('fallback-swoft-1', $result);
        $this->assertTrue($breaker->isOpen());

        // Sleep
        Coroutine::sleep($breaker->getRetryTime());

        $this->assertTrue($breaker->isHalfOpen());

        $result = $this->getBreakerBean()->method('swoft', 1);
        $this->assertEquals('method-1', $result);

        $this->assertTrue($breaker->isHalfOpen());

        $result = $this->getBreakerBean()->method('swoft', 2);
        $this->assertEquals('method-2', $result);
        $this->assertTrue($breaker->isHalfOpen());

        $result = $this->getBreakerBean()->method('swoft', 3);
        $this->assertEquals('method-3', $result);
        $this->assertTrue($breaker->isClose());


        $this->assertEquals($fallback, 'fallback-swoft-100');
    }

    public function testOutOfHalfOpen()
    {
        $breaker = $this->getBreaker(BreakerBean::class, 'method2');

        $fallback = $this->getBreakerBean()->method2('swoft', 100);
        $this->assertTrue($breaker->isClose());

        $this->getBreakerBean()->method2('swoft', 100);
        $this->getBreakerBean()->method2('swoft', 100);

        $this->assertTrue($breaker->isOpen());

        $result = $this->getBreakerBean()->method2('swoft', 1);
        $this->assertEquals('fallback-swoft-1', $result);
        $this->assertTrue($breaker->isOpen());

        // Sleep
        Coroutine::sleep($breaker->getRetryTime()+1);
        $this->getBreakerBean()->method2('swoft', 1);
        $this->getBreakerBean()->method2('swoft', 1);
        $this->getBreakerBean()->method2('swoft', 1);
        $this->getBreakerBean()->method2('swoft', 1);

        $this->assertTrue($breaker->isClose());
    }

    /**
     * @expectedException Swoft\Breaker\Exception\BreakerException
     *
     * @throws BreakerException
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function testOutTimeout()
    {
        $this->getBreaker(BreakerBean::class, 'timeout');

        $this->getBreakerBean()->timeout(100);
    }

    /**
     * @throws BreakerException
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function testTimeoutFallback()
    {
        $this->getBreaker(BreakerBean::class, 'timeoutFall');

        $result = $this->getBreakerBean()->timeoutFall(100);
        $this->assertEquals('timeout-fallback', $result);
    }

    /**
     * @return BreakerBean
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function getBreakerBean(): BreakerBean
    {
        return bean(BreakerBean::class);
    }

    /**
     * @param string $className
     * @param string $method
     *
     * @return Breaker
     * @throws ContainerException
     * @throws ReflectionException
     * @throws BreakerException
     */
    private function getBreaker(string $className, string $method): Breaker
    {
        return $this->getBreakerManager()->getBreaker($className, $method);
    }

    /**
     * @return BreakerManager
     * @throws ContainerException
     * @throws ReflectionException
     */
    private function getBreakerManager(): BreakerManager
    {
        return bean(BreakerManager::class);
    }
}