<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class TcpController
 *
 * @since 2.0.3
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes(
 *     @Attribute("prefix", type="string")
 * )
 */
final class TcpController
{
    /**
     * Controller prefix.
     *
     * @var string
     */
    private $prefix = '';

    /**
     * Class constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->prefix = (string)$values['value'];
        } elseif (isset($values['prefix'])) {
            $this->prefix = (string)$values['prefix'];
        }
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
