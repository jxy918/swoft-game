<?php declare(strict_types=1);


namespace Swoft\Process\Annotation\Parser;


use Exception;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Process\Annotation\Mapping\Process;
use Swoft\Process\ProcessRegister;

/**
 * Class ProcessParser
 *
 * @since 2.0
 *
 * @AnnotationParser(annotation=Process::class)
 */
class ProcessParser extends Parser
{
    /**
     * @param int     $type
     * @param Process $annotationObject
     *
     * @return array
     * @throws Exception
     */
    public function parse(int $type, $annotationObject): array
    {
        // Register
        ProcessRegister::registerProcess($this->className, $annotationObject->getWorkerId());

        return [$this->className, $this->className, Bean::SINGLETON, ''];
    }
}