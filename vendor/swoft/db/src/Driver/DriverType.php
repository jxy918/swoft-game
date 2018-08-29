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
 * The types of driver
 *
 * @uses      DriverType
 * @version   2018年01月27日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class DriverType
{
    /**
     * The type of coroutine
     */
    const COR = 'cor';

    /**
     * The type of sync
     */
    const SYNC = 'sync';
}
