<?php
namespace Leuffen\TextTemplate;

require __DIR__ . "/../vendor/autoload.php";

use Tester\Assert;


\Tester\Environment::setup();

Assert::throws(function() {
    $in = "{= some.var.bla}";
    $tt = new TextTemplate($in);
    $tt->apply(["some"], false);
}, UndefinedVariableException::class);

Assert::throws(function() {
    $in = "{= some.var}";
    $tt = new TextTemplate($in);
    $obj = new \stdClass();
    $tt->apply($obj, false);
}, UndefinedVariableException::class);

try {
    $in = "{= some.var}";
    $tt = new TextTemplate($in);
    $obj = new \stdClass();
    $tt->apply($obj, false);
    Assert::fail("No Exception was thrown");
} catch (UndefinedVariableException $e) {
    Assert::equal("some.var", $e->getTriggerVarName());
}
