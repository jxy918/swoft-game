<?php declare(strict_types=1);


namespace Swoft\Validator\Annotation\Parser;


use ReflectionException;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\Validator;
use Swoft\Validator\ValidatorRegister;

/**
 * Class ValidatorParser
 *
 * @since 2.0
 *
 * @AnnotationParser(Validator::class)
 */
class ValidatorParser extends Parser
{
    /**
     * @param int       $type
     * @param Validator $annotationObject
     *
     * @return array
     * @throws ReflectionException
     */
    public function parse(int $type, $annotationObject): array
    {
        $beanName = $this->className;
        $name     = $annotationObject->getName();
        if (!empty($name)) {
            $beanName = $name;
        }

        ValidatorRegister::registerValidator($this->className, $beanName);

        return [$beanName, $this->className, Bean::SINGLETON, ''];
    }
}