<?php declare(strict_types=1);


namespace Swoft\Limiter;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Limiter\Contract\RateLimiterInterface;
use Swoft\Limiter\Exception\RateLImiterException;
use Swoft\Stdlib\Helper\Arr;
use Swoft\Stdlib\Helper\PhpHelper;
use Swoft\Stdlib\Reflections;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class RateLimter
 *
 * @since 2.0
 *
 * @Bean("rateLimiter")
 */
class RateLimter
{
    /**
     * @var string
     */
    private $name = 'swoft:limiter';

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
     * @var int
     */
    private $default = 10;

    /**
     * @var RateLimiterInterface
     */
    private $rateLimter;

    /**
     * @param callable|array $callback
     * @param string         $className
     * @param string         $method
     * @param                $target
     * @param array          $params
     *
     * @return mixed
     * @throws RateLImiterException
     * @throws ReflectionException
     */
    public function checkRate($callback, string $className, string $method, $target, array $params)
    {
        $config = RateLimiterRegister::getRateLimiter($className, $method);
        $key    = $config['key'] ?? '';

        if (!empty($key)) {
            $key = $this->evaluateKey($key, $className, $method, $params);
        }

        $commonConfig = [
            'name'    => $this->name,
            'rate'    => $this->rate,
            'max'     => $this->max,
            'default' => $this->default,
        ];

        // Default Key
        if (empty($key)) {
            $key = md5(sprintf('%s:%s', $className, $method));
        }

        $config['key'] = $key;

        $config   = Arr::merge($commonConfig, $config);
        $fallback = $config['fallback'] ?? '';

        if ($method == $fallback) {
            throw new RateLImiterException(sprintf('Method(%s) and fallback must be different', $method));
        }

        $ticket = $this->rateLimter->getTicket($config);

        if ($ticket) {
            return PhpHelper::call($callback);
        }

        if (!empty($fallback)) {
            return PhpHelper::call([$target, $fallback], ...$params);
        }

        throw new RateLImiterException(
            sprintf('Rate(%s->%s) to Limit!', $className, $method)
        );
    }

    /**
     * @param string $key
     * @param string $className
     * @param string $method
     * @param array  $params
     *
     * @return string
     * @throws ReflectionException
     */
    private function evaluateKey(string $key, string $className, string $method, array $params): string
    {
        $values   = [];
        $rcMethod = Reflections::get($className);
        $rcParams = $rcMethod['methods'][$method]['params'] ?? [];

        $index = 0;
        foreach ($rcParams as $rcParam) {
            [$pName] = $rcParam;
            $values[$pName] = $params[$index];
            $index++;
        }

        // Inner vars
        $values['CLASS']  = $className;
        $values['METHOD'] = $method;

        // Parse express language
        $el = new ExpressionLanguage();
        return $el->evaluate($key, $values);
    }
}