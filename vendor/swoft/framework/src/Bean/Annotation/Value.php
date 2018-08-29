<?php

namespace Swoft\Bean\Annotation;

/**
 * Value annotation
 *
 * 1. 注入值
 * 2. 注入property配置文件值
 * 3. 注入env环境变量
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Value
{
    /**
     * Property name
     *
     * @var string
     */
    private $name = '';

    /**
     * Env name
     *
     * @var string
     */
    private $env = '';

    /**
     * Value constructor.
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
        if (isset($values['env'])) {
            $this->env = $values['env'];
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
    public function getEnv(): string
    {
        return $this->env;
    }
}
