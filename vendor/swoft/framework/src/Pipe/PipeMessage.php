<?php

namespace Swoft\Pipe;

use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Exception\Exception;
use Swoft\Helper\ArrayHelper;

/**
 * Pipe message
 *
 * @Bean()
 */
class PipeMessage implements PipeMessageInterface
{
    /**
     * Task message type
     */
    const MESSAGE_TYPE_TASK = 'task';

    /**
     * User message type
     */
    const MESSAGE_TYPE_USER = 'user';

    /**
     * @var string
     */
    private $type = 'json';

    /**
     * @var array
     */
    private $packers = [];

    /**
     * @param string $type
     * @param array  $data
     *
     * @return string
     */
    public function pack(string $type, array $data): string
    {
        return $this->getPacker()->pack($type, $data);
    }

    /**
     * @param string $message
     *
     * @return array
     */
    public function unpack(string $message): array
    {
        return $this->getPacker()->unpack($message);
    }

    /**
     * @return PipeMessageInterface
     * @throws Exception
     */
    private function getPacker(): PipeMessageInterface
    {
        $packers = ArrayHelper::merge($this->defaultPackers(), $this->packers);
        if (!isset($packers[$this->type])) {
            throw new Exception(sprintf('The %s pipe message packer is not exist! '));
        }

        $packer = $packers[$this->type];

        return App::getBean($packer);
    }

    /**
     * @return array
     */
    private function defaultPackers()
    {
        return [
            'json' => JsonPipeMessage::class,
        ];
    }
}