<?php declare(strict_types=1);

namespace Swoft\Console\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;
use Swoft\Stdlib\Helper\Str;
use function trim;

/**
 * The annotation of command controller
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes(
 *     @Attribute("name", type="string"),
 *     @Attribute("alias", type="string")
 * )
 */
final class Command
{
    // fixed args and opts for a command/controller-command
    public const ARG_REQUIRED = 1;
    public const ARG_OPTIONAL = 2;
    public const ARG_IS_ARRAY = 4;

    public const OPT_BOOLEAN  = 1; // like symfony InputOption::VALUE_NONE
    public const OPT_REQUIRED = 2;
    public const OPT_OPTIONAL = 4;
    public const OPT_IS_ARRAY = 8; // allow multi value

    /**
     * Command group name
     *
     * @var string
     */
    private $name = '';

    /**
     * The group description message text
     *
     * @var string
     */
    private $desc = '';

    /**
     * Command group name alias. Allow add multi by ','
     *
     * @var string
     */
    private $alias = '';

    /**
     * Full command ID aliases.
     *
     * @var array
     * [
     *  // id alias => 'group:command'
     *  'gen-ws' => 'gen:ws'
     * ]
     */
    private $idAliases = [];

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var bool
     */
    private $coroutine = true;

    /**
     * Default command in the group
     *
     * @var string
     */
    private $defaultCommand = '';

    /**
     * Command constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = (string)$values['value'];
        } elseif (isset($values['name'])) {
            $this->name = (string)$values['name'];
        }

        if (isset($values['alias'])) {
            $this->alias = trim((string)$values['alias']);
        }

        if (!empty($values['desc'])) {
            $this->desc = trim((string)$values['desc']);
        }

        if (isset($values['enabled'])) {
            $this->enabled = (bool)$values['enabled'];
        }

        if (isset($values['coroutine'])) {
            $this->coroutine = (bool)$values['coroutine'];
        }

        if (isset($values['idAliases'])) {
            $this->idAliases = (array)$values['idAliases'];
        }

        if (isset($values['defaultCommand'])) {
            $this->defaultCommand = trim($values['defaultCommand']);
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
    public function getDesc(): string
    {
        return $this->desc;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return $this->alias ? Str::explode($this->alias) : [];
    }

    /**
     * @return bool
     */
    public function isCoroutine(): bool
    {
        return $this->coroutine;
    }

    /**
     * @return string
     */
    public function getDefaultCommand(): string
    {
        return $this->defaultCommand;
    }

    /**
     * @return array
     */
    public function getIdAliases(): array
    {
        return $this->idAliases;
    }
}
