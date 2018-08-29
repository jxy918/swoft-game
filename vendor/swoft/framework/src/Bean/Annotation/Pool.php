<?php

namespace Swoft\Bean\Annotation;

/**
 * the annotation of pool
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @uses      Pool
 * @version   2017年12月15日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Pool
{
    /**
     * the name of pool
     *
     * @var string
     */
    private $name = "";

    /**
     * Pool constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }
        if (isset($values['name'])) {
            $this->name = $values['name'];
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
