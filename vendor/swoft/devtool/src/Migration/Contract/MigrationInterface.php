<?php declare(strict_types=1);


namespace Swoft\Devtool\Migration\Contract;

/**
 * Class MigrationInterface
 *
 * @since 2.0
 */
interface MigrationInterface
{
    /**
     * @return void
     */
    public function up(): void;

    /**
     * @return void
     */
    public function down(): void;
}
