<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 25.07.18
 * Time: 11:38
 */

namespace Test;
require __DIR__ . "/../vendor/autoload.php";

use http\Exception\InvalidArgumentException;
use Leuffen\TextTemplate\TextTemplate;
use Tester\Assert;
use Tester\Environment;

Environment::setup();


$tpl = <<<EOT
-A-
{section name="A"}
    AAA
{/section}
-B-
{section name="B" > out}
    BBB
{/section}
-C-
{= out}
EOT;

$compare = <<<EOT
-A-*AAA*
-B-
-C-
*BBB*
EOT;


$template = new TextTemplate($tpl);
$sec = [];
$template->addSection("section", function ($content, $params, $command, $context, $cmdParam) use (&$sec) {
     $sec[$params["name"]] = $content;
     if ($command !== "section")
         throw new InvalidArgumentException("Command missing");
     return "*" . trim ($content) . "*";
});
$textResult = $template->apply([]);

Assert::equal($compare, $textResult);
Assert::equal("AAA", trim($sec["A"]));
Assert::equal("BBB", trim($sec["B"]));
