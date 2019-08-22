<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2019-02-02
 * Time: 14:51
 */

namespace Swoft\Http\Server\Annotation\Mapping;

/**
 * Class RequestMethod - supported request method list
 * @since 2.0
 * @package Swoft\Http\Server\Annotation\Mapping
 */
class RequestMethod
{
    public const GET     = 'GET';
    public const POST    = 'POST';
    public const PUT     = 'PUT';
    public const PATCH   = 'PATCH';
    public const DELETE  = 'DELETE';
    public const OPTIONS = 'OPTIONS';
    public const HEAD    = 'HEAD';
}
