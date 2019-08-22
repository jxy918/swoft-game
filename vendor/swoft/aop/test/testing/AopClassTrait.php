<?php declare(strict_types=1);


namespace SwoftTest\Aop\Testing;


trait AopClassTrait
{
    public function traitMethod(int $int, string $str, float $float, AopClass $aopClass): int
    {
        return 0;
    }

    public function traitMethodSelf(int $int, string $str, float $float, AopClass $aopClass): self
    {
        return self;
    }

    public function traitMethodVoid(int $int, $str, $float, $aopClass): void
    {
    }

    public function traitMethodNull($int, $str, float $float, AopClass $aopClass)
    {
    }
}