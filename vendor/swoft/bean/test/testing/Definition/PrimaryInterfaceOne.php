<?php declare(strict_types=1);


namespace SwoftTest\Bean\Testing\Definition;

use Swoft\Bean\Annotation\Mapping\Bean;
use SwoftTest\Bean\Testing\Contract\PrimaryInterface;

/**
 * Class PrimaryInterfaceOne
 *
 * @since 2.0
 *
 * @Bean()
 */
class PrimaryInterfaceOne implements PrimaryInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PrimaryInterfaceOne';
    }
}