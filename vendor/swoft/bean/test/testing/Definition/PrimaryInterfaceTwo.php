<?php declare(strict_types=1);


namespace SwoftTest\Bean\Testing\Definition;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Primary;
use SwoftTest\Bean\Testing\Contract\PrimaryInterface;

/**
 * Class PrimaryInterfaceTwo
 *
 * @since 2.0
 *
 * @Bean()
 * @Primary()
 */
class PrimaryInterfaceTwo implements PrimaryInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PrimaryInterfaceTwo';
    }
}