<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class TcpMapping - Use for mark tcp message request command handler method
 *
 * @since   2.0.3
 *
 * @Annotation
 * @Target("METHOD")
 * @Attributes(
 *     @Attribute("route", type="string")
 * )
 */
final class TcpMapping
{
    /**
     * The tcp server route path. eg: 'home.index'
     *
     * @var string
     * @Required()
     */
    private $route = '';

    /**
     * Mark current route is root command.
     *
     * @var bool
     */
    private $root = false;

    /**
     * Class constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->route = (string)$values['value'];
        } elseif (isset($values['route'])) {
            $this->route = (string)$values['route'];
        }

        if (isset($values['root'])) {
            $this->root = (bool)$values['root'];
        }
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->root;
    }
}
