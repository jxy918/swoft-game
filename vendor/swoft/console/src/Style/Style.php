<?php

namespace Swoft\Console\Style;

use Swoft\Bean\Annotation\Bean;
use Swoft\Console\Helper\CommandHelper;

/**
 * The style of command
 * @Bean()
 */
class Style
{
    // 默认样式集合
    const NORMAL = 'normal';
    const FAINTLY = 'faintly';
    const BOLD = 'bold';
    const NOTICE = 'notice';
    const PRIMARY = 'primary';
    const SUCCESS = 'success';
    const INFO = 'info';
    const NOTE = 'note';
    const WARNING = 'warning';
    const COMMENT = 'comment';
    const QUESTION = 'question';
    const DANGER = 'danger';
    const ERROR = 'error';
    const UNDERLINE = 'underline';
    const BLUE = 'blue';
    const CYAN = 'cyan';
    const MAGENTA = 'magenta';
    const RED = 'red';
    const YELLOW = 'yellow';

    /**
     * tag样式表情匹配正则
     */
    const TAGS_REG = '/<([a-z=;]+)>(.*?)<\/\\1>/s';

    /**
     * 移除颜色匹配正则
     */
    const STRIP_REG = '/<[\/]?[a-z=;]+>/';

    /**
     * 所有初始化的样式tag标签
     *
     * @var array
     */
    private $tags = [];

    /**
     * 初始化颜色标签
     *
     * @throws \InvalidArgumentException
     */
    public function init()
    {
        $this->tags[self::NORMAL] = Color::make('normal');
        $this->tags[self::FAINTLY] = Color::make('normal', '', ['italic']);
        $this->tags[self::BOLD] = Color::make('', '', ['bold']);
        $this->tags[self::INFO] = Color::make('green');
        $this->tags[self::NOTE] = Color::make('green', '', ['bold']);
        $this->tags[self::PRIMARY] = Color::make('blue');
        $this->tags[self::SUCCESS] = Color::make('green', '', ['bold']);
        $this->tags[self::NOTICE] = Color::make('', '', ['bold', 'underscore']);
        $this->tags[self::WARNING] = Color::make('yellow');
        $this->tags[self::COMMENT] = Color::make('yellow');
        $this->tags[self::QUESTION] = Color::make('black', 'cyan');
        $this->tags[self::DANGER] = Color::make('red');
        $this->tags[self::ERROR] = Color::make('black', 'red');
        $this->tags[self::UNDERLINE] = Color::make('normal', '', ['underscore']);
        $this->tags[self::BLUE] = Color::make('blue');
        $this->tags[self::CYAN] = Color::make('cyan');
        $this->tags[self::MAGENTA] = Color::make('magenta');
        $this->tags[self::RED] = Color::make('red');
        $this->tags[self::YELLOW] = Color::make('yellow');
    }

    /**
     * 颜色翻译
     *
     * @param string $message 文字
     * @return mixed|string
     */
    public function t(string $message)
    {
        // 不支持颜色，移除颜色标签
        if (!CommandHelper::supportColor()) {
            return $this->stripColor($message);
        }

        $isMatch = preg_match_all(self::TAGS_REG, $message, $matches);
        // 未匹配颜色标签
        if ($isMatch === false) {
            return $message;
        }

        // 颜色标签处理
        foreach ((array)$matches[0] as $i => $m) {
            if (array_key_exists($matches[1][$i], $this->tags)) {
                $message = $this->replaceColor($message, $matches[1][$i], $matches[2][$i], (string)$this->tags[$matches[1][$i]]);
            }
        }
        return $message;
    }

    /**
     * 根据信息，新增一个tag颜色标签
     *
     * @param string $name    名称
     * @param string $fg      前景色
     * @param string $bg      背景色
     * @param array  $options 颜色选项
     * @throws \InvalidArgumentException
     */
    public function addTag(string $name, string $fg = '', string $bg = '', array $options = [])
    {
        $this->tags[$name] = Color::make($fg, $bg, $options);
    }

    /**
     * 根据颜色对象，新增一个tag颜色标签
     *
     * @param string $name
     * @param Color  $color
     */
    public function addTagByColor(string $name, Color $color)
    {
        $this->tags[$name] = $color;
    }

    /**
     * 标签替换成颜色
     *
     * @param string $text
     * @param string $tag
     * @param string $match
     * @param string $style
     * @return string
     */
    private function replaceColor(string $text, string $tag, string $match, string $style): string
    {
        $replace = sprintf("\033[%sm%s\033[0m", $style, $match);
        return str_replace("<$tag>$match</$tag>", $replace, $text);
    }

    /**
     * 移除颜色标签
     *
     * @param string $message
     * @return mixed
     */
    public function stripColor(string $message)
    {
        return preg_replace(self::STRIP_REG, '', $message);
    }

    /**
     * @param string $text
     * @param string $tag
     * @return string
     */
    public static function wrap(string $text, string $tag = 'info'): string
    {
        return \sprintf('<%s>%s</%s>', $tag, $text, $tag);
    }
}
