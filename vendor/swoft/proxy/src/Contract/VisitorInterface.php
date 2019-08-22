<?php declare(strict_types=1);


namespace Swoft\Proxy\Contract;

/**
 * Class VisitorInterface
 *
 * @since 2.0
 */
interface VisitorInterface
{
    /**
     * @return string
     */
    public function getProxyName(): string;

    /**
     * @return string
     */
    public function getProxyClassName(): string;
}