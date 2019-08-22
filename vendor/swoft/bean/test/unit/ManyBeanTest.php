<?php declare(strict_types=1);


namespace SwoftTest\Bean\Unit;


use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Container;
use Swoft\Bean\Exception\ContainerException;
use SwoftTest\Bean\Testing\Definition\CommaNameClass;
use SwoftTest\Bean\Testing\Definition\ManyInstance;

/**
 * Class ManyBeanTest
 *
 * @since 2.0
 */
class ManyBeanTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testMany()
    {
        $beans = Container::getInstance()->gets(ManyInstance::class);

        $this->assertEquals(3, count($beans));

        foreach ($beans as $bean) {
            $this->assertTrue($bean instanceof ManyInstance);
        }

        $beans = BeanFactory::getBeans(ManyInstance::class);
        $this->assertEquals(3, count($beans));

        foreach ($beans as $bean) {
            $this->assertTrue($bean instanceof ManyInstance);
        }

        /* @var CommaNameClass $comma*/
        $comma = BeanFactory::getBean(CommaNameClass::class);

        $two = $comma->getManyInstance2();

        $this->assertTrue($two instanceof ManyInstance);
    }
}