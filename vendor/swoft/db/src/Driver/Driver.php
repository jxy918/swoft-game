<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db\Driver;

/**
 * The driver class of database
 *
 * @uses      Driver
 * @version   2018年01月27日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Driver
{
    /**
     * The driver of mysql
     */
    const MYSQL = 'mysql';

    /**
     * Pgsql
     */
    const PGSQL = 'pgsql';
}
