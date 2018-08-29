<?php

namespace Swoft\Devtool\Command;

use Swoft\App;
use Swoft\Console\Bean\Annotation\Command;
use Swoft\Console\Helper\ConsoleUtil;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Devtool\FileGenerator;

/**
 * Generate some common application template classes[<cyan>built-in</cyan>]
 * @Command(coroutine=false)
 * @package Swoft\Devtool\Command
 */
class GenCommand
{
    /**
     * @var string
     */
    public $defaultTplPath;

    public function init()
    {
        $this->defaultTplPath = \dirname(__DIR__, 2) . '/res/templates/';
    }

    /**
     * Generate CLI command controller class
     * @Usage {fullCommand} CLASS_NAME SAVE_DIR [--option ...]
     * @Arguments
     *   name       The class name, don't need suffix and ext.(eg. <info>demo</info>)
     *   dir        The class file save dir(default: <info>@app/Commands</info>)
     * @Options
     *   -y, --yes BOOL             No need to confirm when performing file writing. default is: <info>False</info>
     *   -o, --override BOOL        Force override exists file. default is: <info>False</info>
     *   -n, --namespace STRING     The class namespace. default is: <info>App\Commands</info>
     *   --suffix STRING            The class name suffix. default is: <info>Command</info>
     *   --tpl-file STRING          The template file name. default is: <info>command.stub</info>
     *   --tpl-dir STRING           The template file dir path.(default: devtool/res/templates)
     * @Example
     *   <info>{fullCommand} demo</info>     Gen DemoCommand class to `@app/Commands`
     * @param Input $in
     * @param Output $out
     * @return int
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    public function command(Input $in, Output $out): int
    {
        list($config, $data) = $this->collectInfo($in, $out, [
            'suffix' => 'Command',
            'namespace' => 'App\\Commands',
            'tplFilename' => 'command',
        ]);

        $data['commandVar'] = '{command}';

        return $this->writeFile('@app/Commands', $data, $config, $out);
    }

    /**
     * Generate HTTP controller class
     * @Usage {fullCommand} CLASS_NAME SAVE_DIR [--option ...]
     * @Arguments
     *   name       The class name, don't need suffix and ext.(eg. <info>demo</info>)
     *   dir        The class file save dir(default: <info>@app/Controllers</info>)
     * @Options
     *   -y, --yes BOOL             No need to confirm when performing file writing. default is: <info>False</info>
     *   -o, --override BOOL        Force override exists file. default is: <info>False</info>
     *   -n, --namespace STRING     The class namespace. default is: <info>App\Controllers</info>
     *   --rest BOOL                The class will contains CURD action. default is: <info>False</info>
     *   --prefix STRING            The route prefix for the controller. default is class name
     *   --suffix STRING            The class name suffix. default is: <info>Controller</info>
     *   --tpl-file STRING          The template file name. default is: <info>command.stub</info>
     *   --tpl-dir STRING           The template file dir path.(default: devtool/res/templates)
     * @Example
     *   <info>{fullCommand} demo --prefix /demo -y</info>          Gen DemoController class to `@app/Controllers`
     *   <info>{fullCommand} user --prefix /users --rest</info>     Gen UserController class to `@app/Controllers`(RESTFul type)
     * @return int
     * @param Input $in
     * @param Output $out
     * @return int
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    public function controller(Input $in, Output $out): int
    {
        list($config, $data) = $this->collectInfo($in, $out, [
            'suffix' => 'Controller',
            'namespace' => 'App\\Controllers',
            'tplFilename' => 'controller',
        ]);

        $data['prefix'] = $in->getOpt('prefix') ?: '/' . $data['name'];
        $data['idVar'] = '{id}';

        if ($in->getOpt('rest', false)) {
            $config['tplFilename'] = 'controller-rest';
        }

        return $this->writeFile('@app/Controllers', $data, $config, $out);
    }

    /**
     * Generate WebSocket controller class
     * @Usage {fullCommand} CLASS_NAME SAVE_DIR [--option ...]
     * @Arguments
     *   name       The class name, don't need suffix and ext.(eg. <info>demo</info>)
     *   dir        The class file save dir(default: <info>@app/WebSocket</info>)
     * @Options
     *   -y, --yes BOOL             No need to confirm when performing file writing. default is: <info>False</info>
     *   -o, --override BOOL        Force override exists file. default is: <info>False</info>
     *   -n, --namespace STRING     The class namespace. default is: <info>App\WebSocket</info>
     *   --prefix STRING            The route path for the controller. default is class name
     *   --suffix STRING            The class name suffix. default is: <info>Controller</info>
     *   --tpl-file STRING          The template file name. default is: <info>ws-controller.stub</info>
     *   --tpl-dir STRING           The template file dir path.(default: devtool/res/templates)
     * @Example
     *   <info>{fullCommand} echo --prefix /echo -y</info>         Gen EchoController class to `@app/WebSocket`
     *   <info>{fullCommand} chat --prefix /chat</info>     Gen ChatController class to `@app/WebSocket`
     * @return int
     * @param Input $in
     * @param Output $out
     * @return int
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    public function websocket(Input $in, Output $out): int
    {
        list($config, $data) = $this->collectInfo($in, $out, [
            'suffix' => 'Controller',
            'namespace' => 'App\\WebSocket',
            'tplFilename' => 'ws-controller',
        ]);

        $data['prefix'] = $in->getOpt('prefix') ?: '/' . $data['name'];

        return $this->writeFile('@app/WebSocket', $data, $config, $out);
    }

    /**
     * Generate RPC service class
     * @return int
     */
    public function rpcService(): int
    {
        \output()->writeln('un-completed ...');
        return 0;
    }

    /**
     * Generate an event listener class
     * @Usage {fullCommand} CLASS_NAME SAVE_DIR [--option ...]
     * @Arguments
     *   name       The class name, don't need suffix and ext.(eg. <info>demo</info>)
     *   dir        The class file save dir(default: <info>@app/Listener</info>)
     * @Options
     *   -y, --yes BOOL             No need to confirm when performing file writing. default is: <info>False</info>
     *   -o, --override BOOL        Force override exists file. default is: <info>False</info>
     *   -n, --namespace STRING     The class namespace. default is: <info>App\Listener</info>
     *   --suffix STRING            The class name suffix. default is: <info>Listener</info>
     *   --tpl-file STRING          The template file name. default is: <info>listener.stub</info>
     *   --tpl-dir STRING           The template file dir path.(default: devtool/res/templates)
     * @Example
     *   <info>{fullCommand} demo</info>     Gen DemoListener class to `@app/Listener`
     * @param Input $in
     * @param Output $out
     * @return int
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    public function listener(Input $in, Output $out): int
    {
        list($config, $data) = $this->collectInfo($in, $out, [
            'suffix' => 'Listener',
            'namespace' => 'App\\Listener',
            'tplFilename' => 'listener',
        ]);

        return $this->writeFile('@app/Listener', $data, $config, $out);
    }

    /**
     * Generate HTTP middleware class
     * @Usage {fullCommand} CLASS_NAME SAVE_DIR [--option ...]
     * @Arguments
     *   name       The class name, don't need suffix and ext.(eg. <info>demo</info>)
     *   dir        The class file save dir(default: <info>@app/Middlewares</info>)
     * @Options
     *   -y, --yes BOOL             No need to confirm when performing file writing. default is: <info>False</info>
     *   -o, --override BOOL        Force override exists file. default is: <info>False</info>
     *   -n, --namespace STRING     The class namespace. default is: <info>App\Middlewares</info>
     *   --suffix STRING            The class name suffix. default is: <info>Middleware</info>
     *   --tpl-file STRING          The template file name. default is: <info>middleware.stub</info>
     *   --tpl-dir STRING           The template file dir path.(default: devtool/res/templates)
     * @Example
     *   <info>{fullCommand} demo</info>     Gen DemoMiddleware class to `@app/Middlewares`
     * @param Input $in
     * @param Output $out
     * @return int
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    public function middleware(Input $in, Output $out): int
    {
        list($config, $data) = $this->collectInfo($in, $out, [
            'suffix' => 'Middleware',
            'namespace' => 'App\\Middlewares',
            'tplFilename' => 'middleware',
        ]);

        return $this->writeFile('@app/Middlewares', $data, $config, $out);
    }

    /**
     * Generate user task class
     * @Usage {fullCommand} CLASS_NAME SAVE_DIR [--option ...]
     * @Arguments
     *   name       The class name, don't need suffix and ext.(eg. <info>demo</info>)
     *   dir        The class file save dir(default: <info>@app/Tasks</info>)
     * @Options
     *   -y, --yes BOOL             No need to confirm when performing file writing. default is: <info>False</info>
     *   -o, --override BOOL        Force override exists file. default is: <info>False</info>
     *   -n, --namespace STRING     The class namespace. default is: <info>App\Tasks</info>
     *   --suffix STRING            The class name suffix. default is: <info>Task</info>
     *   --tpl-file STRING          The template file name. default is: <info>task.stub</info>
     *   --tpl-dir STRING           The template file dir path.(default: devtool/res/templates)
     * @Example
     *   <info>{fullCommand} demo</info>     Gen DemoTask class to `@app/Tasks`
     * @param Input $in
     * @param Output $out
     * @return int
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    public function task(Input $in, Output $out): int
    {
        list($config, $data) = $this->collectInfo($in, $out, [
            'suffix' => 'Task',
            'namespace' => 'App\\Tasks',
            'tplFilename' => 'task',
        ]);

        return $this->writeFile('@app/Tasks', $data, $config, $out);
    }

    /**
     * Generate user custom process class
     * @Usage {fullCommand} CLASS_NAME SAVE_DIR [--option ...]
     * @Arguments
     *   name       The class name, don't need suffix and ext.(eg. <info>demo</info>)
     *   dir        The class file save dir(default: <info>@app/Process</info>)
     * @Options
     *   -y, --yes BOOL             No need to confirm when performing file writing. default is: <info>False</info>
     *   -o, --override BOOL        Force override exists file. default is: <info>False</info>
     *   -n, --namespace STRING     The class namespace. default is: <info>App\Process</info>
     *   --suffix STRING            The class name suffix. default is: <info>Process</info>
     *   --tpl-file STRING          The template file name. default is: <info>process.stub</info>
     *   --tpl-dir STRING           The template file dir path.(default: devtool/res/templates)
     * @Example
     *   <info>{fullCommand} demo</info>     Gen DemoProcess class to `@app/Process`
     * @param Input $in
     * @param Output $out
     * @return int
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    public function process(Input $in, Output $out): int
    {
        list($config, $data) = $this->collectInfo($in, $out, [
            'suffix' => 'Process',
            'namespace' => 'App\\Process',
            'tplFilename' => 'process',
        ]);

        return $this->writeFile('@app/Process', $data, $config, $out);
    }

    /**
     * @param Input $in
     * @param Output $out
     * @param array $defaults
     * @return array
     */
    private function collectInfo(Input $in, Output $out, array $defaults = []): array
    {
        $config = [
            'tplFilename' => $in->getOpt('tpl-file') ?: $defaults['tplFilename'],
            'tplDir' => $in->getOpt('tpl-dir') ?: $this->defaultTplPath,
        ];

        if (!$name = $in->getArg(0)) {
            $name = $in->read('Please input class name(no suffix and ext. eg. test): ');
        }

        if (!$name) {
            $out->writeln('<error>No class name input! Quit</error>', true, 1);
        }

        $sfx = $in->getOpt('suffix') ?: $defaults['suffix'];
        $data = [
            'name' => $name,
            'suffix' => $sfx,
            'namespace' => $in->sameOpt(['n','namespace']) ?: $defaults['namespace'],
            'className' => \ucfirst($name) . $sfx,
        ];

        return [$config, $data];
    }

    /**
     * @param string $defaultDir
     * @param array $data
     * @param array $config
     * @param Output $out
     * @return int
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Leuffen\TextTemplate\TemplateParsingException
     */
    private function writeFile(string $defaultDir, array $data, array $config, Output $out): int
    {
        // $out->writeln("Some Info: \n" . \json_encode($config, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES));
        $out->writeln("Class data: \n" . \json_encode($data, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES));

        if (!$saveDir = \input()->getArg(1)) {
            $saveDir = $defaultDir;
        }

        $file = App::getAlias($saveDir) . '/' . $data['className'] . '.php';

        $out->writeln("Target File: <info>$file</info>\n");

        if (\file_exists($file)) {
            $override = \input()->sameOpt(['o', 'override']);

            if (null === $override) {
                if (!ConsoleUtil::confirm('Target file has been exists, override?', false)) {
                    $out->writeln(' Quit, Bye!');

                    return 0;
                }
            } elseif (!$override) {
                $out->writeln(' Quit, Bye!');

                return 0;
            }
        }

        $yes = \input()->sameOpt(['y', 'yes'], false);

        if (!$yes && !ConsoleUtil::confirm('Now, will write content to file, ensure continue?')) {
            $out->writeln(' Quit, Bye!');

            return 0;
        }

        $ger = new FileGenerator($config);

        if ($ok = $ger->renderAs($file, $data)) {
            $out->writeln('<success>OK, write successful!</success>');
        } else {
            $out->writeln('<error>NO, write failed!</error>');
        }

        return 0;
    }
}
