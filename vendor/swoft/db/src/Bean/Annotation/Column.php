<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Db\Bean\Annotation;

/**
 * Column
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Column
{
    /**
     * 名称
     *
     * @var string
     */
    private $name;

    /**
     * 类型
     *
     * @var string
     */
    private $type = 'string';

    /**
     * 长度
     *
     * @var int
     */
    private $length = -1;

    /**
     * @var mixed
     */
    private $default;

    /**
     * Column constructor.
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
        if (isset($values['type'])) {
            $this->type = $values['type'];
        }
        if (isset($values['length'])) {
            $this->length = $values['length'];
        }
        if (isset($values['default'])) {
            $this->default = $values['default'];
        }
    }

    /**
     * 数据字段名称
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 类型
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * 长度
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }
}
