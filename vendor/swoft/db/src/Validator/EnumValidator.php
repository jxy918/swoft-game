<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db\Validator;

use Swoft\Bean\Annotation\Bean;
use Swoft\Exception\ValidatorException;

/**
 * @Bean("DbEnumValidator")
 */
class EnumValidator implements ValidatorInterface
{
    /**
     * @param string $column    Colunm name
     * @param mixed  $value     Column value
     * @param array  ...$params Other parameters
     * @throws ValidatorException When validation failures, will throw an Exception
     * @return bool When validation successful
     */
    public function validate(string $column, $value, ...$params): bool
    {
        if (! isset($params[0][0]) || ! \in_array($value, $params[0][0], false)) {
            throw new ValidatorException('数据库字段值验证失败，不是在枚举集合的里面，column=' . $column . ' enum=' . json_encode($params[0][0]));
        }
        return true;
    }
}
