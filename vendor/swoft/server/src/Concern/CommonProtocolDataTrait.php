<?php declare(strict_types=1);

namespace Swoft\Server\Concern;

use Swoft\Stdlib\Helper\JsonHelper;
use function is_scalar;

/**
 * Trait CommonProtocolDataTrait
 */
trait CommonProtocolDataTrait
{
    /**
     * The ext data
     *
     * @var array
     */
    protected $ext = [];

    /**
     * The main data
     *
     * @var mixed
     */
    protected $data;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getExt(): array
    {
        return $this->ext;
    }

    /**
     * @param array $ext
     */
    public function setExt(array $ext): void
    {
        $this->ext = $ext;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getExtValue(string $key, $default = null)
    {
        return $this->ext[$key] ?? $default;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setExtValue(string $key, $value): void
    {
        $this->ext[$key] = $value;
    }

    /**
     * @return string
     */
    public function getDataString(): string
    {
        if (!$this->data) {
            return '';
        }

        if (is_scalar($this->data)) {
            return (string)$this->data;
        }

        return JsonHelper::encode($this->data);
    }
}
