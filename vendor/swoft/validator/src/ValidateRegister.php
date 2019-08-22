<?php declare(strict_types=1);


namespace Swoft\Validator;

/**
 * Class ValidateRegister
 *
 * @since 2.0
 */
class ValidateRegister
{
    /**
     * @var array
     *
     * @example
     * [
     *     'className' => [
     *         'methodName' => [
     *              'validatorName' => [
     *                  'validator' => 'validatorName',
     *                  'fields' => ['a', 'b'],
     *                  'unfields' => ['c', 'd'],
     *                  'params' => [1,2]
     *                  'message' => 'Fail message',
     *                  'type' => 'body'
     *              ]
     *          ]
     *     ]
     * ]
     */
    private static $validates = [];

    /**
     * @param string $className
     * @param string $method
     * @param string $validator
     * @param array  $fields
     * @param array  $unfields
     * @param array  $params
     * @param string $message
     * @param string $type
     */
    public static function registerValidate(
        string $className,
        string $method,
        string $validator,
        array $fields,
        array $unfields,
        array $params,
        string $message,
        string $type
    ): void {
        self::$validates[$className][$method][$validator] = [
            'validator' => $validator,
            'fields'    => $fields,
            'unfields'  => $unfields,
            'params'    => $params,
            'message'   => $message,
            'type'      => $type
        ];
    }

    /**
     * @param string $className
     * @param string $method
     *
     * @return array
     */
    public static function getValidates(string $className, string $method): array
    {
        return self::$validates[$className][$method] ?? [];
    }
}