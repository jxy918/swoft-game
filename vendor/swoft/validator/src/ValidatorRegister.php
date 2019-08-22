<?php declare(strict_types=1);


namespace Swoft\Validator;

use ReflectionClass;
use ReflectionException;
use function sprintf;
use Swoft\Validator\Annotation\Mapping\Type;
use Swoft\Validator\Contract\ValidatorInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class ValidatorRegister
 *
 * @since 2.0
 */
class ValidatorRegister
{
    /**
     * Default
     */
    public const TYPE_DEFAULT = 1;

    /**
     * User
     */
    public const TYPE_USER = 2;

    /**
     * @var array
     *
     * @example
     * [
     *     'className' => [
     *         'name' => '',
     *         'class' => '',
     *         'type' => 1,
     *         'properties' => [
     *            'propName' => [
     *                 'type' => [
     *                      'default' => '11',
     *                      'annotation' => XxxType(),
     *                  ],
     *                 'annotations' => [
     *                     AnnotationObject(Email/Max/Min),
     *                     AnnotationObject(Email/Max/Min),
     *                 ]
     *            ],
     *         ]
     *     ],
     *     'className' => [
     *         'name' => '',
     *         'class' => '',
     *         'type' => 2,
     *         'properties' => [
     *            'propName' => [
     *                 'type' => [
     *                      'default' => '11',
     *                      'annotation' => XxxType(),
     *                  ],
     *                 'annotations' => [
     *                     AnnotationObject(Email/Max/Min),
     *                     AnnotationObject(Email/Max/Min),
     *                 ]
     *            ],
     *         ]
     *     ],
     * ]
     */
    private static $validators = [];

    /**
     * @var array
     * @example
     * [
     *     'className' => 'validateName'
     * ]
     */
    private static $validatorClasses = [];

    /**
     * @param string $className
     * @param string $validatorName
     *
     * @throws ReflectionException
     */
    public static function registerValidator(string $className, string $validatorName): void
    {
        $reflectClass = new ReflectionClass($className);
        $interfaces   = $reflectClass->getInterfaceNames();

        $type = self::TYPE_DEFAULT;
        if (in_array(ValidatorInterface::class, $interfaces)) {
            $type = self::TYPE_USER;
        }

        self::$validators[$validatorName] = [
            'name'  => $validatorName,
            'class' => $className,
            'type'  => $type,
        ];

        self::$validatorClasses[$className] = $validatorName;
    }

    /**
     * @param string $className
     * @param string $propertyName
     * @param object $objAnnotation
     *
     * @throws ValidatorException
     * @throws ReflectionException
     */
    public static function registerValidatorItem(string $className, string $propertyName, $objAnnotation): void
    {
        if (!isset(self::$validatorClasses[$className])) {
            throw new ValidatorException(
                sprintf('%s must be define class `@Validate()`', get_class($objAnnotation))
            );
        }

        $validateName = self::$validatorClasses[$className];

        $type = self::$validators[$validateName]['properties'][$propertyName]['type'] ?? [];
        if (!empty($type) && $objAnnotation instanceof Type) {
            throw new ValidatorException(
                sprintf('Only one `@XxxType` can be defined(propterty=%s)!', $propertyName)
            );
        }

        if ($objAnnotation instanceof Type) {
            $rc          = new ReflectionClass($className);
            $defaultProp = $rc->getProperty($propertyName);
            $defaultProp->setAccessible(true);

            $default = $defaultProp->getValue(new $className());

            self::$validators[$validateName]['properties'][$propertyName]['type']['default']    = $default;
            self::$validators[$validateName]['properties'][$propertyName]['type']['annotation'] = $objAnnotation;
            return;
        }

        self::$validators[$validateName]['properties'][$propertyName]['annotations'][] = $objAnnotation;
    }

    /**
     * @throws ValidatorException
     */
    public static function checkValidators()
    {
        foreach (self::$validators as $className => $values) {
            if ($values['type'] == self::TYPE_USER) {
                continue;
            }

            $properties = $values['properties'] ?? [];
            foreach ($properties as $propName => $propValues) {
                $type = $propValues['type'] ?? null;
                if (empty($type)) {
                    throw new ValidatorException(
                        sprintf('Property(%s->%s) must be define `@XxxType`', $className, $propName)
                    );
                }
            }
        }
    }

    /**
     * @param string $validateName
     *
     * @return array
     */
    public static function getValidator(string $validateName): array
    {
        return self::$validators[$validateName] ?? [];
    }
}