<?php declare(strict_types=1);


namespace SwoftTest\Aop\Testing;


class AopClass
{
    use AopClassTrait;

    public function Method(int $int, string $str, float $float, AopClass $aopClass): int
    {
        return 0;
    }

    public function MethodSelf(int $int, string $str, float $float, AopClass $aopClass): self
    {
        return self;
    }

    public function MethodVoid(int $int, string $str, float $float, AopClass $aopClass): void
    {
    }

    public function MethodNull($int, $str, $float, $aopClass)
    {
    }
}