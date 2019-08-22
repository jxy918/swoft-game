<?php declare(strict_types=1);

namespace Swoft\Stdlib\Helper;

use InvalidArgumentException;
use Throwable;
use function is_numeric;
use function json_encode;
use function method_exists;
use function property_exists;
use ReflectionProperty;
use function spl_object_hash;
use function ucfirst;

/**
 * Object helper
 *
 * @since 2.0
 */
class ObjectHelper
{
    /**
     * Base types
     */
    public const BASE_TYPES = [
        'bool',
        'boolean',
        'string',
        'int',
        'integer',
        'float',
        'double',
        'array'
    ];

    /**
     * Return object hash value
     *
     * @param object $object
     *
     * @return string
     */
    public static function hash($object): string
    {
        return spl_object_hash($object);
    }

    /**
     * Set the property value for the object
     * - Will try to set properties using the setter method
     * - Then, try to set properties directly
     *
     * @param mixed $object An object instance
     * @param array $options
     *
     * @return mixed
     */
    public static function init($object, array $options)
    {
        foreach ($options as $property => $value) {
            if (is_numeric($property)) {
                continue;
            }

            $setter = 'set' . ucfirst($property);

            // has setter
            if (method_exists($object, $setter)) {
                $object->$setter($value);
            } elseif (property_exists($object, $property)) {
                $object->$property = $value;
            }
        }

        return $object;
    }

    /**
     * Parse the type of binding param
     *
     * @param string $type  the type of param
     * @param mixed  $value the value of param
     *
     * @return mixed
     */
    public static function parseParamType(string $type, $value)
    {
        try {
            switch ($type) {
                case 'integer':
                case 'int':
                    $value = (int)$value;
                    break;
                case 'string':
                    $value = (string)$value;
                    break;
                case 'bool':
                case 'boolean':
                    $value = (bool)$value;
                    break;
                case 'float':
                    $value = (float)$value;
                    break;
                case 'double':
                    $value = (double)$value;
                    break;
                case 'array':
                    if (is_string($value)) {
                        $value = json_decode($value, true) ?: [];
                    }
                    $value = (array)$value;
                    break;
            }
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                sprintf('Error on convert value(%s) to %s', json_encode($value), $type)
            );
        }

        return $value;
    }

    /**
     * @param ReflectionProperty $property
     *
     * @return string
     */
    public static function getPropertyBaseType(ReflectionProperty $property): string
    {
        $docComment = $property->getDocComment();
        if (!$docComment) {
            return '';
        }

        // Get the content of the @var annotation
        if (preg_match('/@var\s+([^\s]+)/', $docComment, $matches)) {
            [, $type] = $matches;

            if (in_array($type, self::BASE_TYPES, true)) {
                return $type;
            }
        }

        return '';
    }

    /**
     * Get default by type
     *
     * @param string
     *
     * @return mixed
     */
    public static function getDefaultValue(string $type)
    {
        $value = null;
        switch ($type) {
            case 'int':
            case 'integer':
                $value = 0;
                break;
            case 'string':
                $value = '';
                break;
            case 'bool':
            case 'boolean':
                $value = false;
                break;
            case 'float':
                $value = 0;
                break;
            case 'double':
                $value = 0;
                break;
        }

        return $value;
    }
}
