<?php declare(strict_types=1);


namespace SwoftTest\Bean\Testing\Definition;


class InterfaceBeanDefinition
{
    /**
     * @var PrimaryInterfaceThree
     */
    private $pinterface;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->pinterface->getName();
    }
}