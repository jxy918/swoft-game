<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Testing\Validator;


use Swoft\Stdlib\Helper\JsonHelper;
use Swoft\Validator\Annotation\Mapping\Validator;
use Swoft\Validator\Contract\ValidatorInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class UserValidator
 *
 * @since 2.0
 *
 * @Validator(name="testUserValidtor")
 */
class UserValidator implements ValidatorInterface
{
    /**
     * @param array $data
     * @param array $params
     *
     * @return array
     * @throws ValidatorException
     */
    public function validate(array $data, array $params): array
    {
        $start          = $data['start'];
        $end            = $data['end'];
        $data['params'] = $params;

        if ($start < $end) {
            return $data;
        }

        $md = md5(JsonHelper::encode($params));
        throw new ValidatorException(sprintf('Start(%s) must less than end', $md));
    }
}