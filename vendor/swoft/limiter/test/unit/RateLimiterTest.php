<?php declare(strict_types=1);


namespace SwoftTest\Limiter\Unit;


use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Limiter\Exception\RateLImiterException;
use SwoftTest\Db\Unit\TestCase;
use SwoftTest\Limiter\Testing\KeyHelper;
use SwoftTest\Limiter\Testing\RateLimiterBean;
use Swoole\Coroutine;

class RateLimiterTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testLimitByCount()
    {
        /* @var RateLimiterBean $rateLimiterBean */
        $rateLimiterBean = BeanFactory::getBean(RateLimiterBean::class);
        $result          = $rateLimiterBean->limitByCount();
        $this->assertEquals('limitByCount', $result);
        $result = $rateLimiterBean->limitByCount();
        $this->assertEquals('limitByCount', $result);
        $result = $rateLimiterBean->limitByCount();
        $this->assertEquals('limitByCount', $result);

        try {
            $rateLimiterBean->limitByCount();
        } catch (\Exception $e) {
            $this->assertInstanceOf(RateLImiterException::class, $e);
        }

        Coroutine::sleep(1);

        $result = $rateLimiterBean->limitByCount();
        $this->assertEquals('limitByCount', $result);
        $result = $rateLimiterBean->limitByCount();
        $this->assertEquals('limitByCount', $result);
        $result = $rateLimiterBean->limitByCount();
        $this->assertEquals('limitByCount', $result);

        try {
            $rateLimiterBean->limitByCount();
        } catch (\Exception $e) {
            $this->assertInstanceOf(RateLImiterException::class, $e);
        }
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function testLimitByEl()
    {
        /* @var RateLimiterBean $rateLimiterBean */
        $rateLimiterBean = BeanFactory::getBean(RateLimiterBean::class);

        $result = $rateLimiterBean->limitByEl('swoft', 1);
        $this->assertEquals($result, 'swoft-1');
        $result = $rateLimiterBean->limitByEl('swoft', 2);
        $this->assertEquals($result, 'swoft-2');

        Coroutine::sleep(2);
        $result = $rateLimiterBean->limitByEl('swoft', 2);
        $this->assertEquals($result, 'swoft-2');

        try {
            $rateLimiterBean->limitByEl('swoft', 2);
        } catch (\Exception $e) {
            $this->assertInstanceOf(RateLImiterException::class, $e);
        }
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function testLimitFall()
    {
        /* @var RateLimiterBean $rateLimiterBean */
        $rateLimiterBean = BeanFactory::getBean(RateLimiterBean::class);

        $result = $rateLimiterBean->limitByFall();
        $this->assertEquals($result, 'limitByFall');

        $result = $rateLimiterBean->limitByFall();
        $this->assertEquals($result, 'limitByFallback');

        $result = $rateLimiterBean->limitByFall();
        $this->assertEquals($result, 'limitByFallback');
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function testLimtInnerVars()
    {
        /* @var RateLimiterBean $rateLimiterBean */
        $rateLimiterBean = BeanFactory::getBean(RateLimiterBean::class);

        $result = $rateLimiterBean->limitInnerVars();
        $this->assertEquals($result, 'limitInnerVars');
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function testLimitByElObj()
    {
        /* @var RateLimiterBean $rateLimiterBean */
        $rateLimiterBean = BeanFactory::getBean(RateLimiterBean::class);

        $result = $rateLimiterBean->limitByElObj(new KeyHelper(), 66, 'obj');
        $this->assertEquals('limitByElObj-66', $result);
    }
}