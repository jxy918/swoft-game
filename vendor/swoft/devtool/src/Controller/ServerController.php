<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Devtool\Controller;

use Swoft\App;
use Swoft\Bean\Collector\ServerListenerCollector;
use Swoft\Bean\Collector\SwooleListenerCollector;
use Swoft\Helper\ProcessHelper;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Annotation\RequestMethod;
use Swoft\Http\Server\Payload;

/**
 * Class ServerController
 * @Controller(prefix="/__devtool/server/")
 * @package Swoft\Devtool\Controller
 */
class ServerController
{
    /**
     * get server config
     * @RequestMapping(route="config", method=RequestMethod::GET)
     * @return array
     */
    public function config(): array
    {
        $info = [
            'swoole' => App::$server->getServer()->setting,
            'basic' => App::$server->getServerSetting(),
            'http' => App::$server->getHttpSetting(),
            'tcp' => App::$server->getTcpSetting(),
        ];

        if (\method_exists(App::$server, 'getWsSettings')) {
            $info['websocket'] = App::$server->getWsSettings();
        }

        return $info;
    }

    /**
     * get all registered events list
     * @RequestMapping(route="events", method=RequestMethod::GET)
     * @param Request $request
     * @return array
     */
    public function events(Request $request): array
    {
        // 1 server event
        // 2 swoole event
        $type = (int)$request->query('type');

        if ($type === 1) {
            return ServerListenerCollector::getCollector();
        }

        if ($type === 2) {
            return SwooleListenerCollector::getCollector();
        }

        return [
            'server' => ServerListenerCollector::getCollector(),
            'swoole' => SwooleListenerCollector::getCollector(),
        ];
    }

    /**
     * get php ext list
     * @RequestMapping(route="php-ext-list", method=RequestMethod::GET)
     * @return array
     */
    public function phpExt(): array
    {
        return \get_loaded_extensions();
    }

    /**
     * get swoole server stats
     * @RequestMapping(route="stats", method=RequestMethod::GET)
     * @return Payload
     */
    public function stats(): Payload
    {
        if (!App::$server) {
            return Payload::make(['msg' => 'server is not running'], 404);
        }

        $stat = App::$server->getServer()->stats();
        $stat['start_time'] = \date('Y-m-d H:i:s', $stat['start_time']);

        return Payload::make($stat);
    }

    /**
     * get crontab list
     * @RequestMapping(route="crontab", method=RequestMethod::GET)
     * @return array
     */
    public function crontab(): array
    {
        if (!App::hasBean('crontab')) {
            return [];
        }

        /** @var \Swoft\Task\Crontab\Crontab $cronTab */
        $cronTab = \bean('crontab');

        return $cronTab->getTasks();
    }

    /**
     * get swoole info
     * @RequestMapping(route="processes", method=RequestMethod::GET)
     * @return Payload
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function process(): Payload
    {
        list($code, $return, $error) = ProcessHelper::run('ps aux | grep swoft');

        if ($code) {
            return Payload::make(['msg' => $error], 404);
        }

        return Payload::make([
            'raw' => $return
        ]);
    }

    /**
     * get swoole info
     * @RequestMapping(route="swoole-info", method=RequestMethod::GET)
     * @return Payload
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function swoole(): Payload
    {
        list($code, $return, $error) = ProcessHelper::run('php --ri swoole');

        if ($code) {
            return Payload::make(['msg' => $error], 404);
        }

        // format
        $str = \str_replace("\r\n", "\n", \trim($return));
        list(, $enableStr, $directiveStr) = \explode("\n\n", $str);

        $directive = $this->formatSwooleInfo($directiveStr);
        \array_shift($directive);

        return Payload::make([
            'raw' => $return,
            'enable' => $this->formatSwooleInfo($enableStr),
            'directive' => $directive,
        ]);
    }

    /**
     * @param string $str
     * @return array
     */
    private function formatSwooleInfo(string $str): array
    {
        $data = [];
        $lines = \explode("\n", \trim($str));

        foreach ($lines as $line) {
            list($name, $value) = \explode(' => ', $line);
            $data[] = [
                'name' => $name,
                'value' => $value,
            ];
        }

        return $data;
    }
}
