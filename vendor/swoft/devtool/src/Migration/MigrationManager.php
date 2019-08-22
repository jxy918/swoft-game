<?php declare(strict_types=1);


namespace Swoft\Devtool\Migration;

use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class Migration
 *
 * @since 2.0
 *
 * @Bean(name="migrationManager")
 */
class MigrationManager
{
    /**
     * @var string
     */
    private $migrationPath = '@app/Migration';

    /**
     * @var string
     */
    private $templateDir = '@devtool/devtool/resource/template';

    /**
     * @var string
     */
    private $templateFile = 'migration';

    /**
     * @return string
     */
    public function getMigrationPath(): string
    {
        return $this->migrationPath;
    }

    /**
     * @param string $migrationPath
     */
    public function setMigrationPath(string $migrationPath): void
    {
        $this->migrationPath = $migrationPath;
    }

    /**
     * @return string
     */
    public function getTemplateDir(): string
    {
        return $this->templateDir;
    }

    /**
     * @param string $templateDir
     */
    public function setTemplateDir(string $templateDir): void
    {
        $this->templateDir = $templateDir;
    }

    /**
     * @return string
     */
    public function getTemplateFile(): string
    {
        return $this->templateFile;
    }

    /**
     * @param string $templateFile
     */
    public function setTemplateFile(string $templateFile): void
    {
        $this->templateFile = $templateFile;
    }
}
