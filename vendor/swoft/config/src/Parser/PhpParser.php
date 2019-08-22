<?php declare(strict_types=1);

namespace Swoft\Config\Parser;


use SplFileInfo;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Config\Config;
use Swoft\Config\Exception\ConfigException;
use Swoft\Stdlib\Helper\Arr;
use Swoft\Stdlib\Helper\ArrayHelper;
use Swoft\Stdlib\Helper\DirectoryHelper;
use function is_dir;

/**
 * Class PhpParser
 *
 * @Bean("phpParser")
 */
class PhpParser extends Parser
{
    /**
     * Parse php files
     *
     * @param Config $config
     *
     * @return array
     * @throws ConfigException
     */
    public function parse(Config $config): array
    {
        $base         = $config->getBase();
        $env          = $config->getEnv();
        $path         = $config->getPath();
        $envPath      = sprintf('%s%s%s', $path, DIRECTORY_SEPARATOR, $env);
        $baseFileName = sprintf('%s.%s', $base, Config::TYPE_PHP);

        $phpConfig = $this->getConfig($baseFileName, $path);

        if (!empty($env) && !file_exists($envPath)) {
            throw new ConfigException(
                sprintf('Env path(%s) is not exist!', $envPath)
            );
        }

        if (!empty($env)) {
            $envConfig = $this->getConfig($baseFileName, $envPath);
            $phpConfig = Arr::merge($phpConfig, $envConfig);
        }

        return $phpConfig;
    }

    /**
     * @param string $baseFileName
     * @param string $path
     *
     * @return array
     */
    protected function getConfig(string $baseFileName, string $path): array
    {
        $iterator = DirectoryHelper::iterator($path);

        $baseConfig  = [];
        $otherConfig = [];

        /* @var SplFileInfo $splFileInfo */
        foreach ($iterator as $splFileInfo) {

            // Ingore other extension file
            $ext = $splFileInfo->getExtension();
            $ext = strtolower($ext);

            if ($ext != Config::TYPE_PHP) {
                continue;
            }

            $fileName = $splFileInfo->getFilename();
            $fileName = strtolower($fileName);
            $filePath = $splFileInfo->getPathname();

            // Exclude dir
            if (is_dir($filePath)) {
                continue;
            }

            // Base config
            if ($fileName == $baseFileName) {
                $baseConfig = require $filePath;
                continue;
            }

            // Other config
            [$key] = explode('.', $fileName);
            $data = require $filePath;

            ArrayHelper::set($otherConfig, $key, $data);
        }

        return ArrayHelper::merge($baseConfig, $otherConfig);
    }
}