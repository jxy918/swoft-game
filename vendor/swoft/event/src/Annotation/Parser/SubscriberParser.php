<?php declare(strict_types=1);

namespace Swoft\Event\Annotation\Parser;

use Doctrine\Common\Annotations\AnnotationException;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Event\Annotation\Mapping\Subscriber;
use Swoft\Event\ListenerRegister;

/**
 * Class ListenerParser
 *
 * @since   2.0
 * @package Swoft\Event\Annotation\Parser
 *
 * @AnnotationParser(Subscriber::class)
 */
class SubscriberParser extends Parser
{
    /**
     * @param int        $type
     * @param Subscriber $annotation
     *
     * @return array
     * @throws AnnotationException
     */
    public function parse(int $type, $annotation): array
    {
        if ($type !== self::TYPE_CLASS) {
            throw new AnnotationException('`@Subscriber` must be defined on class!');
        }

        ListenerRegister::addSubscriber($this->className);

        return [$this->className, $this->className, Bean::SINGLETON, ''];
    }
}
