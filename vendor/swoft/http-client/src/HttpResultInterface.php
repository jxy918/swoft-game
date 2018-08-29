<?php

namespace Swoft\HttpClient;

use Psr\Http\Message\ResponseInterface;
use Swoft\Core\ResultInterface;


/**
 * @uses      HttpResultInterface
 * @version   2018年02月03日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2018 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface HttpResultInterface extends ResultInterface
{

    /**
     * @param array ...$params
     * @return string
     */
    public function getResult(...$params): string;

    /**
     * @param array ...$params
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(...$params): ResponseInterface;


}