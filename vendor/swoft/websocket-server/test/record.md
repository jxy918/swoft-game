# record

## websocket status

```php
WEBSOCKET_STATUS_CONNECTION = 1，连接进入等待握手
WEBSOCKET_STATUS_HANDSHAKE = 2，正在握手
WEBSOCKET_STATUS_FRAME = 3，已握手成功等待浏览器发送数据帧
```

- 拒绝连接 比如需要认证，限定路由，限定ip，限定domain等

## Handshake Example

### Request Headers

```text
GET ws://127.0.0.1:18307/ HTTP/1.1
Host: 127.0.0.1:18307
Connection: Upgrade
Pragma: no-cache
Cache-Control: no-cache
Upgrade: websocket
Origin: http://www.blue-zero.com
Sec-WebSocket-Version: 13
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36
Accept-Encoding: gzip, deflate, br
Accept-Language: zh-CN,zh;q=0.9
Sec-WebSocket-Key: x/uZyzrrAPFopD8Ydwbb1Q==
Sec-WebSocket-Extensions: permessage-deflate; client_max_window_bits
```

### Response Headers

```text
HTTP/1.1 101 Switching Protocols
Upgrade: websocket
Connection: Upgrade
Sec-Websocket-Accept: MN6FxKAAalmiGJ8Bg4kK5YO+wB8=
Sec-Websocket-Version: 13
Server: swoole-http-server
```