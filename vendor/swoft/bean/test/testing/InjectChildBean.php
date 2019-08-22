<?php declare(strict_types=1);


namespace SwoftTest\Bean\Testing;

use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class InjectChildBean
 *
 * @since 2.0
 *
 * @Bean()
 */
class InjectChildBean
{
    /**
     * @return string
     */
    public function getData(): string
    {
        return 'InjectChildBeanData';
    }
}