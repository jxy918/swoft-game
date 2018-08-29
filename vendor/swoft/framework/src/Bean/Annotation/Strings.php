<?php

namespace Swoft\Bean\Annotation;

/**
 * String validator
 *
 * @Annotation
 * @Target("METHOD")
 */
class Strings
{
    /**
     * @var string
     */
    private $from = ValidatorFrom::POST;

    /**
     * Parameter name
     *
     * @var string
     */
    private $name;

    /**
     * Min value
     *
     * @var int
     */
    private $min = PHP_INT_MIN;

    /**
     * Max value
     *
     * @var int
     */
    private $max = PHP_INT_MAX;

    /**
     * @var string
     */
    private $template = '';

    /**
     * 默认值，如果是null，强制验证参数
     *
     * @var null|string
     */
    private $default = null;

    /**
     * Strings constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['from'])) {
            $this->from = $values['from'];
        }
        if (isset($values['name'])) {
            $this->name = $values['name'];
        }
        if (isset($values['min'])) {
            $this->min = $values['min'];
        }
        if (isset($values['max'])) {
            $this->max = $values['max'];
        }
        if (isset($values['default'])) {
            $this->default = $values['default'];
        }
        if (isset($values['template'])) {
            $this->template = $values['template'];
        }
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param string $from
     *
     * @return Strings
     */
    public function setFrom(string $from): Strings
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Strings
     */
    public function setName(string $name): Strings
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @param int $min
     *
     * @return Strings
     */
    public function setMin(int $min): Strings
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * @param int $max
     *
     * @return Strings
     */
    public function setMax(int $max): Strings
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param null|string $default
     *
     * @return Strings
     */
    public function setDefault($default): Strings
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;
    }
}
