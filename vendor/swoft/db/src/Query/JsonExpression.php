<?php declare(strict_types=1);


namespace Swoft\Db\Query;

use InvalidArgumentException;
use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;

/**
 * Class JsonExpression
 *
 * @Bean(scope=Bean::PROTOTYPE)
 * @since 2.0
 */
class JsonExpression extends Expression
{
    /**
     * Create a new raw query expression.
     *
     * @param mixed ...$params
     *
     * @return static
     * @throws ContainerException
     * @throws ReflectionException
     */
    public static function new(...$params)
    {
        list($value) = $params;
        $self = self::__instance();

        $self->value = $self->getJsonBindingParameter($value);

        return $self;
    }

    /**
     * Translate the given value into the appropriate JSON binding parameter.
     *
     * @param mixed $value
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function getJsonBindingParameter($value)
    {
        if ($value instanceof Expression) {
            return $value->getValue();
        }

        switch ($type = gettype($value)) {
            case 'boolean':
                return $value ? 'true' : 'false';
            case 'NULL':
            case 'integer':
            case 'double':
            case 'string':
                return '?';
            case 'object':
            case 'array':
                return '?';
        }

        throw new InvalidArgumentException("JSON value is of illegal type: {$type}");
    }
}
