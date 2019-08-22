<?php declare(strict_types=1);


namespace Swoft\Validator;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Validator\Annotation\Mapping\IsBool;
use Swoft\Validator\Annotation\Mapping\IsFloat;
use Swoft\Validator\Annotation\Mapping\IsInt;
use Swoft\Validator\Annotation\Mapping\IsString;
use Swoft\Validator\Annotation\Mapping\ValidateType;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Contract\ValidatorInterface;
use Swoft\Validator\Exception\ValidatorException;
use function sprintf;

/**
 * Class Validator
 *
 * @since 2.0
 *
 * @Bean(name="validator")
 */
class Validator
{
    /***
     * @param array  $data
     * @param string $validatorName
     * @param array  $fields
     * @param array  $userValidators
     *
     * @param array  $unfields
     *
     * @return array
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ValidatorException
     */
    public function validate(
        array $data,
        string $validatorName,
        array $fields = [],
        array $userValidators = [],
        array $unfields = []
    ): array {
        if (empty($data)) {
            throw new ValidatorException('Validator data is empty!');
        }

        $type      = ValidatorRegister::TYPE_DEFAULT;
        $validator = ValidatorRegister::getValidator($validatorName);

        if (empty($validator)) {
            throw new ValidatorException(
                sprintf('Validator(%s) is not exist!', $validatorName)
            );
        }

        $data = $this->validateValidator($data, $type, $validatorName, [], $validator, $fields, $unfields);
        if (empty($userValidators)) {
            return $data;
        }

        foreach ($userValidators as $userValidator => $params) {
            if (is_int($userValidator)) {
                $userValidator = $params;
                $params        = [];
            }

            $validator = ValidatorRegister::getValidator($userValidator);

            // Check type
            $type = $validator['type'];
            if ($type != ValidatorRegister::TYPE_USER) {
                throw new ValidatorException(
                    sprintf('Validator(%s) is user validator!', $userValidator)
                );
            }

            $data = $this->validateValidator($data, $type, $userValidator, $params, $validator, $fields, $unfields);
        }

        return $data;
    }

    /**
     * @param array $body
     * @param array $validates
     * @param array $query
     *
     * @return array
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ValidatorException
     */
    public function validateRequest(array $body, array $validates, array $query = []): array
    {
        foreach ($validates as $validateName => $validate) {
            $validator = ValidatorRegister::getValidator($validateName);

            if (empty($validator)) {
                throw new ValidatorException(
                    sprintf('Validator(%s) is not exist!', $validateName)
                );
            }

            $type     = $validator['type'];
            $fields   = $validate['fields'] ?? [];
            $unfields = $validate['unfields'] ?? [];
            $params   = $validate['params'] ?? [];

            $validateType = $validate['type'];

            // Get query params
            if ($validateType == ValidateType::GET) {
                $query = $this->validateValidator($query, $type, $validateName, $params, $validator, $fields,
                    $unfields);
                continue;
            }

            $body = $this->validateValidator($body, $type, $validateName, $params, $validator, $fields, $unfields);
        }
        return [$body, $query];
    }

    /**
     * @param array  $data
     * @param int    $type
     * @param string $validateName
     * @param array  $params
     * @param array  $validator
     * @param array  $fields
     *
     * @param array  $unfields
     *
     * @return array
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ValidatorException
     */
    protected function validateValidator(
        array $data,
        int $type,
        string $validateName,
        array $params,
        array $validator,
        array $fields,
        array $unfields = []
    ): array {
        // User validator
        if ($type == ValidatorRegister::TYPE_USER) {
            return $this->validateUserValidator($validateName, $data, $params);
        }

        return $this->validateDefaultValidator($data, $validator, $fields, $unfields);
    }

    /**
     * @param array $data
     * @param array $validator
     * @param array $fields
     *
     * @param array $unfields
     *
     * @return array
     * @throws ContainerException
     * @throws ReflectionException
     */
    protected function validateDefaultValidator(array $data, array $validator, array $fields, array $unfields): array
    {
        $properties = $validator['properties'] ?? [];
        foreach ($properties as $propName => $property) {
            if (!empty($fields) && !in_array($propName, $fields)) {
                continue;
            }

            // Unfields
            if (in_array($propName, $unfields)) {
                continue;
            }

            /* @var IsString|IsInt|IsBool|IsFloat $type */
            $type        = $property['type']['annotation'] ?? null;
            $default     = $property['type']['default'] ?? null;
            $annotations = $property['annotations'] ?? [];
            if ($type === null) {
                continue;
            }

            $name     = $type->getName();
            $propName = (empty($name)) ? $propName : $name;

            // Default validate item(Type) and other item
            $data = $this->validateDefaultItem($data, $propName, $type, $default);
            foreach ($annotations as $annotation) {
                $data = $this->validateDefaultItem($data, $propName, $annotation);
            }
        }

        return $data;
    }

    /**
     * @param array  $data
     * @param string $propName
     * @param object $item
     * @param mixed  $default
     *
     * @return array
     * @throws ContainerException
     * @throws ReflectionException
     */
    protected function validateDefaultItem(array $data, string $propName, $item, $default = null): array
    {
        $itemClass = get_class($item);

        /* @var RuleInterface $rule */
        $rule = BeanFactory::getBean($itemClass);
        $data = $rule->validate($data, $propName, $item, $default);
        return $data;
    }

    /**
     * @param string $validateName
     * @param array  $data
     * @param array  $params
     *
     * @return array
     * @throws ValidatorException
     * @throws ReflectionException
     * @throws ContainerException
     */
    protected function validateUserValidator(string $validateName, array $data, array $params): array
    {
        $validator = BeanFactory::getBean($validateName);
        if (!$validator instanceof ValidatorInterface) {
            throw new ValidatorException(
                sprintf('User validator(%s) must instance of ValidatorInterface', $validateName)
            );
        }

        return $validator->validate($data, $params);
    }
}
