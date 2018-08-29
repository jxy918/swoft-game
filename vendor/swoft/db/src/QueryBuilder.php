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

use Swoft\App;
use Swoft\Core\ResultInterface;
use Swoft\Db\Bean\Collector\EntityCollector;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Exception\MysqlException;
use Swoft\Db\Helper\DbHelper;
use Swoft\Db\Helper\EntityHelper;

/**
 * 查询器
 */
class QueryBuilder implements QueryBuilderInterface
{
    /**
     * 升序
     */
    const ORDER_BY_ASC = 'ASC';

    /**
     * 降序
     */
    const ORDER_BY_DESC = 'DESC';

    /**
     * 等于
     */
    const OPERATOR_EQ = '=';

    /**
     * 不等于
     */
    const OPERATOR_NE = '!=';

    /**
     * 小于
     */
    const OPERATOR_LT = '<';

    /**
     * 小于等于
     */
    const OPERATOR_LTE = '<=';

    /**
     * 大于
     */
    const OPERATOR_GT = '>';

    /**
     * 大于等于
     */
    const OPERATOR_GTE = '>=';

    /**
     * 左括号
     */
    const BRACKET_OPEN = '(';

    /**
     * 右括号
     */
    const BRACKET_CLOSE = ')';

    /**
     * 修饰符in
     */
    const IN = 'IN';

    /**
     * 修饰符not in
     */
    const NOT_IN = 'NOT IN';

    /**
     * 修饰符like
     */
    const LIKE = 'LIKE';

    /**
     * 修饰符in
     */
    const NOT_LIKE = 'NOT LIKE';

    /**
     * 修饰符between
     */
    const BETWEEN = 'BETWEEN';

    /**
     * 修饰符not between
     */
    const NOT_BETWEEN = 'NOT BETWEEN';

    /**
     * 内连接
     */
    const INNER_JOIN = 'INNER JOIN';

    /**
     * 左连接
     */
    const LEFT_JOIN = 'LEFT JOIN';

    /**
     * 右连接
     */
    const RIGHT_JOIN = 'RIGHT JOIN';

    /**
     * 逻辑运算符and
     */
    const LOGICAL_AND = 'AND';

    /**
     * 逻辑运算符or
     */
    const LOGICAL_OR = 'OR';

    /**
     * is判断语句
     */
    const IS = 'IS';

    /**
     * is not 判断语句
     */
    const IS_NOT = 'IS NOT';

    /**
     * 插入表名
     *
     * @var string
     */
    private $insert = '';

    /**
     * @var array
     */
    private $insertValues = [];

    /**
     * 更新表名
     *
     * @var string
     */
    private $update = '';

    /**
     * @var array
     */
    private $updateValues = [];

    /**
     * @var array
     */
    private $counterValues = [];

    /**
     * 是否是delete
     *
     * @var bool
     */
    private $delete = false;

    /**
     * select语句
     *
     * @var array
     */
    private $select = [];

    /**
     * set语句
     *
     * @var array
     */
    private $set = [];

    /**
     * from语句
     *
     * @var array
     */
    private $from = [];

    /**
     * join语句
     *
     * @var array
     */
    private $join = [];

    /**
     * where语句
     *
     * @var array
     */
    private $where = [];

    /**
     * group by语句
     *
     * @var array
     */
    private $groupBy = [];

    /**
     * having语句
     *
     * @var array
     */
    private $having = [];

    /**
     * order by 语句
     *
     * @var array
     */
    private $orderBy = [];

    /**
     * limit 语句
     *
     * @var array
     */
    protected $limit = [];

    /**
     * 参数集合
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * @var array
     */
    protected $table = [];

    /**
     * @var string
     */
    protected $instance = Pool::INSTANCE;

    /**
     * @var string
     */
    protected $node = '';

    /**
     * Selected database
     *
     * @var string
     */
    protected $db = '';

    /**
     * @var array
     */
    protected $aggregate = [];

    /**
     * @var array
     */
    protected $decorators = [];

    /**
     * @var string
     */
    protected $className = '';

    /**
     * @param array $values
     *
     * @return ResultInterface
     * @throws MysqlException
     */
    public function insert(array $values): ResultInterface
    {
        $this->insert                   = $this->getTableName();
        $this->insertValues['columns']  = array_keys($values);
        $this->insertValues['values'][] = array_values($values);

        return $this->execute();
    }

    /**
     * @param array $rows
     *
     * @return ResultInterface
     * @throws MysqlException
     */
    public function batchInsert(array $rows): ResultInterface
    {
        $this->insert = $this->getTableName();
        foreach ($rows as $row) {
            ksort($row);
            if (!isset($this->insertValues['columns'])) {
                $this->insertValues['columns'] = array_keys($row);
            }
            $this->insertValues['values'][] = array_values($row);
        }

        return $this->execute();
    }

    /**
     * @param array $values
     *
     * @return ResultInterface
     */
    public function update(array $values): ResultInterface
    {
        $this->update       = $this->getTableName();
        $this->updateValues = $values;

        return $this->execute();
    }

    /**
     * @param mixed $column
     * @param int   $amount
     *
     * @return \Swoft\Core\ResultInterface
     */
    public function counter($column, $amount = 1): ResultInterface
    {
        $this->update = $this->getTableName();
        if (is_array($column)) {
            $this->counterValues = $column;
        } else {
            $this->counterValues = [$column => $amount];
        }

        return $this->execute();
    }

    /**
     * delete语句
     *
     * @return ResultInterface
     */
    public function delete(): ResultInterface
    {
        $this->delete = true;

        return $this->execute();
    }

    /**
     * @param array $columns
     *
     * @return ResultInterface
     */
    public function get(array $columns = ['*']): ResultInterface
    {
        if (empty($columns)) {
            $columns = ['*'];
        }

        $isAllColumns = count($columns) == 1 && isset($columns[0]) && $columns[0] == '*';
        if (!empty($this->className) && $isAllColumns) {
            $columns = $this->getAllFields();
        }

        foreach ($columns as $column => $alias) {
            if (\is_int($column)) {
                $this->select[$alias] = null;
                continue;
            }
            $this->select[$column] = $alias;
        }

        $this->addGetDecorator();

        return $this->execute();
    }

    /**
     * @param array $columns
     *
     * @return ResultInterface
     */
    public function one(array $columns = ['*'])
    {
        $this->limit(1);
        $this->addOneDecorator();

        return $this->get($columns);
    }

    /**
     * @param string $table
     * @param string $alias
     *
     * @return QueryBuilder
     * @throws DbException
     */
    public function table(string $table, string $alias = null): self
    {
        $this->table['table'] = $this->getTableNameByClassName($table);
        $this->table['alias'] = $alias;

        return $this;
    }

    /**
     * inner join语句
     *
     * @param string       $table
     * @param string|array $criteria
     * @param string       $alias
     *
     * @return QueryBuilder
     * @throws \Swoft\Db\Exception\DbException
     */
    public function innerJoin(string $table, $criteria = null, string $alias = null): QueryBuilder
    {
        $table = $this->getTableNameByClassName($table);
        $this->join($table, $criteria, self::INNER_JOIN, $alias);

        return $this;
    }

    /**
     * left join语句
     *
     * @param string       $table
     * @param string|array $criteria
     * @param string       $alias
     *
     * @return QueryBuilder
     * @throws \Swoft\Db\Exception\DbException
     */
    public function leftJoin(string $table, $criteria = null, string $alias = null): QueryBuilder
    {
        $table = $this->getTableNameByClassName($table);
        $this->join($table, $criteria, self::LEFT_JOIN, $alias);

        return $this;
    }

    /**
     * right join语句
     *
     * @param string       $table
     * @param string|array $criteria
     * @param string       $alias
     *
     * @return QueryBuilder
     * @throws \Swoft\Db\Exception\DbException
     */
    public function rightJoin(string $table, $criteria = null, string $alias = null): QueryBuilder
    {
        $table = $this->getTableNameByClassName($table);
        $this->join($table, $criteria, self::RIGHT_JOIN, $alias);

        return $this;
    }

    /**
     * where语句
     *
     * @param string $column
     * @param mixed  $value
     * @param string $operator
     * @param string $connector
     *
     * @return QueryBuilder
     */
    public function where(string $column, $value, $operator = self::OPERATOR_EQ, $connector = self::LOGICAL_AND): QueryBuilder
    {
        $this->criteria($this->where, $column, $value, $operator, $connector);

        return $this;
    }

    /**
     * The $condition is array
     *
     * Format `['column1' => value1, 'column2' => value2, ...]`
     * - ['name' => 'swoft', 'status' => 1] => ('name'='swoft' and 'status' = 1)
     * - ['id' => [1, 2, 3], 'status' => 1] => ('id' in (1, 2, 3) and 'status' = 1)
     *
     * Format `[operator, operand1, operand2, ...]`
     * - ['id', '>', 12]
     * - ['id', '<', 13]
     * - ['id', '>=', 13]
     * - ['id', '<=', 13]
     * - ['id', '<>', 13]
     *
     * - ['id', 'in', [1, 2, 3]]
     * - ['id', 'not in', [1, 2, 3]]
     *
     * - ['id', 'between', 2, 3]
     * - ['id', 'not between', 2, 3]
     *
     * - ['name', 'like', '%swoft%']
     * - ['name', 'not like', '%swoft%']
     *
     *
     * @param array $condition
     *
     * @return \Swoft\Db\QueryBuilder
     */
    public function condition(array $condition): self
    {
        foreach ($condition as $key => $value) {
            if (\is_int($key) && is_array($value)) {
                $this->condition($value);
                continue;
            }
            if (is_int($key)) {
                $this->andCondition($condition);
                break;
            }
            if (\is_array($value)) {
                $this->whereIn($key, $value);
                continue;
            }
            $this->andWhere($key, $value);
        }

        return $this;
    }

    /**
     * @param array $condition
     */
    public function andCondition(array $condition)
    {
        list(, $operator) = $condition;
        $operator = strtoupper($operator);
        switch ($operator) {
            case self::OPERATOR_EQ:
            case self::OPERATOR_GT:
            case self::OPERATOR_NE:
            case self::OPERATOR_LT:
            case self::OPERATOR_LTE:
            case self::OPERATOR_GTE:
            case self::LIKE:
            case self::NOT_LIKE:
                list($column, $operator, $value) = $condition;
                $this->andWhere($column, $value, $operator);
                break;
            case self::IN:
                list($column, , $value) = $condition;
                $this->whereIn($column, $value);
                break;
            case self::NOT_IN:
                list($column, , $value) = $condition;
                $this->whereNotIn($column, $value);
                break;
            case self::BETWEEN:
                list($column, , $min, $max) = $condition;
                $this->whereBetween($column, $min, $max);
                break;
            case self::NOT_BETWEEN:
                list($column, , $min, $max) = $condition;
                $this->whereNotBetween($column, $min, $max);
                break;
        }
    }

    /**
     * where and 语句
     *
     * @param string $column
     * @param mixed  $value
     * @param string $operator
     *
     * @return QueryBuilder
     */
    public function andWhere(string $column, $value, $operator = self::OPERATOR_EQ): QueryBuilder
    {
        $this->criteria($this->where, $column, $value, $operator, self::LOGICAL_AND);

        return $this;
    }

    /**
     * where条件中，括号开始(左括号)
     *
     * @param string $connector
     *
     * @return QueryBuilder
     */
    public function openWhere($connector = self::LOGICAL_AND): QueryBuilder
    {
        return $this->bracketCriteria($this->where, self::BRACKET_OPEN, $connector);
    }

    /**
     * where条件中，括号结束(右括号)
     *
     * @return QueryBuilder
     */
    public function closeWhere(): QueryBuilder
    {
        return $this->bracketCriteria($this->where, self::BRACKET_CLOSE);
    }

    /**
     * where or 语句
     *
     * @param string $column
     * @param mixed  $value
     * @param string $operator
     *
     * @return QueryBuilder
     */
    public function orWhere($column, $value, $operator = self::OPERATOR_EQ): QueryBuilder
    {
        $this->criteria($this->where, $column, $value, $operator, self::LOGICAL_OR);

        return $this;
    }

    /**
     * where in 语句
     *
     * @param string $column
     * @param array  $values
     * @param string $connector
     *
     * @return QueryBuilder
     */
    public function whereIn(string $column, array $values, string $connector = self::LOGICAL_AND): QueryBuilder
    {
        if (!empty($values)) {
            $this->criteria($this->where, $column, $values, self::IN, $connector);
        }

        return $this;
    }

    /**
     * where not in 语句
     *
     * @param string $column
     * @param array  $values
     * @param string $connector
     *
     * @return QueryBuilder
     */
    public function whereNotIn(string $column, array $values, string $connector = self::LOGICAL_AND): QueryBuilder
    {
        if (!empty($values)) {
            $this->criteria($this->where, $column, $values, self::NOT_IN, $connector);
        }

        return $this;
    }

    /**
     * between语句
     *
     * @param string $column
     * @param mixed  $min
     * @param mixed  $max
     * @param string $connector
     *
     * @return QueryBuilder
     */
    public function whereBetween(string $column, $min, $max, string $connector = self::LOGICAL_AND): QueryBuilder
    {
        $this->criteria($this->where, $column, [$min, $max], self::BETWEEN, $connector);

        return $this;
    }

    /**
     * not between语句
     *
     * @param string $column
     * @param mixed  $min
     * @param mixed  $max
     * @param string $connector
     *
     * @return QueryBuilder
     */
    public function whereNotBetween(string $column, $min, $max, string $connector = self::LOGICAL_AND): QueryBuilder
    {
        $this->criteria($this->where, $column, [$min, $max], self::NOT_BETWEEN, $connector);

        return $this;
    }

    /**
     * having语句
     *
     * @param string $column
     * @param mixed  $value
     * @param string $operator
     * @param string $connector
     *
     * @return QueryBuilder
     */
    public function having(string $column, $value, string $operator = self::OPERATOR_EQ, string $connector = self::LOGICAL_AND): QueryBuilder
    {
        $this->criteria($this->having, $column, $value, $operator, $connector);

        return $this;
    }

    /**
     * having and 语句
     *
     * @param string $column
     * @param mixed  $value
     * @param string $operator
     *
     * @return QueryBuilder
     */
    public function andHaving(string $column, $value, string $operator = self::OPERATOR_EQ): QueryBuilder
    {
        $this->criteria($this->having, $column, $value, $operator, self::LOGICAL_AND);

        return $this;
    }

    /**
     * having or 语句
     *
     * @param string $column
     * @param mixed  $value
     * @param string $operator
     *
     * @return QueryBuilder
     */
    public function orHaving(string $column, $value, string $operator = self::OPERATOR_EQ): QueryBuilder
    {
        $this->criteria($this->having, $column, $value, $operator, self::LOGICAL_OR);

        return $this;
    }

    /**
     * having in 语句
     *
     * @param string $column
     * @param array  $values
     * @param string $connector
     *
     * @return QueryBuilder
     */
    public function havingIn(string $column, array $values, string $connector = self::LOGICAL_AND): QueryBuilder
    {
        $this->criteria($this->having, $column, $values, self::IN, $connector);

        return $this;
    }

    /**
     * having not in 语句
     *
     * @param string $column
     * @param array  $values
     * @param string $connector
     *
     * @return QueryBuilder
     */
    public function havingNotIn(string $column, array $values, string $connector = self::LOGICAL_AND): QueryBuilder
    {
        $this->criteria($this->having, $column, $values, self::NOT_IN, $connector);

        return $this;
    }

    /**
     * having between语句
     *
     * @param string $column
     * @param mixed  $min
     * @param mixed  $max
     * @param string $connector
     *
     * @return QueryBuilder
     */
    public function havingBetween(string $column, $min, $max, string $connector = self::LOGICAL_AND): QueryBuilder
    {
        $this->criteria($this->having, $column, [$min, $max], self::BETWEEN, $connector);

        return $this;
    }

    /**
     * having not between语句
     *
     * @param string $column
     * @param mixed  $min
     * @param mixed  $max
     * @param string $connector
     *
     * @return QueryBuilder
     */
    public function havingNotBetween(string $column, $min, $max, string $connector = self::LOGICAL_AND): QueryBuilder
    {
        $this->criteria($this->having, $column, [$min, $max], self::NOT_BETWEEN, $connector);

        return $this;
    }

    /**
     * having，括号开始(左括号)
     *
     * @param string $connector
     *
     * @return QueryBuilder
     */
    public function openHaving($connector = self::LOGICAL_AND): QueryBuilder
    {
        return $this->bracketCriteria($this->having, self::BRACKET_OPEN, $connector);
    }

    /**
     * having，括号开始(右括号)
     *
     * @return QueryBuilder
     */
    public function closeHaving(): QueryBuilder
    {
        return $this->bracketCriteria($this->having, self::BRACKET_CLOSE);
    }

    /**
     * group by语句
     *
     * @param string $column
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function groupBy(string $column, string $order = null): QueryBuilder
    {
        $this->groupBy[] = [
            'column' => $column,
            'order'  => $order,
        ];

        return $this;
    }

    /**
     * order by语句
     *
     * @param string $column
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function orderBy(string $column, string $order = self::ORDER_BY_ASC): QueryBuilder
    {
        $this->orderBy[] = [
            'column' => $column,
            'order'  => $order,
        ];

        return $this;
    }

    /**
     * limit语句
     *
     * @param int $limit
     * @param int $offset
     *
     * @return QueryBuilder
     */
    public function limit(int $limit, $offset = 0): QueryBuilder
    {
        $this->limit['limit']  = $limit;
        $this->limit['offset'] = $offset;

        return $this;
    }

    /**
     * 设置参数
     *
     * @param mixed  $key   参数名称整数和字符串，(?n|:name)
     * @param mixed  $value 值
     * @param string $type  类型，默认按照$value传值的类型
     *
     * @return QueryBuilder
     * @throws \Swoft\Db\Exception\DbException
     */
    public function setParameter($key, $value, $type = null): QueryBuilder
    {
        list($key, $value) = EntityHelper::transferParameter($key, $value, $type);
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * 设置多个参数
     *
     * @param array $parameters
     *    $parameters = [
     *    'key1' => 'value1',
     *    'key2' => 'value2',
     *    ]
     *    $parameters = [
     *    'value1',
     *    'value12',
     *    ]
     *    $parameters = [
     *    ['key', 'value', 'type'],
     *    ['key', 'value'],
     *    ['key', 'value', 'type'],
     *    ]
     *
     *
     * @throws \Swoft\Db\Exception\DbException
     * @return $this
     */
    public function setParameters(array $parameters): self
    {
        // 循环设置每个参数
        foreach ($parameters as $index => $parameter) {
            $key = $type = $value = null;

            if (\count($parameter) >= 3) {
                list($key, $value, $type) = $parameter;
            } elseif (\count($parameter) == 2) {
                list($key, $value) = $parameter;
            } elseif (!\is_array($parameter)) {
                $key   = $index;
                $value = $parameter;
            }

            if ($key === null || $value === null) {
                App::warning('Sql parameter formatting error, parameters=' . \json_encode($parameters));
                continue;
            }
            $this->setParameter($key, $value, $type);
        }

        return $this;
    }

    /**
     * @param \Closure $closure
     *
     * @return $this
     */
    public function addDecorator(\Closure $closure): self
    {
        $this->decorators[] = $closure;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearDecorators(): self
    {
        $this->decorators = [];

        return $this;
    }

    /**
     * @param array $decorators
     *
     * @return $this
     */
    public function setDecorators(array $decorators): self
    {
        $this->decorators = $decorators;

        return $this;
    }

    /**
     * @return array
     */
    public function getDecorators(): array
    {
        return $this->decorators;
    }

    /**
     * 括号条件组拼
     *
     * @param array  $criteria
     * @param string $bracket
     * @param string $connector
     *
     * @return QueryBuilder
     */
    private function bracketCriteria(array &$criteria, string $bracket = self::BRACKET_OPEN, string $connector = self::LOGICAL_AND): QueryBuilder
    {
        $criteria[] = [
            'bracket'   => $bracket,
            'connector' => $connector,
        ];

        return $this;
    }

    /**
     * join数据组装
     *
     * @param string       $table
     * @param string|array $criteria
     * @param string       $type
     * @param string       $alias
     *
     * @return QueryBuilder
     */
    private function join(string $table, $criteria = null, string $type = self::INNER_JOIN, string $alias = null): QueryBuilder
    {
        // 是否存在判断...

        if (\is_string($criteria)) {
            $criteria = [$criteria];
        }

        $this->join[] = [
            'table'    => $table,
            'criteria' => $criteria,
            'type'     => $type,
            'alias'    => $alias,
        ];

        return $this;
    }

    /**
     * 条件组装
     *
     * @param array  $criteria
     * @param string $column
     * @param mixed  $value
     * @param string $operator
     * @param string $connector
     *
     * @return QueryBuilder
     */
    private function criteria(
        array &$criteria,
        string $column,
        $value,
        string $operator = self::OPERATOR_EQ,
        string $connector = self::LOGICAL_AND
    ): QueryBuilder {
        $criteria[] = [
            'column'    => $column,
            'value'     => $value,
            'operator'  => $operator,
            'connector' => $connector,
        ];

        return $this;
    }

    /**
     * @param string $db
     *
     * @return QueryBuilder
     */
    public function selectDb(string $db): self
    {
        $this->db = $db;

        return $this;
    }

    /**
     * @param string $node
     *
     * @return QueryBuilder
     */
    public function selectNode(string $node = Pool::MASTER): self
    {
        $this->node = $node;

        return $this;
    }

    /**
     * @param string $className
     *
     * @return QueryBuilder
     */
    public function className(string $className): self
    {
        $this->className = $className;

        return $this;
    }

    /**
     * @param string $instance
     *
     * @return QueryBuilder
     */
    public function selectInstance(string $instance): self
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * @param bool $master
     *
     * @return QueryBuilder
     */
    public function force(bool $master = true): self
    {
        if ($master) {
            $this->node = Pool::MASTER;
        }

        return $this;
    }

    /**
     * @param string $column
     * @param string $alias
     *
     * @return ResultInterface
     */
    public function count(string $column = '*', string $alias = 'count'): ResultInterface
    {
        $this->aggregate['count'] = [$column, $alias];
        $this->addAggregateDecorator($alias);

        return $this->execute();
    }


    /**
     * @param string $column
     * @param string $alias
     *
     * @return ResultInterface
     */
    public function max(string $column, string $alias = 'max'): ResultInterface
    {
        $this->aggregate['max'] = [$column, $alias];
        $this->addAggregateDecorator($alias);

        return $this->execute();
    }

    /**
     * @param string $column
     * @param string $alias
     *
     * @return ResultInterface
     */
    public function min(string $column, string $alias = 'min'): ResultInterface
    {
        $this->aggregate['min'] = [$column, $alias];
        $this->addAggregateDecorator($alias);

        return $this->execute();
    }

    /**
     * @param string $column
     * @param string $alias
     *
     * @return ResultInterface
     */
    public function avg(string $column, string $alias = 'avg'): ResultInterface
    {
        $this->aggregate['avg'] = [$column, $alias];
        $this->addAggregateDecorator($alias);

        return $this->execute();
    }

    /**
     * @param string $column
     * @param string $alias
     *
     * @return ResultInterface
     */
    public function sum(string $column, string $alias = 'sum'): ResultInterface
    {
        $this->aggregate['sum'] = [$column, $alias];
        $this->addAggregateDecorator($alias);

        return $this->execute();
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $statementClassName = DbHelper::getStatementClassNameByInstance($this->instance);
        /* @var StatementInterface $statement */
        $statement = new $statementClassName($this);
        $result = Db::query($statement->getStatement(), $this->parameters, $this->getInstanceName(), $this->className, $this->getDecorators());
        $this->clearDecorators();
        return $result;
    }

    /**
     * @return string
     */
    private function getInstanceName(): string
    {
        return sprintf('%s.%s.%s', $this->instance, $this->node, $this->db);
    }

    /**
     * 实体类名获取表名
     *
     * @param string $tableName
     *
     * @return string
     * @throws DbException
     */
    private function getTableNameByClassName($tableName): string
    {
        // 不是实体类名
        if (strpos($tableName, '\\') === false) {
            return $tableName;
        }

        $entities = EntityCollector::getCollector();
        if (!isset($entities[$tableName]['table']['name'])) {
            throw new DbException('Class is not an entity，className=' . $tableName);
        }

        return $entities[$tableName]['table']['name'];
    }

    /**
     * @return string
     * @throws MysqlException
     */
    private function getTableName(): string
    {
        if (empty($this->table)) {
            throw new MysqlException('Table name must be setting!');
        }

        return $this->table['table'];
    }

    /**
     * @return string
     */
    public function getInsert(): string
    {
        return $this->insert;
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return $this->update;
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->delete;
    }

    /**
     * @return array
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * @return array
     */
    public function getSet(): array
    {
        return $this->set;
    }

    /**
     * @return array
     * @throws MysqlException
     */
    public function getFrom(): array
    {
        if (empty($this->table)) {
            throw new MysqlException('Table name must be set!');
        }

        return $this->table;
    }

    /**
     * @return array
     */
    public function getJoin(): array
    {
        return $this->join;
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * @return array
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * @return array
     */
    public function getHaving(): array
    {
        return $this->having;
    }

    /**
     * @return array
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @return array
     */
    public function getLimit(): array
    {
        return $this->limit;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function getAggregate(): array
    {
        return $this->aggregate;
    }

    /**
     * @return array
     */
    public function getInsertValues(): array
    {
        return $this->insertValues;
    }

    /**
     * @return array
     */
    public function getUpdateValues(): array
    {
        return $this->updateValues;
    }

    /**
     * @return array
     */
    public function getCounterValues(): array
    {
        return $this->counterValues;
    }

    /**
     * @param string $alias
     */
    protected function addAggregateDecorator(string $alias)
    {
        $this->addDecorator(function ($result) use ($alias) {
            if (isset($result[0][$alias])) {
                return $result[0][$alias];
            }

            return 0;
        });
    }

    /**
     * Add one decorator
     */
    protected function addOneDecorator()
    {
        $this->addDecorator(function ($result) {
            if (isset($result[0]) && !empty($this->className)) {
                if ($result[0] instanceof $this->className) {
                    return $result[0];
                }
                if (is_array($result[0])) {
                    return EntityHelper::arrayToEntity($result[0], $this->className);
                }
                throw new DbException('The result is not instanceof ' . $this->className);
            }

            if (isset($result[0]) && empty($this->join)) {
                $tableName = $this->getTableName();

                return EntityHelper::formatRowByType($result[0], $tableName);
            }

            if (empty($result) && !empty($this->className)) {
                return null;
            }

            if (isset($result[0])) {
                return $result[0];
            }

            return $result;
        });
    }

    /**
     * @return array
     */
    protected function getAllFields(): array
    {
        $tableName    = $this->getTableName();
        $entities     = EntityCollector::getCollector();
        $entityClass  = $entities[$tableName];
        $entityFields = $entities[$entityClass]['field']??[];
        $entityFields = array_column($entityFields, 'column');

        return $entityFields;
    }

    /**
     * Add get decorator
     */
    protected function addGetDecorator()
    {
        $this->addDecorator(function ($result) {
            if (!empty($this->className) && !empty($result)) {
                $entities = EntityHelper::listToEntity($result, $this->className);

                return new Collection($entities);
            }

            if (!empty($result) && empty($this->join)) {
                $tableName = $this->getTableName();
                $result    = EntityHelper::formatListByType($result, $tableName);
            }

            return $result;
        });
    }
}
