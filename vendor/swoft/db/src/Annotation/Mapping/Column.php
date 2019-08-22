<?php declare(strict_types=1);


namespace Swoft\Db\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Db
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Attributes({
 *     @Attribute("name", type="string"),
 *     @Attribute("prop", type="string"),
 *     @Attribute("hidden", type="bool"),
 * })
 *
 * @since 2.0
 */
class Column
{
    /**
     * Database field name
     *
     * @var string
     */
    private $name = '';

    /**
     * Entity to array prop name
     *
     * @var string
     */
    private $prop = '';

    /**
     * The attribute that should be hidden for serialization.
     *
     * @var bool
     */
    private $hidden = false;

    /**
     * Column constructor.
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
        if (isset($values['prop'])) {
            $this->prop = $values['prop'];
        }
        if (isset($values['hidden'])) {
            $this->hidden = $values['hidden'];
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getProp(): string
    {
        return $this->prop;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }
}
