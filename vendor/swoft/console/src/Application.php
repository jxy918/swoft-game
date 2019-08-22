<?php declare(strict_types=1);

namespace Swoft\Console;

use ReflectionException;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Concern\DataPropertyTrait;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Concern\RenderHelpInfoTrait;
use Swoft\Console\Contract\ConsoleInterface;
use Swoft\Console\Exception\CommandFlagException;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Console\Router\Router;
use Swoft\Stdlib\Helper\Arr;
use Swoft\Stdlib\Helper\ObjectHelper;
use Throwable;
use function array_merge;
use function implode;
use function strpos;
use function strtr;
use function trim;
use function ucfirst;

/**
 * Class Application
 *
 * @since 2.0
 * @Bean("cliApp")
 */
class Application implements ConsoleInterface
{
    use RenderHelpInfoTrait, DataPropertyTrait;

    // {$%s} name -> {name}
    protected const HELP_VAR_LEFT  = '{';
    protected const HELP_VAR_RIGHT = '}';

    /** @var array */
    private static $globalOptions = [
        '--debug'       => 'Setting the application runtime debug level(0 - 4)',
        // '--profile'     => 'Display timing and memory usage information',
        '--no-color'    => 'Disable color/ANSI for message output',
        '-h, --help'    => 'Display this help message',
        '-V, --version' => 'Show application version information',
    ];

    /**
     * @var Input
     */
    protected $input;

    /**
     * @var Output
     */
    protected $output;

    /**
     * @var string
     */
    private $name = 'My Application';

    /**
     * @var string
     */
    private $version = '2.0.0';

    /**
     * @var string
     */
    private $description = 'Console application description';

    /**
     * @var array
     */
    private $commentsVars = [];

    /**
     * Class constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        ObjectHelper::init($this, $options);
    }

    /**
     * Provides parsable parsing variables for command annotations.
     * Can be used in comments in commands.
     *
     * @return array
     */
    public function commentsVars(): array
    {
        $script = input()->getScript();

        return [
            'name'        => $this->getName(),
            'description' => $this->getDescription(),
            // 'group' => self::getName(),
            'workDir'     => \input()->getPwd(),
            'script'      => $script, // bin/app
            'binFile'     => $script,
            'command'     => \input()->getCommand(), // demo OR home:test
            'fullCmd'     => \input()->getFullCommand(),
            'fullCommand' => \input()->getFullCommand(),
        ];
    }

    protected function prepare(): void
    {
        $this->input  = \input();
        $this->output = \output();

        // load builtin comments vars
        $this->setCommentsVars($this->commentsVars());
    }

    /**
     * @return void
     * @throws ContainerException
     */
    public function run(): void
    {
        try {
            Swoft::trigger(ConsoleEvent::RUN_BEFORE, $this);

            // Prepare
            $this->prepare();

            // Get input command
            $inputCommand = $this->input->getCommand();

            if (!$inputCommand) {
                $this->filterSpecialOption();
            } else {
                $this->doRun($inputCommand);
            }

            Swoft::trigger(ConsoleEvent::RUN_AFTER, $this, $inputCommand);
        } catch (Throwable $e) {
            /** @var ConsoleErrorDispatcher $errDispatcher */
            $errDispatcher = BeanFactory::getSingleton(ConsoleErrorDispatcher::class);

            // Handle request error
            $errDispatcher->run($e);
        }
    }

    /**
     * @param string $inputCmd
     *
     * @return void
     * @throws ReflectionException
     * @throws ContainerException
     * @throws Throwable
     */
    protected function doRun(string $inputCmd): void
    {
        $output = $this->output;
        /* @var Router $router */
        $router = Swoft::getBean('cliRouter');
        $result = $router->match($inputCmd);

        // Command not found
        if ($result[0] === Router::NOT_FOUND) {
            $names = $router->getAllNames();
            $output->liteError("The entered command '{$inputCmd}' is not exists!");

            // find similar command names by similar_text()
            if ($similar = Arr::findSimilar($inputCmd, $names)) {
                $output->writef("\nMaybe what you mean is:\n    <info>%s</info>", implode(', ', $similar));
            } else {
                $this->showApplicationHelp(false);
            }
            return;
        }

        $info = $result[1];

        // Only input a group name, display help for the group
        if ($result[0] === Router::ONLY_GROUP) {
            $this->showGroupHelp($info['group']);
            return;
        }

        // Display help for a command
        if ($this->input->getSameOpt(['h', 'help'])) {
            $this->showCommandHelp($info);
            return;
        }

        // Parse default options and arguments
        $this->bindCommandFlags($info);
        $this->input->setCommandId($info['cmdId']);

        Swoft::triggerByArray(ConsoleEvent::DISPATCH_BEFORE, $this, $info);

        // Call command handler
        /** @var ConsoleDispatcher $dispatcher */
        $dispatcher = Swoft::getSingleton('cliDispatcher');
        $dispatcher->dispatch($info);

        Swoft::triggerByArray(ConsoleEvent::DISPATCH_AFTER, $this, $info);
    }

    /**
     * Filter special option. eg: -h, --help, --version
     *
     * @return void
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function filterSpecialOption(): void
    {
        // Version option resolution
        if ($this->input->getSameOpt(['V', 'version'], false)) {
            $this->showVersionInfo();
            return;
        }

        // Display application help, command list
        $this->showApplicationHelp(false);
    }

    /**
     * Bind option and argument values by route info
     *
     * @param array $info
     *
     * @throws CommandFlagException
     */
    protected function bindCommandFlags(array $info): void
    {
        // Bind options
        if ($opts = $info['options']) {
            $sOpts = $this->input->getSOpts();
            $lOpts = $this->input->getLOpts();

            foreach ($opts as $name => $opt) {
                $inputVal = $this->input->getLongOpt($name);

                // Exist short
                if (null === $inputVal && $opt['short']) {
                    $inputVal = $this->input->getShortOpt($opt['short']);
                }

                // Exist default value
                if (null === $inputVal && isset($opt['default'])) {
                    $inputVal = $opt['default'];
                }

                if (null !== $inputVal) {
                    $sOpts[$name] = $lOpts[$name] = $inputVal;

                    // Required check
                } elseif ($opt['mode'] === Command::OPT_REQUIRED) {
                    $short = $opt['short'] ? "(short: {$opt['short']})" : '';
                    throw new CommandFlagException(
                        "The option '{$name}'{$short} is required"
                    );
                }
            }

            // Save to input
            $this->input->setLOpts($lOpts, true);
            $this->input->setSOpts($sOpts, true);
        }

        // Bind named argument by index
        if ($args = $info['arguments']) {
            $index  = 0;
            $values = $this->input->getArgs();

            foreach ($args as $name => $arg) {
                // Bind value to name
                if (isset($values[$index])) {
                    $values[$name] = $values[$index];
                    // Bind default value
                } elseif (isset($arg['default'])) {
                    $values[$name]  = $arg['default'];
                    $values[$index] = $arg['default'];
                }

                // Check arg is required
                if ($arg['mode'] === Command::ARG_REQUIRED && empty($values[$name])) {
                    throw new CommandFlagException(
                        "The argument '{$name}'(position: {$index}) is required"
                    );
                }

                $index++;
            }

            $this->input->setArgs($values, true);
        }
    }

    /**
     * Replace the variable in the comment with the corresponding value
     *
     * @param string $str
     * @param array  $vars
     *
     * @return string
     */
    protected function parseCommentsVars(string $str, array $vars): string
    {
        // not use vars
        if (false === strpos($str, self::HELP_VAR_LEFT)) {
            return $str;
        }

        $map = [];
        foreach ($vars as $key => $value) {
            $key = self::HELP_VAR_LEFT . $key . self::HELP_VAR_RIGHT;
            // save
            $map[$key] = $value;
        }

        return $map ? strtr($str, $map) : $str;
    }

    /**
     * @return array
     */
    public function getCommentsVars(): array
    {
        return $this->commentsVars;
    }

    /**
     * @param array $vars
     */
    public function setCommentsVars(array $vars): void
    {
        if ($vars) {
            $this->commentsVars = array_merge($this->commentsVars, $vars);
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ? ucfirst($this->description) : '';
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = trim($description);
    }
}
