<?php

namespace Swoft\Core;

use Swoft\App;

/**
 * 应用基类
 *
 * @uses      Application
 * @version   2017年04月25日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Application
{
    /**
     * @var string 应用ID
     */
    protected $id = APP_NAME;

    /**
     * @var string 应用名称
     */
    protected $name = APP_NAME;

    /**
     * @var bool 是否使用第三方(consul/etcd/zk)注册服务
     */
    protected $useProvider = false;

    /**
     * 初始化
     */
    public function init()
    {
        App::$app = $this;
    }

}
