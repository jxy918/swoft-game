<?php

namespace Swoft\Console\Output;

use Swoft\Bean\Annotation\Bean;

/**
 * 输出
 * @Bean()
 */
class Output implements OutputInterface
{
    /**
     * 间隙字符
     */
    const GAP_CHAR = '  ';

    /**
     * 左边字符
     */
    const LEFT_CHAR = '  ';

    /**
     * 输出一行数据
     *
     * @param string|array $messages 信息
     * @param bool   $newline  是否换行
     * @param bool   $quit     是否退出
     */
    public function writeln($messages = '', $newline = true, $quit = false)
    {
        if (\is_array($messages)) {
            $messages = \implode($newline ? PHP_EOL : '', $messages);
        }

        // 文字里面颜色标签翻译
        $messages = \style()->t((string)$messages);

        // 输出文字
        echo $messages;
        if ($newline) {
            echo "\n";
        }

        // 是否退出
        if ($quit) {
            exit;
        }
    }

    /**
     * 输出显示LOGO图标
     */
    public function writeLogo()
    {
        $logo = "
 ____                __ _
/ ___|_      _____  / _| |_
\___ \ \ /\ / / _ \| |_| __|
 ___) \ V  V / (_) |  _| |_
|____/ \_/\_/ \___/|_|  \__|
";
        $this->colored(' ' . \ltrim($logo));
    }

    /**
     * @param string $text
     * @param string $tag
     */
    public function colored(string $text, string $tag = 'info')
    {
        $this->writeln(\sprintf('<%s>%s</%s>', $tag, $text, $tag));
    }

    /**
     * 输出一个列表
     *
     * @param array       $list       列表数据
     * @param string      $titleStyle 标题样式
     * @param string      $cmdStyle   命令样式
     * @param string|null $descStyle  描述样式
     */
    public function writeList(array $list, $titleStyle = 'comment', string $cmdStyle = 'info', string $descStyle = null)
    {
        foreach ($list as $title => $items) {
            // 标题
            $title = "<$titleStyle>$title</$titleStyle>";
            $this->writeln($title);

            // 输出块内容
            $this->writeItems((array)$items, $cmdStyle);
            $this->writeln('');
        }
    }

    /**
     * 显示命令列表一块数据
     *
     * @param array  $items    数据
     * @param string $cmdStyle 命令样式
     */
    private function writeItems(array $items, string $cmdStyle)
    {
        foreach ($items as $cmd => $desc) {
            // 没有命令，只是一行数据
            if (\is_int($cmd)) {
                $message = self::LEFT_CHAR . $desc;
                $this->writeln($message);
                continue;
            }

            // 命令和描述
            $maxLength = $this->getCmdMaxLength(array_keys($items));
            $cmd = \str_pad($cmd, $maxLength, ' ');
            $cmd = "<$cmdStyle>$cmd</$cmdStyle>";
            $message = self::LEFT_CHAR . $cmd . self::GAP_CHAR . $desc;

            $this->writeln($message);
        }
    }

    /**
     * 所有命令最大宽度
     *
     * @param array $commands 所有命令
     * @return int
     */
    private function getCmdMaxLength(array $commands): int
    {
        $max = 0;

        foreach ($commands as $cmd) {
            $length = \strlen($cmd);
            if ($length > $max) {
                $max = $length;
                continue;
            }
        }

        return $max;
    }
}
