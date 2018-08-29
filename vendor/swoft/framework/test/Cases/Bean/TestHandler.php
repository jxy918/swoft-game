<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace SwoftTest\Bean;

use Swoft\Proxy\Handler\HandlerInterface;

/**
 *
 *
 * @uses      TestHandler
 * @version   2017年12月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class TestHandler implements HandlerInterface
{
    /**
     * @var object
     */
    private $target;

    public function __construct($target)
    {
        $this->target = $target;
    }

    public function invoke($method, $parameters)
    {
        $before = 'before';
        $result = $this->target->$method(...$parameters);
        $after = 'after';
        $result .= $before.$after;
        return $result;
    }
}
