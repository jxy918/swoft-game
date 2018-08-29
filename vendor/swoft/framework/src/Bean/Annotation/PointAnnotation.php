<?php

namespace Swoft\Bean\Annotation;

/**
 * the point cut of annotation
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @uses      PointAnnotation
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class PointAnnotation
{
    /**
     * @var array
     */
    private $include = [];

    /**
     * @var array
     */
    private $exclude = [];

    /**
     * PointBean constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->include = $values['value'];
        }
        if (isset($values['include'])) {
            $this->include = $values['include'];
        }
        if (isset($values['exclude'])) {
            $this->exclude = $values['exclude'];
        }
    }

    /**
     * @return array
     */
    public function getInclude(): array
    {
        return $this->include;
    }

    /**
     * @return array
     */
    public function getExclude(): array
    {
        return $this->exclude;
    }
}
