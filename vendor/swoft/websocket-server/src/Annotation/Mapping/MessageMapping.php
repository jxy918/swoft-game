<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class MessageMapping - Use for mark websocket message request command handler method
 *
 * @since   2.0
 * @package Swoft\WebSocket\Server\Annotation\Mapping
 *
 * @Annotation
 * @Target("METHOD")
 * @Attributes(
 *     @Attribute("command", type="string")
 * )
 */
final class MessageMapping
{
    /**
     * @var bool
     */
    private $root = false;

    /**
     * @var string
     * @Required()
     */
    private $command = '';

    /**
     * Default opcode of the command. please see WEBSOCKET_OPCODE_*
     *
     * @var int
     */
    private $opcode = 0;

    /**
     * Class constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->command = (string)$values['value'];
        } elseif (isset($values['command'])) {
            $this->command = (string)$values['command'];
        }

        if (isset($values['root'])) {
            $this->root = (bool)$values['root'];
        }

        if (isset($values['opcode'])) {
            $this->opcode = (int)$values['opcode'];
        }
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->root;
    }

    /**
     * @return int
     */
    public function getOpcode(): int
    {
        return $this->opcode;
    }
}
