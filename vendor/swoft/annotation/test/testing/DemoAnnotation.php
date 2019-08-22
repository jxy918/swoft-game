<?php declare(strict_types=1);


namespace SwoftTest\Annotation\Testing;


use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoClass;
use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoMethod;
use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoProperty;

/**
 * Class DemoAnnotation
 *
 * @since 2.0
 *
 * @DemoClass(name="demoAnnotation")
 */
class DemoAnnotation
{
    /**
     * @DemoProperty(name="prop")
     *
     * @var string
     */
    private $prop = '';

    /**
     * @DemoProperty("defaultProp")
     *
     * @var string
     */
    private $defaultProp = '';

    /**
     * @DemoMethod(name="method")
     */
    public function method(): void
    {

    }

    /**
     * @DemoMethod("defaultMethod")
     */
    public function defaultMethod(): void
    {

    }
}