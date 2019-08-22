<?php declare(strict_types=1);


namespace Swoft\Proxy\Ast\Visitor;


use PhpParser\NodeVisitorAbstract;
use Swoft\Proxy\Contract\VisitorInterface;

abstract class Visitor extends NodeVisitorAbstract implements VisitorInterface
{

}