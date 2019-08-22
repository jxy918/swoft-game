<?php declare(strict_types=1);

namespace Swoft\Devtool\Command;

use function json_encode;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandArgument;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandOption;
use Swoft\Console\Helper\Interact;
use Swoft\Console\Helper\Show;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Tcp\Protocol;
use Swoole\Coroutine\Client;
use Swoole\Coroutine\Http\Client as HttpCoClient;
use const SWOOLE_SOCK_TCP;

/**
 * Provide some simple tcp, ws client for develop or testing[by <cyan>devtool</cyan>]
 *
 * @Command("dclient")
 */
class DClientCommand
{
    /**
     * connect to an tcp server and allow send message interactive
     *
     * @CommandMapping()
     * @CommandOption("host", short="H", desc="the tcp server host address", default="127.0.0.1", type="string")
     * @CommandOption("port", short="p", desc="the tcp server port number", default="18309", type="integer")
     * @CommandOption("split", short="s", desc="the tcp package split type: eof, len", default="eof", type="string")
     * @CommandOption("packer", desc="the tcp package data packer: token-text, json, php", default="token-text", type="string")
     *
     * @param Input  $input
     * @param Output $output
     */
    public function tcp(Input $input, Output $output): void
    {
        $proto = new Protocol();
        $sType = $input->getSameOpt(['split', 's'], 'eof');
        if ($sType === 'len') {
            $proto->setOpenLengthCheck(true);
        }

        if ($pType = $input->getStringOpt('packer')) {
            if (!$proto->isValidType($pType)) {
                $output->error("input invalid packer type: {$pType}, allow: token-text, json, php");
                return;
            }

            $proto->setType($pType);
        }

        $output->aList([
            'splitPackageType' => $proto->getSplitType(),
            'dataPackerType'   => $proto->getType(),
            'dataPackerClass'  => $proto->getPackerClass(),
        ], 'Client Protocol');

        $client = new Client(SWOOLE_SOCK_TCP);
        $client->set($proto->getConfig());

        $host = $input->getSameOpt(['host', 'H'], '127.0.0.1');
        $port = $input->getSameOpt(['port', 'p'], 18309);
        $addr = $host . ':' . $port;

        CONNCET:
        $output->colored('Begin connecting to tcp server: ' . $addr);
        if (!$client->connect((string)$host, (int)$port, 5.0)) {
            $code = $client->errCode;
            /** @noinspection PhpComposerExtensionStubsInspection */
            $msg = socket_strerror($code);
            Show::error("Connect server failed. Error($code): $msg");
            return;
        }

        $output->colored('Success connect to tcp server. now, you can send message');
        $output->title('INTERACTIVE', ['indent' => 0]);

        while (true) {
            if (!$msg = $output->read('<info>client</info>> ')) {
                $output->liteWarning('Please input message for send');
                continue;
            }

            // Exit interactive terminal
            if ($msg === 'quit' || $msg === 'exit') {
                $output->colored('Quit, Bye!');
                break;
            }

            // Send message $msg . $proto->getPackageEOf()
            if (false === $client->send($proto->packBody($msg))) {
                /** @noinspection PhpComposerExtensionStubsInspection */
                $output->error('Send error - ' . socket_strerror($client->errCode));

                if (Interact::confirm('Reconnect', true, false)) {
                    $client->close();
                    goto CONNCET;
                }

                $output->colored('GoodBye!');
                break;
            }

            // Recv response
            $res = $client->recv(2.0);
            if ($res === false) {
                /** @noinspection PhpComposerExtensionStubsInspection */
                $output->error('Recv error - ' . socket_strerror($client->errCode));
                continue;
            }

            if ($res === '') {
                $output->info('Server closed connection');
                if (Interact::confirm('Reconnect', true, false)) {
                    $client->close();
                    goto CONNCET;
                }

                $output->colored('GoodBye!');
                break;
            }

            [$head, $body] = $proto->unpackData($res);
            $output->writeln('head: ' . json_encode($head));
            $output->writef('<yellow>server</yellow>> %s', $body);
        }

        $client->close();
    }

    /**
     * connect to websocket server and allow send message interactive
     * @CommandMapping("ws")
     * @CommandOption("host", short="H", desc="the tcp server host address", default="127.0.0.1", type="string")
     * @CommandOption("port", short="p", desc="the tcp server port number", default="18308", type="integer")
     * @CommandArgument("path", type="string", default="/echo", desc="the want connected websocket server uri path")
     * @example
     *  {fullCmd} /chat
     *
     * @param Input  $input
     * @param Output $output
     */
    public function websocket(Input $input, Output $output): void
    {
        $path = $input->getString('path');
        $host = $input->getSameOpt(['host', 'H'], '127.0.0.1');
        $port = $input->getSameOpt(['port', 'p'], 18308);
        $addr = $host . ':' . $port;

        $output->colored("Begin connecting to websocket server: $addr path: $path");

        $client = new HttpCoClient((string)$host, (int)$port, false);

        $output->colored('Success connect to websocket server. Now, you can send message');
        $output->title('INTERACTIVE', ['indent' => 0]);

        CONNCET:
        if (!$client->upgrade($input->getString('path'))) {
            $code = $client->errCode;
            /** @noinspection PhpComposerExtensionStubsInspection */
            $msg = socket_strerror($code);
            Show::error("websocket handshake failed. Error($code): $msg");
            return;
        }

        if ($res = $client->recv(1.0)) {
            $output->writef('<yellow>server</yellow>> %s', $res);
        }

        while (true) {
            if (!$msg = $output->read('<info>client</info>> ')) {
                $output->liteWarning('Please input message for send');
                continue;
            }

            // Exit interactive terminal
            if ($msg === 'quit' || $msg === 'exit') {
                $output->colored('Quit, Bye!');
                break;
            }

            // Send message
            if (false === $client->push($msg)) {
                /** @noinspection PhpComposerExtensionStubsInspection */
                $output->error('Send error - ' . socket_strerror($client->errCode));

                if (Interact::confirm('Reconnect', true, false)) {
                    $client->close();
                    goto CONNCET;
                }

                $output->colored('GoodBye!');
                break;
            }

            // Recv response
            $res = $client->recv(2.0);
            if ($res === false) {
                /** @noinspection PhpComposerExtensionStubsInspection */
                $output->error('Recv error - ' . socket_strerror($client->errCode));
                continue;
            }

            if ($res === '') {
                $output->info('Server closed connection');
                if (Interact::confirm('Reconnect', true, false)) {
                    $client->close();
                    goto CONNCET;
                }

                $output->colored('GoodBye!');
                break;
            }

            // $output->prettyJSON($head);
            $output->writef('<yellow>server</yellow>> %s', $res);
        }

        $client->close();
    }
}
