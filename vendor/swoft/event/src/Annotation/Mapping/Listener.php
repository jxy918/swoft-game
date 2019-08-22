<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2019-02-01
 * Time: 19:57
 */

namespace Swoft\Event\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Listener annotation - for mark an event listener handler class.
 *
 * @since 2.0
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("event", type="string"),
 * })
 */
final class Listener
{
    /**
     * Listen event name
     *
     * @var string
     * @Required()
     */
    private $event = '';

    /**
     * Listener priority value
     *
     * @var int
     */
    private $priority = 0;

    /**
     * Class constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->event = (string)$values['value'];
        } elseif (isset($values['event'])) {
            $this->event = (string)$values['event'];
        }

        if (isset($values['priority'])) {
            $this->priority = (int)$values['priority'];
        }
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
}
