<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Context;

use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Context\AbstractContext;
use Swoft\Http\Message\Request;

/**
 * Class WsOpenContext - on ws open event
 *
 * @since 2.0
 * @Bean(scope=Bean::PROTOTYPE)
 */
class WsOpenContext extends AbstractContext
{
    use PrototypeTrait;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     *
     * @return WsOpenContext
     */
    public static function new(Request $request): self
    {
        /** @var self $ctx */
        $ctx = Swoft::getBean(self::class);

        // Initial properties
        $ctx->request = $request;

        return $ctx;
    }

    /**
     * Clear resource
     */
    public function clear(): void
    {
        parent::clear();

        $this->request = null;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
