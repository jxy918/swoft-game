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

use Swoft\Core\RequestContext;
use Swoft\Db\Exception\MysqlException;
use Swoft\Db\Helper\DbHelper;
use Swoft\Pool\AbstractConnection;

/**
 * Abstract database connection
 */
abstract class AbstractDbConnection extends AbstractConnection implements DbConnectionInterface
{
    /**
     * @var string
     */
    protected $originDb = '';

    /**
     * @var string
     */
    protected $currentDb = '';

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->pool->getDriver();
    }

    /**
     * @param bool $release
     */
    public function release($release = false)
    {
        if (!empty($this->currentDb) && $this->currentDb !== $this->originDb) {
            $this->selectDb($this->originDb);
        }

        parent::release($release);
    }

    /**
     * Parse uri
     *
     * @param string $uri
     *
     * @return array
     * @throws MysqlException
     */
    protected function parseUri(string $uri): array
    {
        $parseAry = \parse_url($uri);

        if (!isset($parseAry['host'], $parseAry['port'], $parseAry['path'], $parseAry['query'])) {
            throw new MysqlException('Uri format error uri=' . $uri);
        }

        $parseAry['database'] = \str_replace('/', '', $parseAry['path']);
        $query                = $parseAry['query'];

        \parse_str($query, $options);

        if (!isset($options['user'], $options['password'])) {
            throw new MysqlException('Lack of username and password，uri=' . $uri);
        }

        if (!isset($options['charset'])) {
            $options['charset'] = '';
        }

        $configs = \array_merge($parseAry, $options);
        unset($configs['path'], $configs['query']);

        return $configs;
    }

    /**
     * @param string $sql
     */
    protected function pushSqlToStack(string $sql)
    {
        $contextSqlKey = DbHelper::getContextSqlKey();

        /* @var \SplStack $stack */
        $stack = RequestContext::getContextDataByKey($contextSqlKey, new \SplStack());
        $stack->push($sql);

        RequestContext::setContextDataByKey($contextSqlKey, $stack);
    }
}
