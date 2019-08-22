<?php
declare(strict_types=1);

namespace SwoftTest\Validator\Testing;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\Validate;

/**
 * Class ValidatorRule
 *
 * @since 2.0
 *
 * @Bean()
 */
class ValidatorRule
{
    /**
     * @Validate(validator="testRule",fields={"dataAfterDate"})
     *
     * @return bool
     */
    public function testAfterDate(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataAlpha"})
     *
     * @return bool
     */
    public function testAlpha(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataAlphaDash"})
     *
     * @return bool
     */
    public function testAlphaDash(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataAlphaNum"})
     *
     * @return bool
     */
    public function testAlphaNum(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataBeforeDate"})
     *
     * @return bool
     */
    public function testBeforeDate(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataChs"})
     *
     * @return bool
     */
    public function testChs(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataChsAlpha"})
     *
     * @return bool
     */
    public function testChsAlpha(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataChsAlphaDash"})
     *
     * @return bool
     */
    public function testChsAlphaDash(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataChsAlphaNum"})
     *
     * @return bool
     */
    public function testChsAlphaNum(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataConfirm"})
     *
     * @return bool
     */
    public function testConfirm(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataDifferent"})
     *
     * @return bool
     */
    public function testDifferent(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataGreaterThan"})
     *
     * @return bool
     */
    public function testGreaterThan(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataLessThan"})
     *
     * @return bool
     */
    public function testLessThan(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataDate"})
     *
     * @return bool
     */
    public function testDate(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataDateRange"})
     *
     * @return bool
     */
    public function testDateRange(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataDns"})
     *
     * @return bool
     */
    public function testDns(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataFileMediaType"})
     *
     * @return bool
     */
    public function testFileMediaType(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataFileSize"})
     *
     * @return bool
     */
    public function testFileSize(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataFileSuffix"})
     *
     * @return bool
     */
    public function testFileSuffix(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataIsFile"})
     *
     * @return bool
     */
    public function testIsFile(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataLow"})
     *
     * @return bool
     */
    public function testLow(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataNotInEnum"})
     *
     * @return bool
     */
    public function testNotInEnum(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataNotInRange"})
     *
     * @return bool
     */
    public function testNotInRange(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataUpper"})
     *
     * @return bool
     */
    public function testUpper(): bool
    {
        return true;
    }

    /**
     * @Validate(validator="testRule",fields={"dataUrl"})
     *
     * @return bool
     */
    public function testUrl(): bool
    {
        return true;
    }
}
