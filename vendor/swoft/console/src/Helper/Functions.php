<?php declare(strict_types=1);

use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Console\Style\Style;

if (!function_exists('input')) {
    /**
     * @return Input
     */
    function input(): Input
    {
        return Swoft::getSingleton(Input::class);
    }
}

if (!function_exists('output')) {
    /**
     * @return Output
     */
    function output(): Output
    {
        return Swoft::getSingleton(Output::class);
    }
}

if (!function_exists('style')) {
    /**
     * @return Style
     */
    function style(): Style
    {
        return Swoft::getSingleton(Style::class);
    }
}
