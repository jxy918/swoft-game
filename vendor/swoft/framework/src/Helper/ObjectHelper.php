<?php

namespace Swoft\Helper;

/**
 * Class ObjectHelper
 * @package Swoft\Helper
 * @author inhere <in.798@qq.com>
 */
class ObjectHelper
{
    /**
     * Set the property value for the object
     * - Will try to set properties using the setter method
     * - Then, try to set properties directly
     * @param mixed $object An object instance
     * @param array $options
     * @return mixed
     */
    public static function init($object, array $options)
    {
        foreach ($options as $property => $value) {
            if (\is_numeric($property)) {
                continue;
            }

            $setter = 'set' . \ucfirst($property);

            // has setter
            if (\method_exists($object, $setter)) {
                $object->$setter($value);
            } else {
                $object->$property = $value;
            }
        }

        return $object;
    }
}
