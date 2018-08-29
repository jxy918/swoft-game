<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace SwoftTest;

use Swoft\Helper\DirHelper;
use Swoft\Log\Logger;

/**
 * Class LoggerTest
 *
 * @package SwoftTest
 */
class LoggerTest extends AbstractTestCase
{
    /**
     * @var \Swoft\Log\Logger
     */
    protected $logger;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $runtimeDir = alias('@runtime');
        ! file_exists($runtimeDir) && mkdir($runtimeDir, 755, true);
        $this->logger = bean('logger');
        $this->assertInstanceOf(Logger::class, $this->logger);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
        DirHelper::deleteDirectory(alias('@runtime'));
    }

    /**
     * @test
     */
    public function info()
    {
        $infoMessage = 'Test Info Message';
        $result = $this->logger->info($infoMessage);
        $this->assertTrue($result);
        $this->assertContains($infoMessage, $this->getNoticeLog());
    }

    /**
     * @test
     */
    public function error()
    {
        $errorMessage = 'Test Error Message';
        $result = $this->logger->error($errorMessage . 1);
        $this->assertTrue($result);
        $this->assertContains($errorMessage . 1, $this->getErrorLog());
        $result = $this->logger->err($errorMessage . 2);
        $this->assertTrue($result);
        $this->assertContains($errorMessage . 2, $this->getErrorLog());
    }

    /**
     * @return bool|string
     */
    protected function getNoticeLog()
    {
        return file_get_contents(alias('@runtime/logs/notice.log'));
    }

    /**
     * @return bool|string
     */
    protected function getErrorLog()
    {
        return file_get_contents(alias('@runtime/logs/error.log'));
    }
}
