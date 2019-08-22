<?php declare(strict_types=1);

namespace Swoft\Tcp;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Server\Concern\CommonProtocolDataTrait;
use Swoft\Stdlib\Helper\JsonHelper;
use Swoft\Tcp\Contract\ResponseInterface;

/**
 * Class Response
 *
 * @since 2.0.4
 * @Bean(scope=Bean::PROTOTYPE)
 */
class Response implements ResponseInterface
{
    use CommonProtocolDataTrait;

    /**
     * @var int
     */
    protected $code = self::OK;

    /**
     * @var string
     */
    protected $msg = self::DEFAULT_MSG;

    /**
     * @var string
     */
    protected $content = '';

    /**
     * Class constructor.
     *
     * @param int    $code
     * @param string $msg
     * @param mixed  $data
     */
    public function __construct(int $code = self::OK, string $msg = self::DEFAULT_MSG, $data = null)
    {
        $this->code = $code;
        $this->msg  = $msg;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'msg'  => $this->msg,
            'data' => $this->data,
            'ext'  => $this->ext,
        ];
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return JsonHelper::encode($this->toArray());
    }

    /**
     * @return bool
     */
    public function isOK(): bool
    {
        return $this->code === self::OK;
    }

    /**
     * @return bool
     */
    public function isFail(): bool
    {
        return $this->code !== self::OK;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getMsg(): string
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     */
    public function setMsg(string $msg): void
    {
        $this->msg = $msg;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
