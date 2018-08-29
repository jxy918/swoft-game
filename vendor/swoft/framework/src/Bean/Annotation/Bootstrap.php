<?php

namespace Swoft\Bean\Annotation;

/**
 * the annotation of bootstrap
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @uses      Bootstrap
 * @version   2018年01月12日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Bootstrap
{
    /**
     * bean名称
     *
     * @var string
     */
    private $name = "";

    /**
     * @var int
     */
    private $order = PHP_INT_MAX;

    /**
     * Bean constructor.
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
        if (isset($values['order'])) {
            $this->order = $values['order'];
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }
}