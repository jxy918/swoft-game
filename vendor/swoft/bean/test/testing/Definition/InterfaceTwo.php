<?php declare(strict_types=1);


namespace SwoftTest\Bean\Testing\Definition;


use Swoft\Bean\Annotation\Mapping\Bean;
use SwoftTest\Bean\Testing\Contract\TestInterface;

/**
 * Class InterfaceTwo
 *
 * @since 2.0
 *
 * @Bean("interfaceTwo")
 */
class InterfaceTwo implements TestInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'InterfaceTwo';
    }
}