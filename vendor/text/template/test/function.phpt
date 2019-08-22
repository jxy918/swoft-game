<?php
namespace Leuffen\TextTemplate;

require __DIR__ . "/../vendor/autoload.php";

use Tester\Assert;

/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 17.07.15
 * Time: 15:55
 */


\Tester\Environment::setup();

$in = "{someFunc arg1='StrVal' arg2=var1}";
$tt = new TextTemplate();
$tt->addFunction("someFunc",
    function ($paramArr, $command, $context, $cmdParam) {
        return $command . "(" . json_encode($paramArr) . ")";
    }
);

$out = $tt->loadTemplate($in)->apply(["var1" => "value1"]);
Assert::equal('someFunc({"arg1":"StrVal","arg2":"value1"})', $out);

$in = "{someFunc arg1='StrVal' arg2=var1 > out}{=out|raw}";
$out = $tt->loadTemplate($in)->apply(["var1" => "value1"]);
Assert::equal('someFunc({"arg1":"StrVal","arg2":"value1"})', $out);

$in = "{someFunc arg1='StrVal' arg2=var1}";
$out = $tt->loadTemplate($in)->apply([]);
Assert::equal('someFunc({"arg1":"StrVal","arg2":null})', $out);



$tt->addFunction("throw", function () {
    throw new \Exception("Some Exception");
});

$in = "{throw !> err}{if err != null}{= err}{/if}";
$out = $tt->loadTemplate($in)->apply([]);
Assert::equal('Some Exception', $out);


$in = "{set retVal='SomeValue'}{=retVal}";
$out = $tt->loadTemplate($in)->apply([], true, $retContext);
Assert::equal('SomeValue', $out);
Assert::equal("SomeValue", $retContext["retVal"]);


