<?php declare(strict_types=1);

namespace Swoft\View\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * View class
 *
 * @Annotation
 * @Target("METHOD")
 * @Attributes(
 *     @Attribute("layout", type="string"),
 *     @Attribute("template", type="string")
 * )
 */
final class View
{
    /**
     * @var string
     */
    private $layout = '';

    /**
     * @var string
     * @Required()
     */
    private $template = '';

    /**
     * View constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->template = $values['value'];
        } elseif ($values['template']) {
            $this->template = $values['template'];
        }

        if (isset($values['layout'])) {
            $this->layout = $values['layout'];
        }
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }
}
