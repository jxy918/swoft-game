<?php declare(strict_types=1);


namespace Swoft\Bean\Contract;

use Swoft\Bean\Definition\ObjectDefinition;

/**
 * Class HandlerInterface
 *
 * @since 2.0
 */
interface HandlerInterface
{
    /**
     * Before initialize bean
     *
     * @param string           $beanName
     * @param string           $className
     * @param ObjectDefinition $objDfn
     * @param array            $annotation
     */
    public function beforeInit(string $beanName, string $className, ObjectDefinition $objDfn, array $annotation): void;

    /**
     * Class proxy
     *
     * @param string $className
     *
     * @return string
     */
    public function classProxy(string $className): string;

    /**
     * Get reference value
     *
     * @param $value
     *
     * @return mixed
     */
    public function getReferenceValue($value);
}