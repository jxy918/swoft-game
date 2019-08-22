<?php declare(strict_types=1);


namespace SwoftTest\Limiter\Testing;

/**
 * Class KeyHelper
 *
 * @since 2.0
 */
class KeyHelper
{
    /**
     * @param int    $id
     * @param string $type
     *
     * @return string
     */
    public function getKey(int $id, string $type): string
    {
        return sprintf('key:%d:%s', $id, $type);
    }
}