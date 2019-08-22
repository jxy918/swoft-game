<?php declare(strict_types=1);


namespace SwoftTest\Bean\Testing;

/**
 * Class BeanProperty
 *
 * @since 2.0
 */
abstract class BeanProperty
{
    /**
     * @var string
     */
    private $privateProp = '';

    /**
     * @var int
     */
    public $publicProp = 0;

    /**
     * @return string
     */
    public function getPrivateProp(): string
    {
        return $this->privateProp;
    }

    /**
     * @return int
     */
    public function getPublicProp(): int
    {
        return $this->publicProp;
    }
}