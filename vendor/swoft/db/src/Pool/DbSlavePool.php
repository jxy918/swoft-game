<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db\Pool;

use Swoft\Bean\Annotation\Inject;
use Swoft\Bean\Annotation\Pool;
use Swoft\Db\Pool\Config\DbSlavePoolConfig;

/**
 * Slave pool
 *
 * @Pool("default.slave")
 */
class DbSlavePool extends DbPool
{
    /**
     * @Inject()
     * @var DbSlavePoolConfig
     */
    protected $poolConfig;
}
