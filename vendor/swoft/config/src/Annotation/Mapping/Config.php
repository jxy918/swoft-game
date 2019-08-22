<?php declare(strict_types=1);

namespace Swoft\Config\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Config
 *
 * @Annotation
 * @Target({"CLASS","PROPERTY"})
 * @Attributes({
 *     @Attribute("key", type="string")
 * })
 *
 * @since 2.0
 */
final class Config
{
    /**
     * @var string
     */
    private $key = '';

    /**
     * Config constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->key = $values['value'];
        }

        if (isset($values['key'])) {
            $this->key = $values['key'];
        }
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
