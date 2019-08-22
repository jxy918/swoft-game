<?php declare(strict_types=1);

namespace Swoft\Smarty\Contract;

/**
 * Class SmartyInterface The interface of view
 * @since 1.0
 */
interface SmartyInterface
{
    /**
     * @return object
     */
    public function initView(): object;
}
