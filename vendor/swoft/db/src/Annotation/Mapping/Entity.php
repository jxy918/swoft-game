<?php declare(strict_types=1);


namespace Swoft\Db\Annotation\Mapping;

use Swoft\Db\Pool;

/**
 * Class Entity
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("table", type="string"),
 *     @Attribute("pool", type="string"),
 * })
 *
 * @since 2.0
 */
class Entity
{
    /**
     * Table name
     *
     * @var string
     */
    private $table = '';

    /**
     * Deafaut
     *
     * @var string
     */
    private $pool = Pool::DEFAULT_POOL;

    /**
     * Entity constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->table = $values['value'];
        }
        if (isset($values['table'])) {
            $this->table = $values['table'];
        }
        if (isset($values['pool'])) {
            $this->pool = $values['pool'];
        }
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getPool(): string
    {
        return $this->pool;
    }
}