<?php declare(strict_types=1);


namespace Swoft\Devtool\Migration;

/**
 * Class MigrationRegister
 *
 * @since 2.0
 */
class MigrationRegister
{

    /**
     * Migration array
     *
     * @var array
     *
     * @example
     * [
     *     'MigrationClassName' => [
     *         'time' => '20190623183144',
     *         'pool' => 'db.pool',
     *     ]
     * ]
     */
    private static $migration = [];

    /**
     * Migration array
     *
     * @var array
     *
     * @example
     * [
     *     'MigrationClassName20190623183144' => 'MigrationClassName'
     * ]
     */
    private static $migrationAlias = [];

    /**
     * Register migration
     *
     * @param string $migrationName
     * @param int    $time
     * @param string $pool
     */
    public static function registerMigration(string $migrationName, int $time, string $pool): void
    {
        // migrate alias
        $originClassName                        = sprintf('%s%s', $migrationName, $time);
        self::$migrationAlias[$originClassName] = $migrationName;

        self::$migration[$migrationName] = [
            'time' => $time,
            'pool' => $pool,
        ];
    }

    /**
     * @return array
     */
    public static function getMigrations(): array
    {
        return self::$migration;
    }

    /**
     * @param string $migrationName
     *
     * @return array
     */
    public static function getMigrationDetail(string $migrationName): array
    {
        $key = self::$migrationAlias[$migrationName] ?? $migrationName;

        return self::$migration[$key] ;
    }

    /**
     * @param string $migrationName
     *
     * @return bool
     */
    public static function checkExists(string $migrationName): bool
    {
        return isset(self::$migration[$migrationName]) || isset(self::$migrationAlias[$migrationName]);
    }
}
