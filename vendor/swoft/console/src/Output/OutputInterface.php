<?php

namespace Swoft\Console\Output;

/**
 * 输出接口
 */
interface OutputInterface
{
    /**
     * 输出一行数据
     *
     * @param string $messages 信息
     * @param bool   $newline  是否换行
     * @param bool   $quit     是否退出
     */
    public function writeln($messages = '', $newline = true, $quit = false);

    /**
     * 输出一个列表
     *
     * @param array       $list       列表数据
     * @param string      $titleStyle 标题样式
     * @param string      $cmdStyle   命令样式
     * @param string|null $descStyle  描述样式
     */
    public function writeList(
        array $list,
        $titleStyle = 'comment',
        string $cmdStyle = 'info',
        string $descStyle = null
    );
}
