<?php
namespace Swoft\Testing;

class SwooleRequest extends \Swoole\Http\Request
{
    public $get;
    public $post;
    public $header;
    public $server;
    public $cookie;
    public $files;

    public $fd;

    private $testContent;

    /**
     * 获取非urlencode-form表单的POST原始数据
     * @return string
     */
    public function rawContent()
    {
        return $this->testContent;
    }

    public function setRawContent($content)
    {
        $this->testContent = $content;
    }
}
