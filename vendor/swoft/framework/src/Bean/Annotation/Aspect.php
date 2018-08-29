<?php

namespace Swoft\Bean\Annotation;

/**
 * the annotation of aspect
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @uses      Aspect
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Aspect
{
    /**
     * @var int
     */
    private $order = PHP_INT_MAX;

    /**
     * Aspect constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->order = $values['value'];
        }
        if (isset($values['order'])) {
            $this->order = $values['order'];
        }
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }
}
