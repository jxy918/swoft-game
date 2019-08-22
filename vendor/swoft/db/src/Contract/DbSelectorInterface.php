<?php declare(strict_types=1);


namespace Swoft\Db\Contract;

use Swoft\Db\Connection\Connection;
use Swoft\Db\Database;

/**
 * Class DbSelectInterface
 *
 * @since 2.0
 */
interface DbSelectorInterface
{
    /**
     * @param Connection $connection
     */
    public function select(Connection $connection): void;
}