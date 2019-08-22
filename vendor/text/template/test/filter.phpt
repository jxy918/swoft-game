<?php
namespace Leuffen\TextTemplate;

require __DIR__ . "/../vendor/autoload.php";


use Tester\Assert;



\Tester\Environment::setup();


$p = new TextTemplate(file_get_contents(__DIR__ . "/unit/filter.in.txt"));
Assert::equal(
    file_get_contents(__DIR__ . "/unit/filter.expected.txt"),
    $p->apply(["str" => "abc123'\"", "strMultiLine" => "a\nb\nc"])
    , "Error in filter-test"
);


$p = new TextTemplate("T: {= t1}");
$p->setDefaultFilter("raw");
Assert::equal("T: <>", $p->apply(["t1" => "<>"]));




