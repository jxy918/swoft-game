<?php declare(strict_types=1);


namespace Swoft\Db\Contract;

use PDO;

/**
 * Class ConnectorInterface
 *
 * @since 2.0
 */
interface ConnectorInterface
{
    /**
     * Establish a database connection.
     *
     * @param  array $config
     *
     * @return PDO
     */
    public function connect(array $config): PDO;
}