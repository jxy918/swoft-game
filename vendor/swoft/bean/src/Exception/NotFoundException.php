<?php declare(strict_types=1);

namespace Swoft\Bean\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class NotFoundException
 *
 * @since 2.0
 */
class NotFoundException extends Exception implements NotFoundExceptionInterface
{

}