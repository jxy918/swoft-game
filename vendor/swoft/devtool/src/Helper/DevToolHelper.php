<?php declare(strict_types=1);

namespace Swoft\Devtool\Helper;

/**
 * Class DevToolHelper
 */
class DevToolHelper
{
    /**
     * @param string $file
     * @return array
     */
    public static function parseComposerLockFile(string $file): array
    {
        if (!\is_file($file)) {
            return [];
        }

        if (!$json = \file_get_contents($file)) {
            return [];
        }

        /** @var array[] $data */
        $data = \json_decode($json, true);
        $components = [];

        if (!$data || !isset($data['packages'])) {
            return [];
        }

        foreach ($data['packages'] as $package) {
            if (0 !== \strpos($package['name'], 'swoft/')) {
                continue;
            }

            $components[] = [
                'name' => $package['name'],
                'version' => $package['version'],
                'source' => $package['source'],
                'require' => $package['require'] ?? [],
                'description' => $package['description'],
                'keywords' => $package['keywords'],
                'time' => $package['time'],
            ];
        }

        return $components;
    }
}
