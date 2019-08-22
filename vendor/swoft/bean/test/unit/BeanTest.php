<?php declare(strict_types=1);


namespace SwoftTest\Bean\Unit;


use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use SwoftTest\Bean\Testing\Definition\TypeBean;

/**
 * Class BeanTest
 *
 * @since 2.0
 */
class BeanTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testType()
    {
        /* @var TypeBean $typeBean */
        $typeBean = BeanFactory::getBean('testTypeBean');

        $this->assertEquals($typeBean->getStringVar(), '1');
        $this->assertEquals($typeBean->getIntVar(), 1);
        $this->assertEquals($typeBean->getIntegerVar(), 2);
        $this->assertEquals($typeBean->getFloatVar(), 1.1);
        $this->assertEquals($typeBean->getDoubleVar(), 1.2);

        /* @var TypeBean $typeBean */
        $typeBean = BeanFactory::getBean(TypeBean::class);

        $this->assertEquals($typeBean->getStringVar(), '1');
        $this->assertEquals($typeBean->getIntVar(), 1);
        $this->assertEquals($typeBean->getIntegerVar(), 2);
        $this->assertEquals($typeBean->getFloatVar(), 1.1);
        $this->assertEquals($typeBean->getDoubleVar(), 1.2);
    }
}
