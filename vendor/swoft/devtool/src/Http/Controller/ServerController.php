<?php declare(strict_types=1);

namespace Swoft\Devtool\Http\Controller;

use Swoft\Helper\ProcessHelper;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Stdlib\Helper\Sys;

/**
 * Class ServerController
 *
 * @Controller(prefix="/__devtool/server/")
 */
class ServerController
{
    /**
     * Get server config
     * @RequestMapping(route="config", method=RequestMethod::GET)
     * @return array
     */
    public function config(): array
    {
        return [
            'swoole' => \server()->getSetting(),
            'server' => \server()->getTypeName(),
        ];
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
     * Get php extensions list
     *
     * @RequestMapping(route="php-exts", method=RequestMethod::GET)
     * @return array
     */
    public function phpExt(): array
    {
        return \get_loaded_extensions();
    }

    /**
     * Get swoole server stats
     * @RequestMapping(route="stats", method=RequestMethod::GET)
     * @return array
     */
    public function stats(): array
    {
        if (!\server()) {
            return ['msg' => 'server is not running'];
        }

        $stat = \server()->getSwooleStats();
        // start date
        $stat['start_date'] = \date('Y-m-d H:i:s', $stat['start_time']);

        return $stat;
    }

    /**
     * get crontab list
     * @RequestMapping(route="crontab", method=RequestMethod::GET)
     * @return array
     * @throws \Throwable
     */
    public function cronTab(): array
    {
        if (!\Swoft::hasBean('crontab')) {
            return [];
        }

        /** @var \Swoft\Task\Crontab\Crontab $cronTab */
        $cronTab = \bean('crontab');

        return $cronTab->getTasks();
    }

    /**
     * get swoole info
     * @RequestMapping(route="processes", method=RequestMethod::GET)
     * @return array
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function process(): array
    {
        [$code, $return, $error] = Sys::run('ps aux | grep swoft');
        if ($code) {
            return ['code' => 404, 'msg' => $error];
        }

        return [
            'raw' => $return
        ];
    }

    /**
     * Get swoole info
     * @RequestMapping(route="swoole-info", method=RequestMethod::GET)
     * @return array
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function swoole(): array
    {
        [$code, $return, $error] = Sys::run('php --ri swoole');
        if ($code) {
            return ['code' => 404, 'msg' => $error];
        }

        // format
        $str = \str_replace("\r\n", "\n", \trim($return));
        [, $enableStr, $directiveStr] = \explode("\n\n", $str);

        $directive = $this->formatSwooleInfo($directiveStr);
        \array_shift($directive);

        return [
            'raw'       => $return,
            'enable'    => $this->formatSwooleInfo($enableStr),
            'directive' => $directive,
        ];
    }

    /**
     * @param string $str
     * @return array
     */
    private function formatSwooleInfo(string $str): array
    {
        $data  = [];
        $lines = \explode("\n", \trim($str));

        foreach ($lines as $line) {
            [$name, $value] = \explode(' => ', $line);
            $data[] = [
                'name'  => $name,
                'value' => $value,
            ];
        }

        return $data;
    }
}
