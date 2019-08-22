<?php declare(strict_types=1);

namespace Swoft\Error\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class ExceptionHandler
 *
 * @since 2.0
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *     @Attribute("exceptions", type="array")
 * })
 */
class ExceptionHandler
{
    /**
     * Exception handler classes
     *
     * @var string[]
     * @Required()
     */
    private $exceptions;

    /**
     * Class constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->exceptions = (array)$values['value'];
        }

        if (isset($values['exceptions'])) {
            $this->exceptions = (array)$values['exceptions'];
        }
    }

    /**
     * @return string[]
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
