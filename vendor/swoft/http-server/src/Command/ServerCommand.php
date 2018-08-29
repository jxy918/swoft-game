<?php

namespace Swoft\Http\Server\Command;

use Swoft\Console\Bean\Annotation\Command;
use Swoft\Helper\EnvHelper;
use Swoft\Http\Server\Http\HttpServer;

/**
 * The group command list of HTTP-Server
 *
 * @Command(coroutine=false,server=true)
 */
class ServerCommand
{
    /**
     * Start http server
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
        $httpServer = $this->getHttpServer();

        // 是否正在运行
        if ($httpServer->isRunning()) {
            $serverStatus = $httpServer->getServerSetting();

            \output()->writeln("<error>The server have been running!(PID: {$serverStatus['masterPid']})</error>", true, true);
        } else {
            $serverStatus = $httpServer->getServerSetting();
        }

        // 启动参数
        $this->setStartArgs($httpServer);
        $httpStatus = $httpServer->getHttpSetting();
        $tcpStatus = $httpServer->getTcpSetting();

        // Setting
        $workerNum = $httpServer->setting['worker_num'];

        // HTTP 启动参数
        $httpHost = $httpStatus['host'];
        $httpPort = $httpStatus['port'];
        $httpMode = $httpStatus['mode'];
        $httpType = $httpStatus['type'];

        // TCP 启动参数
        $tcpEnable = $serverStatus['tcpable'];
        $tcpHost = $tcpStatus['host'];
        $tcpPort = $tcpStatus['port'];
        $tcpType = $tcpStatus['type'];
        $tcpEnable = $tcpEnable ? '<info>Enabled</info>' : '<warning>Disabled</warning>';

        // 信息面板
        $lines = [
            '                         Server Information                      ',
            '********************************************************************',
            "* HTTP | host: <note>$httpHost</note>, port: <note>$httpPort</note>, type: <note>$httpType</note>, worker: <note>$workerNum</note>, mode: <note>$httpMode</note>",
            "* TCP  | host: <note>$tcpHost</note>, port: <note>$tcpPort</note>, type: <note>$tcpType</note>, worker: <note>$workerNum</note> ($tcpEnable)",
            '********************************************************************',
        ];

        // 启动服务器
        \output()->writeln(implode("\n", $lines));
        $httpServer->start();
    }

    /**
     * Reload worker processes
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
        $httpServer = $this->getHttpServer();

        // 是否已启动
        if (!$httpServer->isRunning()) {
            output()->writeln('<error>The server is not running! cannot reload</error>', true, true);
        }

        output()->writeln(sprintf('<info>Server %s is reloading</info>', input()->getScript()));

        // 重载
        $reloadTask = input()->hasOpt('t');
        $httpServer->reload($reloadTask);
        output()->writeln(sprintf('<success>Server %s reload success</success>', input()->getScript()));
    }

    /**
     * Stop the http server
     *
     * @Usage {fullCommand}
     * @Example {fullCommand}
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function stop()
    {
        $httpServer = $this->getHttpServer();

        // 是否已启动
        if (!$httpServer->isRunning()) {
            \output()->writeln('<error>The server is not running! cannot stop</error>', true, true);
        }

        // pid文件
        $serverStatus = $httpServer->getServerSetting();
        $pidFile = $serverStatus['pfile'];

        @unlink($pidFile);
        \output()->writeln(sprintf('<info>Swoft %s is stopping ...</info>', input()->getScript()));

        $result = $httpServer->stop();

        // 停止失败
        if (!$result) {
            \output()->writeln(sprintf('<error>Swoft %s stop fail</error>', input()->getScript()), true, true);
        }

        output()->writeln(sprintf('<success>Swoft %s stop success!</success>', input()->getScript()));
    }

    /**
     * Restart the http server
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
        $httpServer = $this->getHttpServer();

        // 是否已启动
        if ($httpServer->isRunning()) {
            $this->stop();
        }

        // 重启默认是守护进程
        $httpServer->setDaemonize();
        $this->start();
    }

    /**
     * @return HttpServer
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    private function getHttpServer(): HttpServer
    {
        // check env
        EnvHelper::check();

        // http server初始化
        $script = input()->getScript();

        $httpServer = new HttpServer();
        $httpServer->setScriptFile($script);

        return $httpServer;
    }

    /**
     * 设置启动选项，覆盖 config/server.php 配置选项
     *
     * @param HttpServer $httpServer
     */
    private function setStartArgs(HttpServer $httpServer)
    {
        $daemonize = \input()->getSameOpt(['d', 'daemon'], false);

        if ($daemonize) {
            $httpServer->setDaemonize();
        }
    }
}
