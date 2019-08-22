<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Testing\Controller;

use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Validator\Annotation\Mapping\Validate;
use SwoftTest\Http\Server\Testing\Validator\DefaultValidator;
use SwoftTest\Http\Server\Testing\Validator\UserBaseValidate;
use Swoft\Validator\Annotation\Mapping\ValidateType;

/**
 * Class ValidatorController
 *
 * @since 2.0
 *
 * @Controller(prefix="testValidator")
 */
class ValidatorController
{
    /**
     * @Validate(validator=DefaultValidator::class)
     * @RequestMapping(route="defautValidator")
     *
     * @param Request $request
     *
     * @return array
     */
    public function defaultValidator(Request $request)
    {
        $data            = $request->getParsedBody();
        $data['kString'] = $request->parsedBody('string');
        $data['noKey']   = $request->parsedBody('noKey', 'not');

        return $data;
    }

    /**
     * @Validate(validator=UserBaseValidate::class)
     * @Validate(validator="testUserValidtor", params={1,2})
     *
     * @RequestMapping(route="userValidator")
     *
     * @param Request $request
     *
     * @return array
     */
    public function userValidator(Request $request)
    {
        $data = $request->getParsedBody();

        return $data;
    }


    /**
     * @Validate(validator=DefaultValidator::class, type=ValidateType::GET)
     * @RequestMapping(route="defaultValidatorQuery")
     *
     * @param Request $request
     *
     * @return array
     */
    public function defaultValidatorQuery(Request $request)
    {
        $data = $request->getParsedQuery();

        $data['kString'] = $request->parsedQuery('string');
        $data['noKey']   = $request->parsedQuery('noKey', 'not');

        return $data;
    }

    /**
     * @Validate(validator=UserBaseValidate::class, type=ValidateType::GET)
     * @Validate(validator="testUserValidtor", params={1,2}, type=ValidateType::GET)
     *
     * @RequestMapping(route="userValidatorQuery")
     *
     * @param Request $request
     *
     * @return array
     */
    public function userValidatorQuery(Request $request)
    {
        $data = $request->getParsedQuery();

        return $data;
    }

    /**
     * @RequestMapping()
     *
     * @param Request $request
     *
     * @return array
     */
    public function noToValidate(Request $request): array
    {
        return [$request->getParsedBody()];
    }
}
