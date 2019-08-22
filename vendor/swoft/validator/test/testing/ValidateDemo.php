<?php declare(strict_types=1);


namespace SwoftTest\Validator\Testing;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\Validate;
use SwoftTest\Validator\Testing\Validator\TestValidator;
use Swoft\Validator\Annotation\Mapping\ValidateType;

/**
 * Class ValidateDemo
 *
 * @since 2.0
 *
 * @Bean()
 */
class ValidateDemo
{
    /**
     * @Validate(validator=TestValidator::class, fields={"array"})
     *
     * @return bool
     */
    public function testArray(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"int"})
     *
     * @return bool
     */
    public function testInt(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"float"})
     *
     * @return bool
     */
    public function testFloat(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"bool"})
     *
     * @return bool
     */
    public function testBool(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"string"})
     *
     * @return bool
     */
    public function testString(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"arrayMessage"})
     *
     * @return bool
     */
    public function testArrayMessage(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"intMessage"})
     *
     * @return bool
     */
    public function testIntMessage(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"floatMessage"})
     *
     * @return bool
     */
    public function testFloatMessage(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"boolMessage"})
     *
     * @return bool
     */
    public function testBoolMessage(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"stringMessage"})
     *
     * @return bool
     */
    public function testStringMessage(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"boolDefault","intDefault","floatDefault", "arrayDefault", "stringDefault"})
     *
     * @return bool
     */
    public function testTypeDefault(): bool
    {
        return false;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"name"})
     */
    public function testName()
    {

    }

    /**
     * @Validate(validator=TestValidator::class, fields={"int"}, type=ValidateType::GET)
     *
     * @return bool
     */
    public function testIntQuery(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"float"}, type=ValidateType::GET)
     *
     * @return bool
     */
    public function testFloatQuery(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"bool"}, type=ValidateType::GET)
     *
     * @return bool
     */
    public function testBoolQuery(): bool
    {
        return true;
    }

    /**
     * @Validate(validator=TestValidator::class, fields={"string"}, type=ValidateType::GET)
     *
     * @return bool
     */
    public function testStringQuery(): bool
    {
        return true;
    }
}