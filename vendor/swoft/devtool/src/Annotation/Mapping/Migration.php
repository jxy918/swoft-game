<?php declare(strict_types=1);


namespace Swoft\Devtool\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;
use Swoft\Db\Pool;

/**
 * Class Migration
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("pool", type="string"),
 * })
 *
 * @since 2.0
 */
class Migration
{
    /**
     * @var string
     */
    private $pool = Pool::DEFAULT_POOL;

    /**
     * @var int
     */
    private $time = 0;

    /**
     * Migration constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->time = $values['value'];
        }

        if (isset($values['pool'])) {
            $this->pool = $values['pool'];
        }

        if (isset($values['time'])) {
            $this->time = $values['time'];
        }
    }

    /**
     * @return string
     */
    public function getPool(): string
    {
        return $this->pool;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return (int)$this->time;
    }
}
