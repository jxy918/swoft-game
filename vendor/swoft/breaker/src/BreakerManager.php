<?php declare(strict_types=1);


namespace Swoft\Breaker;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Breaker\Annotation\Mapping\Breaker as BreakerAnnotation;
use Swoft\Breaker\Exception\BreakerException;

/**
 * Class BreakerManager
 *
 * @since 2.0
 *
 * @Bean()
 */
class BreakerManager
{
    /**
     * @var Breaker[]
     *
     * @example
     * [
     *     'className:method' => new Breaker(),
     *     'className:method' => new Breaker()
     * ]
     */
    private $breakers = [];

    /**
     * @param array $breakers
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function initBreaker(array $breakers): void
    {
        foreach ($breakers as $className => $methodBreakers) {
            /* @var BreakerAnnotation $breaker */
            foreach ($methodBreakers as $methodName => $breaker) {

                $bConfig = [];
                $config  = $breaker->getConfig();
                foreach ($config as $key) {
                    $configMethod  = sprintf('get%s', ucfirst($key));
                    $bConfig[$key] = $breaker->{$configMethod}();
                }

                $this->breakers[$className][$methodName] = Breaker::new($bConfig);
            }
        }
    }

    /**
     * @param string $className
     * @param string $methodName
     *
     * @return Breaker
     * @throws BreakerException
     */
    public function getBreaker(string $className, string $methodName): Breaker
    {
        $breaker = $this->breakers[$className][$methodName] ?? null;
        if (empty($breaker)) {
            throw new BreakerException(
                sprintf('Breaker(%s->%s) is not exist!', $className, $methodName)
            );
        }

        return $breaker;
    }
}