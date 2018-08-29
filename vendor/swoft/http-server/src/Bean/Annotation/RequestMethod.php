<?php

namespace Swoft\Http\Server\Bean\Annotation;

/**
 * HTTP request method
 */
class RequestMethod
{
    /**
     * Get method
     */
    const GET = 'get';

    /**
     * Post method
     */
    const POST = 'post';

    /**
     * Put method
     */
    const PUT = 'put';

    /**
     * Delete method
     */
    const DELETE = 'delete';

    /**
     * Patch method
     */
    const PATCH = 'patch';

    /**
     * Options method
     */
    const OPTIONS = 'options';
}
