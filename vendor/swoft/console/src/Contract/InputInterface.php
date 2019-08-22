<?php declare(strict_types=1);

namespace Swoft\Console\Contract;

/**
 * Interface InputInterface
 * @package Swoft\Console\Contract
 */
interface InputInterface
{
    /**
     * 读取输入信息
     *
     * @param string $question 若不为空，则先输出文本消息
     * @param bool   $nl       true 会添加换行符 false 原样输出，不添加换行符
     *
     * @return string
     */
    public function read(string $question = '', bool $nl = false): string;

    /**
     * 获取执行的脚本
     *
     * @return string
     */
    public function getScript(): string;

    /**
     * 获取执行的命令
     *
     * @return string
     */
    public function getCommand(): string;

    /**
     * 获取输入参数集合
     *
     * @return array
     */
    public function getArgs(): array;

    /**
     * 获取输入某个参数
     *
     * @param null|int|string $name
     * @param mixed           $default
     * @return mixed
     */
    public function getArg($name, $default = null);

    /**
     * 获取输入选项集合
     *
     * @return array
     */
    public function getOpts(): array;

    /**
     * 获取某一个选项参数
     *
     * @param string $name
     * @param null   $default
     * @return bool|mixed|null
     */
    public function getOpt(string $name, $default = null);
}
