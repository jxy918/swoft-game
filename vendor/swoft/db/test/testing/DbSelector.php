<?php declare(strict_types=1);


namespace SwoftTest\Db\Testing;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Db\Connection\Connection;
use Swoft\Db\Contract\DbSelectorInterface;

/**
 * Class DbSelector
 *
 * @since 2.0
 *
 * @Bean()
 */
class DbSelector implements DbSelectorInterface
{
    /**
     * @param Connection $connection
     */
    public function select(Connection $connection): void
    {
        $dbName = $connection->getDb();
        if ($dbName == 'test2') {
            $connection->db('test');
        }
    }
}