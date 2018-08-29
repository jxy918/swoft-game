<?php

namespace Swoft\Bean\Annotation;

/**
 * the annotation of exception handler
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @uses      Handler
 * @version   2018年01月14日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Handler
{
    /**
     * @var string
     */
    private $exception;

    /**
     * Handler constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->exception = $values['value'];
        }

        if (isset($values['exception'])) {
            $this->exception = $values['exception'];
        }
    }

    /**
     * @return string
     */
    public function getException(): string
    {
        return $this->exception;
    }
}