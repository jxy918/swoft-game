<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 10.10.17
 * Time: 15:20
 */


namespace Leuffen\TextTemplate;

TextTemplate::$__DEFAULT_FILTER["_DEFAULT_"] = function ($input) { return htmlspecialchars($input); };

// Raw is only a pseudo-filter. If it is not in the chain of filters, __DEFAULT__ will be appended to the filter
TextTemplate::$__DEFAULT_FILTER["html"] = function ($input) { return htmlspecialchars($input); };
TextTemplate::$__DEFAULT_FILTER["raw"] = function ($input) { return $input; };
TextTemplate::$__DEFAULT_FILTER["singleLine"] = function ($input) { return str_replace("\n", " ", $input); };
TextTemplate::$__DEFAULT_FILTER["inivalue"] = function ($input) { return addslashes(str_replace("\n", " ", $input)); };

TextTemplate::$__DEFAULT_FILTER["fixedLength"] = function ($input, $length, $padChar=" ") {
    return str_pad(substr($input, 0, $length), $length, $padChar);
};

TextTemplate::$__DEFAULT_FILTER["inflect"] = function ($input, $type="tag") {
    switch ($type) {
        case "tag":
            return preg_replace("/[^a-z0-9]/im", "_", trim(strtolower($input)));


        default:
            return "##ERR:inflect:$type - unknown type: '$type'##";
    }
};

TextTemplate::$__DEFAULT_FILTER["sanitize"] = function ($input, $type) {
    switch ($type) {
        case "hostname":
            return preg_replace("/[^a-z0-9\.\-]/im", "", trim(strtolower($input)));

        default:
            return "##ERR:sanitize:$type - unknown type: '$type'##";
    }
};