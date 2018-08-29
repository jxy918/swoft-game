<?php

namespace Swoft\Bean\Annotation;

/**
 * the annotation of cache evict
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @uses      CacheEvict
 * @version   2017年12月30日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class CacheEvict
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $condition;

    /**
     * @var bool;
     */
    private $all = false;

    /**
     * CachePut constructor.
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
        if (isset($values['key'])) {
            $this->key = $values['key'];
        }
        if (isset($values['all'])) {
            $this->all = $values['all'];
        }
        if (isset($values['condition'])) {
            $this->condition = $values['condition'];
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
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @return bool
     */
    public function isAll(): bool
    {
        return $this->all;
    }
}
