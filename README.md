# swoft-game

* 基于swoft2.0框架开发游戏服务器框架（把自己写的游戏框架swoole-game，移植到swoft框架上，可以使用swoft2.0框架的丰富组件功能）

* 自己写的框架源码github：**[swoole-game](https://github.com/jxy918/swoole-game)**
* 自己写的框架使用github：**[swoole-game-framework](https://github.com/jxy918/swoole-game-framework)**

### 一，概述

* 该框架是基于swoft2.0框架开发的游戏框架，主要用于开发游戏服务器，简化游戏前后端开发，框架主要实现了前后端，封包解包，协议解析，压缩，粘包，路由等功能，代码示例为h5游戏。
* 框架比较简单, 把游戏框架里一些逻辑到swoft框架上。
* 学习之前请先了解swoft2.0框架。

### 二，示例图

![游戏demo1](images/demo1.jpg)
![游戏demo2](images/demo2.jpg)
![游戏demo3](images/demo3.png)
![游戏demo4](images/demo4.jpg)
![客户端交互测试工具](images/demo5.png)
 
### 三，特性

* 实现前后端二进制封包解包，采用的是msgpack扩展，msgpack对数据进行了压缩，并实现粘包处理
* 数据采用固定包头，包头4个字节存包体长度，包体前2个字节分别为cmd(主命令字)，scmd(子命令字)，后面为包体内容
* 采用策略模式解耦游戏中的每个协议逻辑
* 实现定义游戏开发cmd(主命令字)和scmd(子命令字)定义，cmd和scmd主要用来路由到游戏协议逻辑处理
* 代码里有个JokerPoker类是一个款小游戏算法类，是一个示例，如示例图1,2,3,4
* 代码主要是用框架实现小游戏JokerPoker例子，服务端代码开源，客户端代码暂不开源，但是提供客户端交互测试工具，见示例图5。
* 可以方便的把JokerPoker范例去除，只使用框架功能定制开发开发自己的游戏功能

          
### 四，环境依赖

>依赖swoft环境，请安装php扩展msgpack
 
* php vesion > 7.0
* swoole version > 4.4.1   
* msgpack
* swoft vesion > 2.0

     
### 五，开始使用
* 1，安装
```
composer install
``` 

* 2，目录说明（swoft目录不具体说明）：

```
./app/Http/Controller/GameController.php		游戏http控制器逻辑
./app/WebSocket/Game		    是这个整体游戏服务器逻辑
./app/WebSocket/Game/Conf	    逻辑配置目录, 比如:命令字, 子名字, 路由转发
./app/WebSocket/Game/Core		游戏路由转发,算法,解包核心类
./app/WebSocket/Game/Login		游戏路由转发逻辑协议包处理目录
./public/client				    测试工具view的资源文件
./resources/views/game		    测试工具view

``` 
         
* 3，进入根目录目录，启动服务器(swoft启动websocket启动法） ：

```
// 启动服务，根据
php bin/swoft ws:start

// 守护进程启动，覆盖 
php bin/swoft ws:start -d

// 重启
php bin/swoft ws:restart

// 重新加载
php bin/swoft ws:reload

// 关闭服务
php bin/swoft ws:stop

```  

* 4，访问url：

```
//测试工具访问入口
http://[ip]:[port]/test

//广播消息测试， 可以通过次url给websocket广播消息， msg就是消息内容                       
http://[ip]:[port]/broadcast?msg=%E4%BD%A0%E5%A6%B9%E7%9A%84

```

* 5 ，H5游戏客户端代码由于公司限制，暂不开放， 但是提供了一个客户端交互测试工具，直接把client目录放入web服务器， 修改客服端配置文件配置websocket链接就能运行。

### 六，联系方式

* qq：251413215，加qq请输入验证消息：swoft-game

### 七，备注

* 可以使用根目录增加docker运行环境(Dockerfile)， 可以直接执行下面的命令，创建镜像php_swoole, 环境增加php-protobuf，php-msgpack支持。 

```
docker build -t php_swoole .

```
* 注意如果程序不能自动加载，请去除环境中opcache扩展。
* 服务器增加支持TCP服务器，服务器启动就会监控TCP游戏服务器， 可以通过/test/tcp_client.php测试。

```
php ./test/tcp_client

```
    
* 请使用根目录下的Dockerfile可以直接跑, 如果使用swoft的Dockerfile需要自行安装msgpack扩展, 
* **[swoft框架](https://github.com/swoft-cloud/swoft/)** 

### 八，增加录像直播功能, 此功能只是测试

* 1, 视频录制url，如果电脑没有摄像头， 请用手机测试录制视频：

````
http://[ip]:[port]/camera
````

* 2，浏览器上播放视频url如下：

```
http://[ip]:[port]/show
```



