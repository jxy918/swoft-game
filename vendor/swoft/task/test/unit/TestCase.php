<?php declare(strict_types=1);


namespace SwoftTest\Task\Unit;


use SwoftTest\Task\Testing\MockTaskServer;

/**
 * Class TestCase
 *
 * @since 2.0
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockTaskServer
     */
    protected $mockTaskServer;

    /**
     * Server
     */
    public function setUp()
    {
        $this->mockTaskServer = new MockTaskServer();
    }
}