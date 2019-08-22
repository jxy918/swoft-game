<?php declare(strict_types=1);


namespace SwoftTest\Bean\Unit;


use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\BF;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Bean\InterfaceRegister;
use SwoftTest\Bean\Testing\Contract\PrimaryInterface;
use SwoftTest\Bean\Testing\Contract\TestInterface;
use SwoftTest\Bean\Testing\Definition\InterfaceBean;
use SwoftTest\Bean\Testing\Definition\InterfaceBeanDefinition;
use SwoftTest\Bean\Testing\Definition\InterfaceOne;
use SwoftTest\Bean\Testing\Definition\PrimaryInterfaceTwo;

/**
 * Class InterfaceTest
 *
 * @since 2.0
 */
class InterfaceTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testGetName(): void
    {
        /* @var InterfaceBean $testInterface */
        $testInterface = BF::getBean(InterfaceBean::class);

        $name = $testInterface->getName();
        $this->assertContains('Interface', $name);

        $name2 = $testInterface->getName2();
        $this->assertEquals('InterfaceTwo', $name2);

        $name3 = $testInterface->getName3();
        $this->assertEquals('InterfaceOne', $name3);

        $obj = BF::getBean(TestInterface::class);
        $this->assertInstanceOf(InterfaceOne::class, $obj);

        $beanName = InterfaceRegister::getInterfaceInjectBean(TestInterface::class);
        $this->assertEquals($beanName, 'interfaceOne');

        $obj = BF::getSingleton(TestInterface::class);
        $this->assertInstanceOf(InterfaceOne::class, $obj);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testGetPname(): void
    {
        /* @var InterfaceBean $testInterface */
        $testInterface = BeanFactory::getBean(InterfaceBean::class);

        $name = $testInterface->getPname();
        $this->assertContains('Primary', $name);

        $name3 = $testInterface->getPname2();
        $this->assertEquals('PrimaryInterfaceThree', $name3);

        $obj = BF::getBean(PrimaryInterface::class);
        $this->assertInstanceOf(PrimaryInterface::class, $obj);

        $beanName = InterfaceRegister::getInterfaceInjectBean(PrimaryInterface::class);
        $this->assertEquals($beanName, PrimaryInterfaceTwo::class);

        $obj = BF::getSingleton(PrimaryInterfaceTwo::class);
        $this->assertInstanceOf(PrimaryInterfaceTwo::class, $obj);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testGetPnameDefinition(): void
    {
        /* @var InterfaceBeanDefinition $testInterface */
        $testInterface = BF::getBean(InterfaceBeanDefinition::class);

        $name = $testInterface->getName();
        $this->assertEquals('PrimaryInterfaceTwo', $name);

        $obj = BF::getBean(PrimaryInterface::class);
        $this->assertInstanceOf(PrimaryInterface::class, $obj);

        $beanName = InterfaceRegister::getInterfaceInjectBean(PrimaryInterface::class);
        $this->assertEquals($beanName, PrimaryInterfaceTwo::class);

        $obj = BF::getSingleton(PrimaryInterfaceTwo::class);
        $this->assertInstanceOf(PrimaryInterfaceTwo::class, $obj);
    }
}