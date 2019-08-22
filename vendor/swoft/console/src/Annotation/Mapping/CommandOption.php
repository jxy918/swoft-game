<?php declare(strict_types=1);

namespace Swoft\Console\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;
use Swoft\Console\Input\AbstractFlag;
use Swoft\Stdlib\Helper\Str;
use function trim;

/**
 * Class CommandOption
 * @since 2.0
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 * @Attributes(
 *     @Attribute("short", type="string")
 * )
 */
final class CommandOption extends AbstractFlag
{
    /**
     * The option short name. (Allow add multi by ',')
     * Notice: each shortcut only allow one char
     *
     * @var string
     */
    private $short = '';

    /**
     * Class constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        parent::__construct($values);

        if (!empty($values['short'])) {
            $this->short = trim((string)$values['short'], '- ');
        }
    }

    /**
     * @return string
     */
    public function getShort(): string
    {
        return $this->short;
    }

    /**
     * Get shorts array
     *
     * @return string[]
     */
    public function getShorts(): array
    {
        return $this->short ? Str::explode($this->short) : [];
    }
}
