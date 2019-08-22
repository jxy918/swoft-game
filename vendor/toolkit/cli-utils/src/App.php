<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-15
 * Time: 10:51
 */

namespace Toolkit\Cli;

use InvalidArgumentException;
use RuntimeException;
use Throwable;
use function array_merge;
use function array_shift;
use function array_values;
use function class_exists;
use function function_exists;
use function getcwd;
use function implode;
use function is_array;
use function is_object;
use function is_string;
use function ksort;
use function method_exists;
use function str_pad;
use function strlen;
use function strpos;
use function strtr;
use function trim;
use function ucfirst;

/**
 * Class App - A lite CLI Application
 *
 * @package Inhere\Console
 */
class App
{
    private const COMMAND_CONFIG = [
        'desc'  => '',
        'usage' => '',
        'help'  => '',
    ];

    /** @var string Current dir */
    private $pwd;

    /**
     * @var array Parsed from `arg0 name=val var2=val2`
     */
    private $args = [];

    /**
     * @var array Parsed from `--name=val --var2=val2 -d`
     */
    private $opts = [];

    /**
     * @var string
     */
    private $script;

    /**
     * @var string
     */
    private $command = '';

    /**
     * @var array User add commands
     */
    private $commands = [];

    /**
     * @var array Command messages for the commands
     */
    private $messages = [];

    /**
     * @var int
     */
    private $keyWidth = 12;

    /**
     * Class constructor.
     *
     * @param array|null $argv
     */
    public function __construct(array $argv = null)
    {
        // get current dir
        $this->pwd = getcwd();

        // parse cli argv
        $argv = $argv ?? (array)$_SERVER['argv'];

        // get script file
        $this->script = array_shift($argv);

        // parse flags
        [$this->args, $this->opts] = Flags::parseArgv($argv, [
            'mergeOpts' => true
        ]);
    }

    /**
     * @param bool $exit
     *
     * @throws InvalidArgumentException
     */
    public function run(bool $exit = true): void
    {
        $this->findCommand();

        $this->dispatch($exit);
    }

    /**
     * find command name. it is first argument.
     */
    protected function findCommand(): void
    {
        if (!isset($this->args[0])) {
            return;
        }

        $newArgs = [];

        foreach ($this->args as $key => $value) {
            if ($key === 0) {
                $this->command = trim($value);
            } elseif (is_int($key)) {
                $newArgs[] = $value;
            } else {
                $newArgs[$key] = $value;
            }
        }

        $this->args = $newArgs;
    }

    /**
     * @param bool $exit
     *
     * @throws InvalidArgumentException
     */
    public function dispatch(bool $exit = true): void
    {
        if (!$command = $this->command) {
            $this->displayHelp();
            return;
        }

        if (!isset($this->commands[$command])) {
            $this->displayHelp("The command '{$command}' is not exists!");
            return;
        }

        if (isset($this->opts['h']) || isset($this->opts['help'])) {
            $this->displayCommandHelp($command);
            return;
        }

        try {
            $status = $this->runHandler($command, $this->commands[$command]);
        } catch (Throwable $e) {
            $status = $this->handleException($e);
        }

        if ($exit) {
            $this->stop($status);
        }
    }

    /**
     * @param int $code
     */
    public function stop($code = 0): void
    {
        exit((int)$code);
    }

    /**
     * @param string $command
     * @param        $handler
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function runHandler(string $command, $handler)
    {
        if (is_string($handler)) {
            // function name
            if (function_exists($handler)) {
                return $handler($this);
            }

            if (class_exists($handler)) {
                $handler = new $handler;

                // $handler->execute()
                if (method_exists($handler, 'execute')) {
                    return $handler->execute($this);
                }
            }
        }

        // a \Closure OR $handler->__invoke()
        if (is_object($handler) && method_exists($handler, '__invoke')) {
            return $handler($this);
        }

        throw new RuntimeException("Invalid handler of the command: $command");
    }

    /**
     * @param Throwable $e
     *
     * @return int
     */
    protected function handleException(Throwable $e): int
    {
        if ($e instanceof InvalidArgumentException) {
            Color::println('ERROR: ' . $e->getMessage(), 'error');
            return 0;
        }

        $code = $e->getCode() !== 0 ? $e->getCode() : -1;
        $eTpl = "Exception(%d): %s\nFile: %s(Line %d)\nTrace:\n%s\n";

        // print exception message
        printf($eTpl, $code, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

        return $code;
    }

    /**
     * @param callable $handler
     * @param array    $config
     */
    public function addByConfig(callable $handler, array $config): void
    {
        $this->addCommand($config['name'], $handler, $config);
    }

    /**
     * @param string            $command
     * @param callable          $handler
     * @param null|array|string $config
     */
    public function add(string $command, callable $handler, $config = null): void
    {
        $this->addCommand($command, $handler, $config);
    }

    /**
     * @param string            $command
     * @param callable          $handler
     * @param null|array|string $config
     */
    public function addCommand(string $command, callable $handler, $config = null): void
    {
        if (!$command || !$handler) {
            throw new InvalidArgumentException('Invalid arguments for add command');
        }

        if (($len = strlen($command)) > $this->keyWidth) {
            $this->keyWidth = $len;
        }

        $this->commands[$command] = $handler;

        if (is_string($config)) {
            $desc   = trim($config);
            $config = self::COMMAND_CONFIG;

            // append desc
            $config['desc'] = $desc;

            // save
            $this->messages[$command] = $config;
        } elseif (is_array($config)) {
            $this->messages[$command] = array_merge(self::COMMAND_CONFIG, $config);
        }
    }

    /**
     * @param array $commands
     *
     * @throws InvalidArgumentException
     */
    public function commands(array $commands): void
    {
        foreach ($commands as $command => $handler) {
            $desc = '';

            if (is_array($handler)) {
                $conf    = array_values($handler);
                $handler = $conf[0];
                $desc    = $conf[1] ?? '';
            }

            $this->addCommand($command, $handler, $desc);
        }
    }

    /****************************************************************************
     * helper methods
     ****************************************************************************/

    /**
     * @param string $err
     */
    public function displayHelp(string $err = ''): void
    {
        if ($err) {
            echo Color::render("<red>ERROR</red>: $err\n\n");
        }

        // help
        $len  = $this->keyWidth;
        $help = "Welcome to the Lite Console Application.\n\n<comment>Available Commands:</comment>\n";
        $data = $this->messages;
        ksort($data);

        foreach ($data as $command => $item) {
            $command = str_pad($command, $len, ' ');
            $desc    = $item['desc'] ? ucfirst($item['desc']) : 'No description for the command';
            $help    .= "  $command   $desc\n";
        }

        echo Color::render($help) . PHP_EOL;
        exit(0);
    }

    /**
     * @param string $name
     */
    public function displayCommandHelp(string $name): void
    {
        $checkVar = false;
        $fullCmd  = $this->script . " $name";

        $config = $this->messages[$name] ?? [];
        $usage  = "$fullCmd [args ...] [--opts ...]";

        if (!$config) {
            $nodes = [
                'No description for the command',
                "<comment>Usage:</comment> \n  $usage"
            ];
        } else {
            $checkVar = true;
            $userHelp = $config['help'];

            $nodes = [
                ucfirst($config['desc']),
                "<comment>Usage:</comment> \n  " . ($config['usage'] ?: $usage),
                $userHelp ? $userHelp . "\n" : ''
            ];
        }

        $help = implode("\n", $nodes);

        if ($checkVar && strpos($help, '{{')) {
            $help = strtr($help, [
                '{{command}}' => $name,
                '{{fullCmd}}' => $fullCmd,
                '{{workDir}}' => $this->pwd,
                '{{pwdDir}}'  => $this->pwd,
                '{{script}}'  => $this->script,
            ]);
        }

        echo Color::render($help);
    }

    /**
     * @param string|int $name
     * @param mixed      $default
     *
     * @return mixed|null
     */
    public function getArg($name, $default = null)
    {
        return $this->args[$name] ?? $default;
    }

    /**
     * @param string|int $name
     * @param int        $default
     *
     * @return int
     */
    public function getIntArg($name, int $default = 0): int
    {
        return (int)$this->getArg($name, $default);
    }

    /**
     * @param string|int $name
     * @param string     $default
     *
     * @return string
     */
    public function getStrArg($name, string $default = ''): string
    {
        return (string)$this->getArg($name, $default);
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function getOpt(string $name, $default = null)
    {
        return $this->opts[$name] ?? $default;
    }

    /**
     * @param string $name
     * @param int    $default
     *
     * @return int
     */
    public function getIntOpt(string $name, int $default = 0): int
    {
        return (int)$this->getOpt($name, $default);
    }

    /**
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public function getStrOpt(string $name, string $default = ''): string
    {
        return (string)$this->getOpt($name, $default);
    }

    /**
     * @param string $name
     * @param bool   $default
     *
     * @return bool
     */
    public function getBoolOpt(string $name, bool $default = false): bool
    {
        return (bool)$this->getOpt($name, $default);
    }

    /****************************************************************************
     * getter/setter methods
     ****************************************************************************/

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    /**
     * @return array
     */
    public function getOpts(): array
    {
        return $this->opts;
    }

    /**
     * @param array $opts
     */
    public function setOpts(array $opts): void
    {
        $this->opts = $opts;
    }

    /**
     * @return string
     */
    public function getScript(): string
    {
        return $this->script;
    }

    /**
     * @param string $script
     */
    public function setScript(string $script): void
    {
        $this->script = $script;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @param string $command
     */
    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @param array $commands
     */
    public function setCommands(array $commands): void
    {
        $this->commands = $commands;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param array $messages
     */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    /**
     * @return int
     */
    public function getKeyWidth(): int
    {
        return $this->keyWidth;
    }

    /**
     * @param int $keyWidth
     */
    public function setKeyWidth(int $keyWidth): void
    {
        $this->keyWidth = $keyWidth > 1 ? $keyWidth : 12;
    }

    /**
     * @return string
     */
    public function getPwd(): string
    {
        return $this->pwd;
    }

}
