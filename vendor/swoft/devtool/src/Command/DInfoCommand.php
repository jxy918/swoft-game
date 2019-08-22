<?php declare(strict_types=1);

namespace Swoft\Devtool\Command;

use ReflectionException;
use RuntimeException;
use Swoft;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandOption;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Devtool\DevTool;
use Swoft\Http\Server\Router\Router;
use Swoft\Stdlib\Helper\Sys;
use Swoole\Coroutine;
use function alias;
use function array_shift;
use function explode;
use function strpos;
use function trim;

/**
 * There are some commands for application dev[by <cyan>devtool</cyan>]
 * @Command("dinfo", coroutine=false)
 */
class DInfoCommand
{
    /**
     * Print current system environment information
     *
     * @CommandMapping(alias="sys")
     *
     * @param Output $output
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function env(Output $output): void
    {
        $info = [
            // "<bold>System environment info</bold>\n",
            'Operating system' => PHP_OS,
            'Php version'      => PHP_VERSION,
            'Swoole version'   => SWOOLE_VERSION,
            'Swoft version'    => Swoft::VERSION,
            'Application Name' => config('name', 'unknown'),
            'Project Path'     => alias('@base'),
            'Runtime Path'     => alias('@runtime'),
        ];

        $output->aList($info, 'System Environment');
    }

    /**
     * display info for the swoole extension
     *
     * @CommandMapping(alias="swo,sw")
     * @param Output $output
     */
    public function swoole(Output $output): void
    {
        [$zero, $ret,] = Sys::run('php --ri swoole');

        // no swoole ext
        if ($zero !== 0) {
            $output->error(trim($ret));
            return;
        }

        $info = $dirt = [];
        $list = explode("\n\n", $ret);

        $information = explode("\n", $list[1]);
        foreach ($information as $line) {
            $info[] = explode(' => ', $line, 2);
            // $info[$k] = $v;
        }

        $directives = explode("\n", trim($list[2]));
        array_shift($directives);

        foreach ($directives as $line) {
            $dirt[] = explode(' => ', $line, 2);
        }

        $output->title('information for the swoole extension');
        $output->table($info, 'basic information', [
            'columns'   => ['name', 'value'],
            'bodyStyle' => 'info'
        ]);

        $output->table($dirt, 'directive config', [
            'columns' => ['Directive', 'Local Value => Master Value']
        ]);
    }

    /**
     * Check current operating environment information
     *
     * @CommandMapping()
     * @param Output $output
     *
     * @throws RuntimeException
     */
    public function check(Output $output): void
    {
        // Env check
        [$code, $return,] = Sys::run('php --ri swoole');
        $asyncRdsEnabled = $code === 0 ? strpos($return, 'async_redis') : false;

        $swoVer = SWOOLE_VERSION;
        $tipMsg = 'Please disabled it, otherwise swoole will be affected!';
        $extOpt = [
            'yes' => 'No',
            'no'  => 'Yes',
        ];

        $list = [
            "<bold>Runtime environment check</bold>\n",
            'PHP version is greater than 7.1?'    => self::wrap(PHP_VERSION_ID > 70100, 'current is ' . PHP_VERSION),
            'Swoole extension is installed?'      => self::wrap(extension_loaded('swoole')),
            'Swoole version is greater than 4.3?' => self::wrap(version_compare($swoVer, '4.3.0', '>='), 'current is ' . $swoVer),
            'Swoole async redis is enabled?'      => self::wrap($asyncRdsEnabled),
            'Swoole coroutine is enabled?'        => self::wrap(class_exists(Coroutine::class, false)),
            "\n<bold>Extensions that conflict with 'swoole'</bold>\n",
            // ' extensions'                             => 'installed',
            ' - zend'                             => self::wrap(!extension_loaded('zend'), $tipMsg, true, $extOpt),
            ' - xdebug'                           => self::wrap(!extension_loaded('xdebug'), $tipMsg, true, $extOpt),
            ' - xhprof'                           => self::wrap(!extension_loaded('xhprof'), $tipMsg, true, $extOpt),
            ' - blackfire'                        => self::wrap(!extension_loaded('blackfire'), $tipMsg, true, $extOpt),
        ];

        $buffer = [];
        $pass   = $total = 0;

        foreach ($list as $question => $value) {
            if (is_int($question)) {
                $buffer[] = $value;
                continue;
            }

            $total++;

            if ($value[0]) {
                $pass++;
            }

            $question = str_pad($question, 45);
            $buffer[] = sprintf('  <comment>%s</comment> %s', $question, $value[1]);
        }

        $buffer[] = "\nCheck total: <bold>$total</bold>, Through the check: <success>$pass</success>";

        $output->writeln($buffer);
    }

    /**
     * @param bool|mixed  $condition
     * @param string|null $msg
     * @param bool        $showOnFalse
     *
     * @param array       $opts
     *
     * @return array
     */
    public static function wrap($condition, string $msg = null, $showOnFalse = false, array $opts = []): array
    {
        $desc = '';
        $opts = array_merge([
            'yes' => 'Yes',
            'no'  => 'No',
        ], $opts);

        $result = $condition ? "<success>{$opts['yes']}</success>" : "<red>{$opts['yes']}</red>";

        if ($msg) {
            if ($showOnFalse) {
                $desc = !$condition ? " ($msg)" : '';
            } else {
                $desc = " ($msg)";
            }
        }

        return [(bool)$condition, $result . $desc];
    }
}
