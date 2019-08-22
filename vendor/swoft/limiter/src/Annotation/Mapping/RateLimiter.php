<?php declare(strict_types=1);


namespace Swoft\Limiter\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class RateLimiter
 *
 * @since 2.0
 *
 * @Annotation
 * @Target("METHOD")
 * @Attributes({
 *     @Attribute("key", type="string"),
 *     @Attribute("rate", type="int"),
 *     @Attribute("max", type="int"),
 * })
 */
class RateLimiter
{
    /**
     * @var string
     */
    private $name = 'swoft:limiter';

    /**
     * @var string
     */
    private $key = '';

    /**
     * Unit is `qps/seconds`
     *
     * @var int
     */
    private $rate = 10;

    /**
     * Max tokens
     *
     * @var int
     */
    private $max = 20;

    /**
     * @var string
     */
    private $fallback = '';

    /**
     * @var int
     */
    private $default = 10;

    /**
     * @var array
     */
    private $config = [];

    /**
     * Breaker constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->rate = $values['value'];

            $values['rate'] = $this->rate;
            unset($values['value']);
        }

        if (isset($values['name'])) {
            $this->name = $values['name'];
        }

        if (isset($values['key'])) {
            $this->key = $values['key'];
        }

        if (isset($values['rate'])) {
            $this->rate = $values['rate'];
        }

        if (isset($values['max'])) {
            $this->max = $values['max'];
        }

        if (isset($values['default'])) {
            $this->default = $values['default'];
        }

        if (isset($values['fallback'])) {
            $this->fallback = $values['fallback'];
        }

        $this->config = array_keys($values);
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
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function getRate(): int
    {
        return $this->rate;
    }

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getFallback(): string
    {
        return $this->fallback;
    }

    /**
     * @return int
     */
    public function getDefault(): int
    {
        return $this->default;
    }
}