<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db;

/**
 * Database interface
 */
interface DbConnectionInterface
{
    /**
     * @param string $sql
     */
    public function prepare(string $sql);

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function execute(array $params = []);

    /**
     * Begin transaction
     */
    public function beginTransaction();

    /**
     * @return mixed
     */
    public function getInsertId();

    /**
     * @return int
     */
    public function getAffectedRows(): int;

    /**
     * @return mixed
     */
    public function fetch();

    /**
     * Rollback transaction
     */
    public function rollback();

    /**
     * Commit transaction
     */
    public function commit();

    /**
     * @param string $db
     *
     * @return void
     */
    public function selectDb(string $db);

    /**
     * Destroy
     */
    public function destroy();
}
