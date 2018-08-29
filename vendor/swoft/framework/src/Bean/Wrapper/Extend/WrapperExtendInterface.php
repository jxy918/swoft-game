<?php

namespace Swoft\Bean\Wrapper\Extend;

/**
 * Wrapper extend
 */
interface WrapperExtendInterface
{
    public function getClassAnnotations(): array;

    public function getPropertyAnnotations(): array;

    public function getMethodAnnotations(): array;

    public function isParseClassAnnotations(array $annotations): bool;

    public function isParsePropertyAnnotations(array $annotations): bool;

    public function isParseMethodAnnotations(array $annotations): bool;
}