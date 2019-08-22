<?php declare(strict_types=1);


namespace Swoft\Bean\Annotation\Parser;


use ReflectionClass;
use ReflectionException;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Bean\Annotation\Mapping\Primary;
use Swoft\Bean\InterfaceRegister;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class PrimaryParser
 *
 * @since 2.0
 *
 * @AnnotationParser(annotation=Primary::class)
 */
class PrimaryParser extends Parser
{
    /**
     * @param int     $type
     * @param Primary $annotationObject
     *
     * @return array
     * @throws ReflectionException
     * @throws ValidatorException
     */
    public function parse(int $type, $annotationObject): array
    {
        $rc = new ReflectionClass($this->className);

        $interfaces = $rc->getInterfaces();
        if (empty($interfaces)) {
            return [];
        }

        foreach ($interfaces as $interface) {
            InterfaceRegister::registerPrimary($interface->getName(), $this->className);
        }

        return [];
    }
}