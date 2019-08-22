<?php declare(strict_types=1);

namespace Swoft\Bean\Exception;


use Exception;
use Psr\Container\ContainerExceptionInterface;

/**
 * Class ContainerException
 *
 * @since 2.0
 */
class ContainerException extends Exception implements ContainerExceptionInterface
{

}