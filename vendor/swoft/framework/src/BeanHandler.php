<?php declare(strict_types=1);

namespace Swoft;

use function array_shift;
use function explode;
use function get_class;
use function implode;
use ReflectionClass;
use ReflectionException;
use Swoft;
use Swoft\Aop\Aop;
use Swoft\Aop\Proxy;
use Swoft\Bean\Definition\ObjectDefinition;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Bean\Handler;
use Swoft\Proxy\Exception\ProxyException;

/**
 * Class BeanHandler
 *
 * @since 2.0
 */
class BeanHandler extends Handler
{
    /**
     * Before initialize bean
     *
     * @param string           $beanName
     * @param string           $className
     * @param ObjectDefinition $objDfn
     * @param array            $annotation
     *
     * @example
     * [
     *     'annotation' => [
     *          new ClassAnnotation(),
     *          new ClassAnnotation(),
     *          new ClassAnnotation(),
     *     ]
     *     'reflection' => new ReflectionClass(),
     *     'properties' => [
     *          'propertyName' => [
     *              'annotation' => [
     *                  new PropertyAnnotation(),
     *                  new PropertyAnnotation(),
     *                  new PropertyAnnotation(),
     *              ]
     *             'reflection' => new ReflectionProperty(),
     *          ]
     *     ],
     *    'methods' => [
     *          'methodName' => [
     *              'annotation' => [
     *                  new MethodAnnotation(),
     *                  new MethodAnnotation(),
     *                  new MethodAnnotation(),
     *              ]
     *             'reflection' => new ReflectionFunctionAbstract(),
     *          ]
     *    ],
     * ]
     *
     * @throws ReflectionException
     */
    public function beforeInit(string $beanName, string $className, ObjectDefinition $objDfn, array $annotation): void
    {
        $alias = $objDfn->getAlias();

        // Register aop
        $reflectionClass   = new ReflectionClass($className);
        $reflectionMethods = $reflectionClass->getMethods();
        foreach ($reflectionMethods as $reflectionMethod) {
            if ($reflectionMethod->isStatic() || $reflectionMethod->isPrivate()) {
                continue;
            }

            // Method annotations
            $methodName        = $reflectionMethod->getName();
            $methodAnnotations = $annotation['methods'][$methodName]['annotation'] ?? [];

            // Bean name and alias
            $beanNames   = [];
            $beanNames[] = $beanName;

            if (!empty($alias)) {
                $beanNames[] = $alias;
            }

            $mtdAntClassNames = [];
            foreach ($methodAnnotations as $methodAnnotation) {
                $mtdAntClassNames[] = get_class($methodAnnotation);
            }

            Aop::register($beanNames, $className, $methodName, $mtdAntClassNames);
        }
    }

    /**
     * Class proxy
     *
     * @param string $className
     *
     * @return string
     *
     * @throws ProxyException
     */
    public function classProxy(string $className): string
    {
        return Proxy::newClassName($className);
    }

    /**
     * Get reference value
     *
     * @param $value
     *
     * @return mixed|string
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function getReferenceValue($value)
    {
        $values = explode('.', $value);

        // Remove `config.` prefix, if exists.
        if (isset($values[0]) && $values[0] === 'config') {
            array_shift($values);
            $value = implode('.', $values);
        }

        /** @see \Swoft\Config\Config::get() */
        return Swoft::getBean('config')->get($value);
    }
}
