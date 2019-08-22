<?php declare(strict_types=1);


namespace Swoft\Aop\Contract;


/**
 * Class JoinPointInterface
 *
 * @since 2.0
 */
interface JoinPointInterface
{
    /**
     * @return array
     */
    public function getArgs(): array;

    /**
     * @return object
     */
    public function getTarget();

    /**
     * @return string
     */
    public function getMethod(): string;
}