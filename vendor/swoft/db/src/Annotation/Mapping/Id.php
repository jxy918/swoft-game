<?php declare(strict_types=1);


namespace Swoft\Db\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Id
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Attributes({
 *     @Attribute("incrementing", type="bool"),
 * })
 *
 * @since 2.0
 */
class Id
{
    /**
     * Set whether IDs are incrementing.
     *
     * @var bool
     */
    private $incrementing = true;

    /**
     * Id constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->incrementing = $values['value'];
        }
        if (isset($values['incrementing'])) {
            $this->incrementing = $values['incrementing'];
        }
    }

    /**
     * @return bool
     */
    public function isIncrementing(): bool
    {
        return $this->incrementing;
    }
}
