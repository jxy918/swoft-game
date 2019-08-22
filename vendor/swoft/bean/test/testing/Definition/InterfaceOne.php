<?php declare(strict_types=1);


namespace SwoftTest\Bean\Testing\Definition;


use Swoft\Bean\Annotation\Mapping\Bean;
use SwoftTest\Bean\Testing\Contract\TestInterface;

/**
 * Class InterfaceOne
 *
 * @since 2.0
 *
 * @Bean("interfaceOne")
 */
class InterfaceOne implements TestInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'InterfaceOne';
    }
}