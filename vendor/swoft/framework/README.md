<p align="center">
    <a href="https://github.com/swoft-cloud/swoft" target="_blank">
        <img src="http://qiniu.daydaygo.top/swoft-logo.png?imageView2/2/w/300" alt="swoft" />
    </a>
</p>

[![Latest Version](https://img.shields.io/badge/beta-v1.0.7-green.svg?maxAge=2592000)](https://github.com/swoft-cloud/swoft-framework/releases)
[![Build Status](https://travis-ci.org/swoft-cloud/swoft-framework.svg?branch=master)](https://travis-ci.org/swoft-cloud/swoft-framework)
[![Php Version](https://img.shields.io/badge/php-%3E=7.0-brightgreen.svg?maxAge=2592000)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=2.1.1-brightgreen.svg?maxAge=2592000)](https://github.com/swoole/swoole-src)
[![Hiredis Version](https://img.shields.io/badge/hiredis-%3E=0.1-brightgreen.svg?maxAge=2592000)](https://github.com/redis/hiredis)
[![Swoft Doc](https://img.shields.io/badge/docs-passing-green.svg?maxAge=2592000)](https://doc.swoft.org)
[![Swoft License](https://img.shields.io/hexpm/l/plug.svg?maxAge=2592000)](https://github.com/swoft-cloud/swoft/blob/master/LICENSE)

# 简介
首个基于 Swoole 原生协程的新时代 PHP 高性能协程全栈框架，内置协程网络服务器及常用的协程客户端，常驻内存，不依赖传统的 PHP-FPM，全异步非阻塞 IO 实现，以类似于同步客户端的写法实现异步客户端的使用，没有复杂的异步回调，没有繁琐的 yield, 有类似 Go 语言的协程、灵活的注解、强大的全局依赖注入容器、完善的服务治理、灵活强大的 AOP、标准的 PSR 规范实现等等，可以用于构建高性能的Web系统、API、中间件、基础服务等等。

- 基于 Swoole 扩展
- 内置协程网络服务器
- 强大的 AOP (面向切面编程)
- 灵活完善的注解功能
- 全局的依赖注入容器
- 基于 PSR-7 的 HTTP 消息实现
- 基于 PSR-14 的事件管理器
- 基于 PSR-15 的中间件
- 基于 PSR-16 的缓存设计
- 可扩展的高性能 RPC
- 完善的服务治理，熔断，降级，负载，注册与发现
- 数据库 ORM
- 通用连接池
- 协程 Mysql, Redis, RPC, HTTP 客户端
- 协程和同步阻塞客户端无缝自动切换
- 协程、异步任务投递
- 自定义用户进程
- RESTful 支持
- 国际化(i18n)支持
- 高性能路由
- 快速灵活的参数验证器
- 别名机制
- 强大的日志系统
- 跨平台热更新自动 Reload

# 文档
[**中文文档**](https://doc.swoft.org)

QQ交流群:548173319

# 环境要求
1. PHP 7.0 +
2. [Swoole 2.0.12](https://github.com/swoole/swoole-src/releases) +, 需开启协程和异步Redis
3. [Hiredis](https://github.com/redis/hiredis/releases)
4. [Composer](https://getcomposer.org/)

# 协议
Swoft的开源协议为apache 2.0，详情参见[LICENSE](LICENSE)。
