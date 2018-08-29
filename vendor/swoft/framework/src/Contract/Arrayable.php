<?php

namespace Swoft\Contract;

/**
 * @uses      Arrayable
 * @version   2017-11-09
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface Arrayable
{
    /**
     * @return array
     */
    public function toArray(): array;
}
