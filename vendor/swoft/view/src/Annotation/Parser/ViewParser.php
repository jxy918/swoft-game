<?php declare(strict_types=1);

namespace Swoft\View\Annotation\Parser;

use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\View\Annotation\Mapping\View;
use Swoft\View\ViewRegister;

/**
 * Class ViewParser
 *
 * @since 2.0
 * @AnnotationParser(View::class)
 */
class ViewParser extends Parser
{
    /**
     * Parse object
     *
     * @param int  $type       Class or Method or Property
     * @param View $annotation Annotation object
     *
     * @return array
     * Return empty array is nothing to do!
     * When class type return [$beanName, $className, $scope, $alias, $size] is to inject bean
     * When property type return [$propertyValue, $isRef] is to reference value
     * @throws AnnotationException
     */
    public function parse(int $type, $annotation): array
    {
        if ($type !== self::TYPE_METHOD) {
            throw new AnnotationException('`@View` must be defined on class method!');
        }

        ViewRegister::bindView(
            $this->className,
            $this->methodName,
            $annotation->getTemplate(),
            $annotation->getLayout()
        );

        return [];
    }
}

