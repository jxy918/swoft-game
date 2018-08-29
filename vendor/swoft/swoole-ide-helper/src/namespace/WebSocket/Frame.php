<?php
namespace Swoole\WebSocket;

/**
 * @since 2.1.3
 * @property int $fd  客户端的socket id，使用`$server->push`推送数据时需要用到
 * @property int $data 数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断
 * @property int $opcode WebSocket的OpCode类型，可以参考WebSocket协议标准文档
 * @property int $finish 表示数据帧是否完整，一个WebSocket请求可能会分成多个数据帧进行发送
 *
 */
class Frame
{
}
