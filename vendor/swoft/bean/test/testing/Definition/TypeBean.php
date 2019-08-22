<?php declare(strict_types=1);


namespace SwoftTest\Bean\Testing\Definition;

use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class TypeBean
 *
 * @since 2.0
 *
 * @Bean("testTypeBean")
 */
class TypeBean
{
    /**
     * @var
     */
    private $stringVar;

    /**
     * @var integer
     */
    private $intVar;

    /**
     * @var int
     */
    private $integerVar;

    /**
     * @var float
     */
    private $floatVar;

    /**
     * @var double
     */
    private $doubleVar;

    /**
     * @var array
     */
    private $arrayVar;

    /**
     * @return mixed
     */
    public function getStringVar()
    {
        return $this->stringVar;
    }

    /**
     * @return mixed
     */
    public function getIntVar()
    {
        return $this->intVar;
    }

    /**
     * @return int
     */
    public function getIntegerVar(): int
    {
        return $this->integerVar;
    }

    /**
     * @return float
     */
    public function getFloatVar(): float
    {
        return $this->floatVar;
    }

    /**
     * @return float
     */
    public function getDoubleVar(): float
    {
        return $this->doubleVar;
    }

    /**
     * @return array
     */
    public function getArrayVar(): array
    {
        return $this->arrayVar;
    }
}