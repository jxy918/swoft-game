<?php declare(strict_types=1);

namespace Swoft\Aop\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Aspect
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("order", type="int"),
 * })
 *
 * @since 2.0
 */
final class Aspect
{
    /**
     * Default order
     * Default is execute at last. The smaller is first to execute.
     *
     * @var int
     */
    private $order = PHP_INT_MAX;

    /**
     * Aspect constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->order = $values['value'];
        }
        if (isset($values['order'])) {
            $this->order = $values['order'];
        }
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }
}