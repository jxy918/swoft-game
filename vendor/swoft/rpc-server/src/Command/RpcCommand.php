<?php

namespace Swoft\Rpc\Server\Command;

use Swoft\Console\Bean\Annotation\Command;
use Swoft\Rpc\Server\Rpc\RpcServer;

/**
 * The group command list of rpc server
 * @Command(coroutine=false,server=true)
 */
class RpcCommand
{
    /**
     * start rpc server
     *
     * @Usage {fullCommand} [-d|--daemon]
     * @Options
     *   -d, --daemon    Run server on the background
     * @Example
     *   {fullCommand}
     *   {fullCommand} -d
     */
    public function start()
    {
        $rpcServer = $this->getRpcServer();

        // 是否正在运行
        if ($rpcServer->isRunning()) {
            $serverStatus = $rpcServer->getServerSetting();
            \output()->writeln("<error>The server have been running!(PID: {$serverStatus['masterPid']})</error>", true, true);
        }

        // 选项参数解析
        $this->setStartArgs($rpcServer);
        $tcpStatus = $rpcServer->getTcpSetting();

        // tcp启动参数
        $tcpHost = $tcpStatus['host'];
        $tcpPort = $tcpStatus['port'];
        $tcpType = $tcpStatus['type'];
        $tcpMode = $tcpStatus['mode'];

        // 信息面板
        $lines = [
            '                    Information Panel                     ',
            '*************************************************************',
            "* tcp | Host: <note>$tcpHost</note>, port: <note>$tcpPort</note>, mode: <note>$tcpMode</note>, type: <note>$tcpType</note>",
            '*************************************************************',
        ];
        \output()->writeln(implode("\n", $lines));

        // 启动
        $rpcServer->start();
    }

    /**
     * reload worker process
     *
     * @Usage
     *   {fullCommand} [arguments] [options]
     * @Options
     *   -t     Only to reload task processes, default to reload worker and task
     * @Example
     * php swoft.php rpc:reload
     */
    public function reload()
    {
        $rpcServer = $this->getRpcServer();

        // 是否已启动
        if (! $rpcServer->isRunning()) {
            output()->writeln('<error>The server is not running! cannot reload</error>', true, true);
        }

        // 打印信息
        output()->writeln(sprintf('<info>Server %s is reloading ...</info>', input()->getFullScript()));

        // 重载
        $reloadTask = input()->hasOpt('t');
        $rpcServer->reload($reloadTask);
        output()->writeln(sprintf('<success>Server %s is reload success</success>', input()->getFullScript()));
    }

    /**
     * stop rpc server
     *
     * @Usage {fullCommand}
     * @Example {fullCommand}
     */
    public function stop()
    {
        $rpcServer = $this->getRpcServer();

        // 是否已启动
        if (! $rpcServer->isRunning()) {
            \output()->writeln('<error>The server is not running! cannot stop</error>', true, true);
        }

        // pid文件
        $serverStatus = $rpcServer->getServerSetting();
        $pidFile = $serverStatus['pfile'];

        @unlink($pidFile);
        \output()->writeln(sprintf('<info>Swoft %s is stopping ...</info>', input()->getFullScript()));

        $result = $rpcServer->stop();

        // 停止失败
        if (! $result) {
            \output()->writeln(sprintf('<error>Swoft %s stop fail</error>', input()->getFullScript()));
        }

        \output()->writeln(sprintf('<success>Swoft %s stop success</success>', input()->getFullScript()));
    }

    /**
     * restart rpc server
     *
     * @Usage {fullCommand}
     * @Options
     *   -d, --daemon    Run server on the background
     * @Example
     *   {fullCommand}
     *   {fullCommand} -d
     */
    public function restart()
    {
        $rpcServer = $this->getRpcServer();

        // 是否已启动
        if ($rpcServer->isRunning()) {
            $this->stop();
        }

        // 重启默认是守护进程
        $rpcServer->setDaemonize();
        $this->start();
    }

    /**
     * @return RpcServer
     */
    private function getRpcServer(): RpcServer
    {
        $script = \input()->getScript();
        $rpcServer = new RpcServer();
        $rpcServer->setScriptFile($script);
        
        return $rpcServer;
    }

    /**
     * @param RpcServer $rpcServer
     */
    private function setStartArgs(RpcServer $rpcServer)
    {
        if (\input()->getSameOpt(['d', 'daemon'], false)) {
            $rpcServer->setDaemonize();
        }
    }
}
