# Idea

## 关于 fd

- `fd` 是TCP客户端连接的标识符，在Server程序中是唯一的
- `fd` 是一个自增数字，范围是1 ～ 1600万，`fd`超过1600万后会自动从1开始进行复用
- `fd` 是复用的，当连接关闭后`fd`会被新进入的连接复用

## Design

- websocket 按请求的路由(eg `/echo`)分模块
- 建立连接后，每次消息请求划分不同的handler

```text
                   swoft ws server
                          |
            +--------------------------------+
            |                                |
      echo module(EchoModule)                |
(path /echo, namespace /echo)                |
                                        chat module(ChatModule)
                                (path /chat, namespace /chat)  
                                             |
                               -------------------
                               |      |       |      ......
                            room 1  room 2  room 3   ......             
```

## 应用目录结构

可以：

```text
/WebSocket
  EchoModule.php
  ChatModule.php
  ... ...
  Echo/
    EchoController.php
    ... ...
  Chat/
    ChatController.php
    ... ...
```

也可以：

```text
/WebSocket
  EchoModule.php
  ChatModule.php
  ... ...
  Controller/
    EchoController.php
    ChatController.php
    ... ...
```

## 连接处理流程

```text
握手请求 -------> 接收到WebSocket请求(根据path找到处理模块 eg EchoModule)
                         |
                         |使用模块类中标记的握手方法验证请求
                         |
                         v
                   握手成功，接受连接 
                         |
                         | 创建连接上下文Connection，存储到Session管理器
                         | (含有fd, request等信息)
                         |
                         v
          消息请求 --> 接收消息
                         |
                         |创建消息上下文Context，存储到Context管理器
                         |同时通过CoID会与Session的绑定关系
                         |
                         V
                    解析消息数据
                         |
                         |得到消息指令和消息body       
                         |(根据消息指令找到处理控制器 eg ChatController)
                         |
                         V
                    调度消息处理
                         |
                         |调用对应的message控制器方法处理
                         |
                         V
                    打包返回数据
                         |
                         |销毁此次消息请求的上下文Context
                         |同时删除与Session的绑定关系
                         |
                         v
          得到响应 <--- 返回结果
                         |
          消息请求 -->    |
              .          |
              .          |(重复上述消息处理流程)
              .          |
          <-- 得到响应    |
                         |
                         v
      断开连接 -——--> 收到关闭事件
                         |
                         | 销毁连接上下文，从Session管理器删除此连接
                         |
                         v
                      关闭连接
```
