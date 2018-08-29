<?php

namespace Swoft\View\Base;

/**
 * the interface of view
 *
 * @uses      ViewInterface
 * @version   2018年01月14日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface ViewInterface
{
    /**
     * @param string      $view
     * @param array       $data
     * @param string|null $layout
     *
     * @return string
     */
    public function render(string $view, array $data = [], $layout = null);
}