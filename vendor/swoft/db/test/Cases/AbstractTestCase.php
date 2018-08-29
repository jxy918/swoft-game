<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace SwoftTest\Db\Cases;

use PHPUnit\Framework\TestCase;

/**
 * Class AbstractTestCase
 *
 * @package SwoftTest\Db\Cases
 */
abstract class AbstractTestCase extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        swoole_timer_after(6 * 1000, function () {
            swoole_event_exit();
        });
    }
}
