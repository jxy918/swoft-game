<?php declare(strict_types=1);


namespace SwoftTest\Validator\Unit;


use Swoft\Validator\ValidateRegister;

/**
 * Class TestCase
 *
 * @since 2.0
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function getValidates(string $className, string $method): array
    {
        return ValidateRegister::getValidates($className, $method);
    }
}