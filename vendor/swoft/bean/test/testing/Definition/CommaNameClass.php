<?php declare(strict_types=1);


namespace SwoftTest\Bean\Testing\Definition;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;

/**
 * Class CommaNameClass
 *
 * @since 2.0
 *
 * @Bean("commaNameClass")
 */
class CommaNameClass
{
    /**
     * @var ManyInstance
     */
    private $manyInstance2;

    /**
     * @return ManyInstance
     */
    public function getManyInstance2(): ManyInstance
    {
        return $this->manyInstance2;
    }
}