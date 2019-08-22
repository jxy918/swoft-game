<?php declare(strict_types=1);

namespace SwoftTest\Validator\Unit;

use Swoft\Bean\Exception\ContainerException;
use Swoft\Validator\Exception\ValidatorException;
use Swoft\Validator\Validator;
use SwoftTest\Validator\Testing\ValidatorRule;

class ValidatorRuleTest extends TestCase
{
    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage dataAfterDate must be after 2019-07-08
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testAfterDateError()
    {
        $data = [
            'dataAfterDate' => '2019-07-06'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testAfterDate'));
    }

    public function testAfterDateSuccess()
    {
        $data = [
            'dataAfterDate' => '2019-07-09 00:00:00'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testAfterDate'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage alpha message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testAlphaError()
    {
        $data = [
            'dataAlpha' => 'abcde0123'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testAlpha'));
    }

    public function testAlphaSuccess()
    {
        $data = [
            'dataAlpha' => 'abcd'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testAlpha'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage alphadash message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testAlphaDashError()
    {
        $data = [
            'dataAlphaDash' => '.='
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testAlphaDash'));
    }

    public function testAlphaDashSuccess()
    {
        $data = [
            'dataAlphaDash' => 'abcd0123-_'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testAlphaDash'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage alphanum message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testAlphaNumError()
    {
        $data = [
            'dataAlphaNum' => 'abcde-'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testAlphaNum'));
    }

    public function testAlphaNumSuccess()
    {
        $data = [
            'dataAlphaNum' => 'abcd012'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testAlphaNum'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage before date message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testBeforeDateError()
    {
        $data = [
            'dataBeforeDate' => '2019-07-10'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testBeforeDate'));
    }

    public function testBeforeDateSuccess()
    {
        $data = [
            'dataBeforeDate' => '2019-07-01 00:00:00'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testBeforeDate'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage chs message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testChsError()
    {
        $data = [
            'dataChs' => 'english'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testChs'));
    }

    public function testChsSuccess()
    {
        $data = [
            'dataChs' => '中文'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testChs'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage chsalpha message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testChsAlphaError()
    {
        $data = [
            'dataChsAlpha' => '-_'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testChsAlpha'));
    }

    public function testChsAlphaSuccess()
    {
        $data = [
            'dataChsAlpha' => '中文english'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testChsAlpha'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage chsalphadash message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testChsAlphaDashError()
    {
        $data = [
            'dataChsAlphaDash' => '>?'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testChsAlphaDash'));
    }

    public function testChsAlphaDashSuccess()
    {
        $data = [
            'dataChsAlphaDash' => '中文english0123-_'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testChsAlphaDash'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage chsalphanum message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testChsAlphaNumError()
    {
        $data = [
            'dataChsAlphaNum' => '-_'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testChsAlphaNum'));
    }

    public function testChsAlphaNumSuccess()
    {
        $data = [
            'dataChsAlphaNum' => '中文english0123'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testChsAlphaNum'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage confirm message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testConfirmError()
    {
        $data = [
            'dataConfirm' => '123',
            'confirm' => '456'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testConfirm'));
    }

    public function testConfirmSuccess()
    {
        $data = [
            'dataConfirm' => '123',
            'confirm' => '123'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testConfirm'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage different message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testDifferentError()
    {
        $data = [
            'dataDifferent' => '123',
            'different' => '123'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testDifferent'));
    }

    public function testDifferentSuccess()
    {
        $data = [
            'dataDifferent' => '123',
            'different' => '1234a'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testDifferent'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage greaterthan message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testGreaterThanError()
    {
        $data = [
            'dataGreaterThan' => '12',
            'gt' => '123'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testGreaterThan'));
    }

    public function testGreaterThanSuccess()
    {
        $data = [
            'dataGreaterThan' => '124',
            'gt' => '123'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testGreaterThan'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage lessthan message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testLessThanError()
    {
        $data = [
            'dataLessThan' => '124',
            'lt' => '123'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testLessThan'));
    }

    public function testLessThanSuccess()
    {
        $data = [
            'dataLessThan' => '122',
            'lt' => '123'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testLessThan'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage date message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testDateError()
    {
        $data = [
            'dataDate' => '2019f'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testDate'));
    }

    public function testDateSuccess()
    {
        $data = [
            'dataDate' => '2019-07-08 12:00:30'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testDate'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage daterange message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testDateRangeError()
    {
        $data = [
            'dataDateRange' => '2019-06-18'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testDateRange'));
    }

    public function testDateRangeSuccess()
    {
        $data = [
            'dataDateRange' => '2019-07-07 00:00:00'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testDateRange'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage dns message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testDnsError()
    {
        $data = [
            'dataDns' => 'swoft.con'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testDns'));
    }

    public function testDnsSuccess()
    {
        $data = [
            'dataDns' => 'swoft.org'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testDns'));
        $this->assertEquals($data, $result);
    }

//    public function testFileMediaTypeError()
//    {
//    }
//
//    public function testFileMediaTypeSuccess()
//    {
//    }
//
//    public function testFileSizeError()
//    {
//    }
//
//    public function testFileSizeSuccess()
//    {
//    }
//
//    public function testFileSuffixError()
//    {
//    }
//
//    public function testFileSuffixSuccess()
//    {
//    }
//    public function testIsFileError()
//    {
//    }
//    public function testIsFileSuccess()
//    {
//    }
    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage low message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testLowError()
    {
        $data = [
            'dataLow' => 'swofT'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testLow'));
    }

    public function testLowSuccess()
    {
        $data = [
            'dataLow' => 'swoft'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testLow'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage notinenum message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testNotInEnumError()
    {
        $data = [
            'dataNotInEnum' => '1'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testNotInEnum'));
    }

    public function testNotInEnumSuccess()
    {
        $data = [
            'dataNotInEnum' => '4'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testNotInEnum'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage notinrange message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testNotInRangeError()
    {
        $data = [
            'dataNotInRange' => '1'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testNotInRange'));
    }

    public function testNotInRangeSuccess()
    {
        $data = [
            'dataNotInRange' => '4'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testNotInRange'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage upper message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testUpperError()
    {
        $data = [
            'dataUpper' => 'sWOFT'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testUpper'));
    }

    public function testUpperSuccess()
    {
        $data = [
            'dataUpper' => 'SWOFT'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testUpper'));
        $this->assertEquals($data, $result);
    }

    /**
     * @expectedException Swoft\Validator\Exception\ValidatorException
     * @expectedExceptionMessage url message
     *
     * @throws ContainerException
     * @throws ValidatorException
     * @throws \ReflectionException
     */
    public function testUrlError()
    {
        $data = [
            'dataUrl' => 'baidu.com'
        ];
        (new Validator())->validateRequest($data, $this->getValidates(ValidatorRule::class, 'testUrl'));
    }

    public function testUrlSuccess()
    {
        $data = [
            'dataUrl' => 'http://baidu.com'
        ];
        [$result] = (new Validator())->validateRequest($data,
            $this->getValidates(ValidatorRule::class, 'testUrl'));
        $this->assertEquals($data, $result);
    }
}
