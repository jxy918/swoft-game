<?php

namespace Swoft\WebSocket\Server\Command;

use Swoft\Console\Bean\Annotation\Command;
use Swoft\Helper\EnvHelper;
use Swoft\WebSocket\Server\WebSocketServer;

/**
 * There are some commands for manage the webSocket server
 * @Command(coroutine=false, server=true)
 * @package Swoft\WebSocket\Command
 */
class WsCommand
{
    /**
     * Start the webSocket server
     *
     * @Usage {fullCommand} [-d|--daemon]
     * @Options
     *   -d, --daemon    Run server on the background
     * @Example
     *   {fullCommand}
     *   {fullCommand} -d
     * @throws \InvalidArgumentException
     * @throws \Swoft\Exception\RuntimeException
     * @throws \RuntimeException
     */
    public function start()
    {
        $server = $this->createServerManager();

        // 是否正在运行
        if ($server->isRunning()) {
            $serverOpts = $server->getServerSetting();

            \output()->writeln("<error>The server have been running!(PID: {$serverOpts['masterPid']})</error>", true, true);
        } else {
            $serverOpts = $server->getServerSetting();
        }

        // 启动参数
        $this->configServer($server);
        $ws = $server->getWsSettings();
        $tcp = $server->getTcpSetting();

        // Setting
        $workerNum = $server->setting['worker_num'];

        // Ws(http) 启动参数
        $wsHost = $ws['host'];
        $wsPort = $ws['port'];
        $wsMode = $ws['mode'];
        $wsType = $ws['type'];
        $httpStatus = $ws['enable_http'] ? '<info>Enabled</info>' : '<warning>Disabled</warning>';

        // TCP 启动参数
        $tcpHost = $tcp['host'];
        $tcpPort = $tcp['port'];
        $tcpType = $tcp['type'];
        $tcpStatus = $serverOpts['tcpable'] ? '<info>Enabled</info>' : '<warning>Disabled</warning>';

        // 信息面板
        $lines = [
            '                                 Server Information                     ',
            '************************************************************************************',
            "* WS   | host: <note>$wsHost</note>, port: <note>$wsPort</note>, type: <note>$wsType</note>, worker: <note>$workerNum</note>, mode: <note>$wsMode</note> (http is $httpStatus)",
            "* TCP  | host: <note>$tcpHost</note>, port: <note>$tcpPort</note>, type: <note>$tcpType</note>, worker: <note>$workerNum</note> ($tcpStatus)",
            '************************************************************************************',
        ];

        // 启动服务器
        \output()->writeln(implode("\n", $lines));
        $server->start();
    }

    /**
     * Reload worker processes for the running server
     *
     * @Usage {fullCommand} [-t]
     * @Options
     *  -t      Only to reload task processes, default to reload worker and task
     * @Example {fullCommand}
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function reload()
    {
        $server = $this->createServerManager();

        if (!$server->isRunning()) {
            \output()->writeln('<error>The server is not running! cannot reload</error>', true, true);
        }

        \output()->writeln(sprintf('<info>Server %s is reloading</info>', input()->getScript()));

        $reloadTask = input()->hasOpt('t');
        $server->reload($reloadTask);
        \output()->writeln(sprintf('<success>Server %s reload success</success>', input()->getScript()));
    }

    /**
     * Stop the running server
     *
     * @Usage {fullCommand}
     * @Example {fullCommand}
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function stop()
    {
        $server = $this->createServerManager();

        // 是否已启动
        if (!$server->isRunning()) {
            \output()->writeln('<error>The server is not running! cannot stop</error>', true, true);
        }

        // pid文件
        $serverOpts = $server->getServerSetting();
        $pidFile = $serverOpts['pfile'];

        @unlink($pidFile);
        \output()->writeln(sprintf('<info>Swoft %s is stopping ...</info>', input()->getScript()));

        $result = $server->stop();

        // 停止失败
        if (!$result) {
            \output()->writeln(sprintf('<error>Swoft %s stop fail</error>', input()->getScript()), true, true);
        }

        \output()->writeln(sprintf('<success>Swoft %s stop success!</success>', input()->getScript()));
    }

    /**
     * Restart the running server
     *
     * @Usage {fullCommand} [-d|--daemon]
     * @Options
     *   -d, --daemon    Run server on the background
     * @Example
     *   {fullCommand}
     *   {fullCommand} -d
     * @throws \Swoft\Exception\RuntimeException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function restart()
    {
        $server = $this->createServerManager();

        // 是否已启动
        if ($server->isRunning()) {
            $this->stop();
        }

        // 重启默认是守护进程
        $server->setDaemonize();
        $this->start();
    }

    /**
     * @return WebSocketServer
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    private function createServerManager(): WebSocketServer
    {
        // check env
        EnvHelper::check();

        // http server初始化
        $script = input()->getScript();

        $server = new WebSocketServer();
        $server->setScriptFile($script);

        return $server;
    }

    /**
     * 设置启动选项，覆盖 config/server.php 配置选项
     *
     * @param WebSocketServer $server
     */
    private function configServer(WebSocketServer $server)
    {
        if (\input()->getSameOpt(['d', 'daemon'], false)) {
            $server->setDaemonize();
        }
    }
}

