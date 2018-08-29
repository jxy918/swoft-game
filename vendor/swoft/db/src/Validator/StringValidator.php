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
 * @Bean("DbStringValidator")
 */
class StringValidator implements ValidatorInterface
{
    /**
     * @param string $column    Column name
     * @param mixed  $value     Column value
     * @param array  ...$params Other parameters
     * @throws ValidatorException When validation failures, will throw an Exception
     * @return bool When validation successful
     */
    public function validate(string $column, $value, ...$params): bool
    {
        if (! \is_string($value)) {
            throw new ValidatorException('数据库字段值验证失败，不是string类型，column=' . $column);
        }
        if (isset($params[0]) && \is_int($params[0]) && mb_strlen($value) > $params[0]) {
            throw new ValidatorException('数据库字段值验证失败，string类型，column=' . $column . '，字符串超过最大长度，length=' . $params[0]);
        }
        return true;
    }
}
