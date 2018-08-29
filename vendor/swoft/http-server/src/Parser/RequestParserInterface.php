<?php

namespace Swoft\Http\Server\Parser;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface of request parser
 */
interface RequestParserInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function parse(ServerRequestInterface $request): ServerRequestInterface;
}
