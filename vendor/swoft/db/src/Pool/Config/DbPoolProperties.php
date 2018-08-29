<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db\Pool\Config;

use Swoft\Db\Driver\Driver;
use Swoft\Pool\PoolProperties;

/**
 * The pool properties of database
 *
 * @uses      DbPoolProperties
 * @version   2018年01月27日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class DbPoolProperties extends PoolProperties
{
    /**
     * The default of driver
     *
     * @var string
     */
    protected $driver = Driver::MYSQL;

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }
}
