<?php declare(strict_types=1);


namespace Swoft\Validator\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Validator\Annotation\Mapping\Validate;
use Swoft\Validator\ValidateRegister;

/**
 * Class ValidateParser
 *
 * @since 2.0
 *
 * @AnnotationParser(Validate::class)
 */
class ValidateParser extends Parser
{
    /**
     * @param int      $type
     * @param Validate $annotationObject
     *
     * @return array
     */
    public function parse(int $type, $annotationObject): array
    {
        $validator = $annotationObject->getValidator();
        $fields    = $annotationObject->getFields();
        $params    = $annotationObject->getParams();
        $type      = $annotationObject->getType();
        $unfields  = $annotationObject->getUnfields();

        ValidateRegister::registerValidate($this->className, $this->methodName, $validator, $fields, $unfields, $params,
            '',
            $type);

        return [];
    }
}