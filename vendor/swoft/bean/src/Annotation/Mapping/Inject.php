<?php declare(strict_types=1);

namespace Swoft\Bean\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Inject
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Attributes({
 *     @Attribute("name", type="string")
 * })
 *
 * @since 2.0
 */
final class Inject
{
    /**
     * Bean name
     *
     * @var string
     */
    private $name = '';

    /**
     * Inject constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }
        if (isset($values['name'])) {
            $this->name = $values['name'];
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}