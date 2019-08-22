<?php declare(strict_types=1);

namespace Swoft\Event\Annotation\Parser;

use Doctrine\Common\Annotations\AnnotationException;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\ListenerRegister;

/**
 * Class ListenerParser
 *
 * @since 2.0
 *
 * @AnnotationParser(Listener::class)
 */
class ListenerParser extends Parser
{
    /**
     * @param int      $type
     * @param Listener $annotation
     *
     * @return array
     * @throws AnnotationException
     */
    public function parse(int $type, $annotation): array
    {
        if ($type !== self::TYPE_CLASS) {
            throw new AnnotationException('`@Listener` must be defined on class!');
        }

        // collect listeners
        ListenerRegister::addListener($this->className, [
            // event name => listener priority
            $annotation->getEvent() => $annotation->getPriority()
        ]);

        return [$this->className, $this->className, Bean::SINGLETON, ''];
    }
}
