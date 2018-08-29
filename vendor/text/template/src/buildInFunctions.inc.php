<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.10.17
 * Time: 08:16
 */


namespace Leuffen\TextTemplate;


TextTemplate::$__DEFAULT_FUNCTION["break"] =
    function ($paramArr, $command, $context, $cmdParam) {
        throw new __BreakLoopException("{break} called outside loop");
    };

TextTemplate::$__DEFAULT_FUNCTION["continue"] =
    function ($paramArr, $command, $context, $cmdParam) {
        throw new __ContinueLoopException("{continue} called outside loop");
    };

TextTemplate::$__DEFAULT_FUNCTION["set"] =
    function ($paramArr, $command, &$context, $cmdParam) {
        foreach ($paramArr as $name => $val)
            $context[$name] = $val;
        return "";
    };