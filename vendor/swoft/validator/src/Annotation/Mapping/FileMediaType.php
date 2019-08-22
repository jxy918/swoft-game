<?php declare(strict_types=1);

namespace Swoft\Validator\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class FileMediaType
 *
 * @since 2.0
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Attributes({
 *     @Attribute("message", type="string"),
 *     @Attribute("mediaType", type="array")
 * })
 */
class FileMediaType
{
    /**
     * @var array
     */
    private $mediaType = [];

    /**
     * @var string
     */
    private $message = '';

    /**
     * FileMediaType constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->message = $values['value'];
        }
        if (isset($values['message'])) {
            $this->message = $values['message'];
        }
        if (isset($values['mediaType'])) {
            $this->mediaType = $values['mediaType'];
        }
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getMediaType(): array
    {
        return $this->mediaType;
    }
}
