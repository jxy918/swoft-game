<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Swoole;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Server\Swoole\PipeMessageInterface;
use Swoft\Session\Session;
use Swoole\Server;
use function json_decode;
use function json_last_error;

/**
 * Class PipeMessageListener
 *
 * @Bean()
 */
class PipeMessageListener implements PipeMessageInterface
{
    /**
     * Pipe message event
     *
     * @param Server $server
     * @param int    $srcWorkerId
     * @param mixed  $message
     */
    public function onPipeMessage(Server $server, int $srcWorkerId, $message): void
    {
        if (!$message) {
            return;
        }

        $data = (array)json_decode($message, true);

        // Don't handle on data is invalid
        if (JSON_ERROR_NONE !== json_last_error()) {
            return;
        }

        server()->log("PipeMessage: received pipe-message fromWID=$srcWorkerId data=", $data);

        // Ensure is websocket notify message
        if (!isset($data['from']) || $data['from'] !== 'tcpServer') {
            return;
        }

        // Handle
        if (isset($data['event'])) {
            $event = (string)$data['event'];

            /** @see CloseListener::onClose() */
            if ($event === 'tcpClose') {
                $this->handleClose($data, $srcWorkerId);
            }
        }
    }

    /**
     * @param array $data
     * @param int   $srcWID
     */
    protected function handleClose(array $data, int $srcWID): void
    {
        $sid = $data['sid'];

        if (Session::has($sid)) {
            server()->log("PipeMessage: destroy tcp connection for fd=$sid fromWID=$srcWID");
            Session::destroy($sid);
        }
    }
}
