<?php declare(strict_types=1);

namespace Swoft\Config\Annotation\Parser;

use function sprintf;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Config\Annotation\Mapping\Config;

/**
 * Class ConfigParser
 *
 * @AnnotationParser(Config::class)
 */
class ConfigParser extends Parser
{
    /**
     * Parse config annotation
     *
     * @param int    $type
     * @param Config $annotationObject
     *
     * @return array
     */
    public function parse(int $type, $annotationObject): array
    {
        if ($type !== Parser::TYPE_PROPERTY) {
            return [];
        }

        $key   = $annotationObject->getKey();
        $value = sprintf('.config.%s', $key);

        return [$value, true];
    }
}