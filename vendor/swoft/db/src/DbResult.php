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

use Swoft\Core\AbstractResult;
use Swoft\Db\Helper\EntityHelper;

/**
 * DbResult
 */
abstract class DbResult extends AbstractResult
{
    /**
     * Result type
     *
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $className = '';

    /**
     * @var array
     */
    protected $decorators = [];

    /**
     * @param int $type
     * @return $this
     */
    public function setType(int $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $className
     * @return $this
     */
    public function setClassName(string $className): self
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @param array $decorators
     * @return $this
     */
    public function setDecorators(array $decorators): self
    {
        $this->decorators = array_reverse($decorators);
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getResultByClassName()
    {
        $className = $this->className;
        $result = $this->getResultByType();

        if (!empty($className) && $this->type == Db::RETURN_FETCH && empty($result)) {
            return [];
        }

        if (!empty($className) && $this->type == Db::RETURN_ONE && empty($result)) {
            return null;
        }

        return $result;
    }

    /**
     * @return mixed
     */
    private function getResultByType()
    {
        if ($this->connection === null) {
            return $this->result;
        }

        /* @var AbstractDbConnection $connection */
        $connection = $this->connection;

        if ($this->type == Db::RETURN_INSERTID) {
            return $this->connection->getInsertId();
        }

        if ($this->type == Db::RETURN_ROWS) {
            return $connection->getAffectedRows();
        }

        if ($this->type == Db::RETURN_FETCH) {
            return $connection->fetch();
        }

        $result = $connection->fetch();
        $result = $result[0]??[];

        return $result;
    }
}
