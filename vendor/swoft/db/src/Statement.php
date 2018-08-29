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
 * Statement
 */
class Statement implements StatementInterface
{
    /**
     * @var QueryBuilder
     */
    protected $builder;

    /**
     * Statement constructor.
     *
     * @param \Swoft\Db\QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->builder = $queryBuilder;
    }

    /**
     * 组拼SQL
     *
     * @return string
     */
    public function getStatement(): string
    {
        $statement = '';
        if ($this->isSelect() || $this->isAggregate()) {
            $statement = $this->getSelectStatement();
        } elseif ($this->isInsert()) {
            $statement = $this->getInsertStatement();
        } elseif ($this->isUpdate()) {
            $statement = $this->getUpdateStatement();
        } elseif ($this->isDelete()) {
            $statement = $this->getDeleteStatement();
        }

        return $statement;
    }

    /**
     * select语句
     *
     * @return string
     */
    protected function getSelectStatement(): string
    {
        $statement = '';
        if (!$this->isSelect() && !$this->isAggregate()) {
            return $statement;
        }

        // select语句
        $statement .= $this->getSelectString();

        // from语句
        if ($this->builder->getFrom()) {
            $statement .= ' ' . $this->getFromString();
        }

        // where语句
        if ($this->builder->getWhere()) {
            $statement .= ' ' . $this->getWhereString();
        }

        // groupBy语句
        if ($this->builder->getGroupBy()) {
            $statement .= ' ' . $this->getGroupByString();
        }

        // having语句
        if ($this->builder->getHaving()) {
            $statement .= ' ' . $this->getHavingString();
        }

        // orderBy语句
        if ($this->builder->getOrderBy()) {
            $statement .= ' ' . $this->getOrderByString();
        }

        // limit语句
        if ($this->builder->getLimit()) {
            $statement .= ' ' . $this->getLimitString();
        }
        
        return $statement;
    }

    /**
     * insert语句
     *
     * @return string
     */
    protected function getInsertStatement(): string
    {
        $statement = '';
        if (!$this->isInsert()) {
            return $statement;
        }

        // insert语句
        $statement .= $this->getInsertString();

        // values
        if ($this->builder->getInsertValues()) {
            $statement .= ' ' . $this->getInsertValuesString();
        }

        return $statement;
    }

    /**
     * update语句
     *
     * @return string
     */
    protected function getUpdateStatement(): string
    {
        $statement = '';
        if (!$this->isUpdate()) {
            return $statement;
        }

        // update语句
        $statement .= $this->getUpdateString();

        // set语句
        if ($this->builder->getUpdateValues() || $this->builder->getCounterValues()) {
            $statement .= ' ' . $this->getUpdateValuesString();
        }

        // where语句
        if ($this->builder->getWhere()) {
            $statement .= ' ' . $this->getWhereString();
        }

        // orderBy语句
        if ($this->builder->getOrderBy()) {
            $statement .= ' ' . $this->getOrderByString();
        }

        // limit语句
        if ($this->builder->getLimit()) {
            $statement .= ' ' . $this->getLimitString();
        }

        return $statement;
    }

    /**
     * delete语句
     *
     * @return string
     */
    protected function getDeleteStatement(): string
    {
        $statement = '';
        if (!$this->isDelete()) {
            return $statement;
        }

        // delete语句
        $statement .= $this->getDeleteString();

        // from语句
        if ($this->builder->getFrom()) {
            $statement .= ' ' . $this->getFromString();
        }

        // where语句
        if ($this->builder->getWhere()) {
            $statement .= ' ' . $this->getWhereString();
        }

        // orderBy语句
        if ($this->builder->getOrderBy()) {
            $statement .= ' ' . $this->getOrderByString();
        }

        // limit语句
        if ($this->builder->getLimit()) {
            $statement .= ' ' . $this->getLimitString();
        }

        return $statement;
    }

    /**
     * select语句
     *
     * @return string
     */
    protected function getSelectString(): string
    {
        $statement = '';
        $fieldSelect    = $this->builder->getSelect();
        $aggregate = $this->builder->getAggregate();
        if (empty($fieldSelect) && empty($aggregate)) {
            return $statement;
        }

        $select = $this->getAggregateStatement($fieldSelect, $aggregate);

        // 字段组拼
        foreach ($select as $column => $alias) {
            // Filter db keyword
            if (array_key_exists($column, $fieldSelect) && trim($column) != '*' && strpos($column, '.') === false) {
                $column = sprintf('`%s`', $column);
            }

            $statement .= $column;
            if ($alias !== null) {
                $statement .= sprintf(' AS `%s`', $alias);
            }
            $statement .= ', ';
        }

        //select组拼
        $statement = substr($statement, 0, -2);
        if (!empty($statement)) {
            $statement = 'SELECT ' . $statement;
        }

        return $statement;
    }

    /**
     * @param array $select
     * @param array $aggregate
     *
     * @return array
     */
    protected function getAggregateStatement(array $select, array $aggregate): array
    {
        foreach ($aggregate as $func => $value) {
            list($column, $alias) = $value;
            switch ($func) {
                case 'count':
                    $column = sprintf('count(%s)', $column);
                    break;
                case 'max':
                    $column = sprintf('max(%s)', $column);
                    break;
                case 'min':
                    $column = sprintf('min(%s)', $column);
                    break;
                case 'avg':
                    $column = sprintf('avg(%s)', $column);
                    break;
                case 'sum':
                    $column = sprintf('sum(%s)', $column);
                    break;
            }
            $select[$column] = $alias;
        }

        return $select;
    }

    /**
     * from语句
     *
     * @return string
     */
    public function getFromString(): string
    {
        $statement = '';
        if (empty($this->builder->getFrom())) {
            return $statement;
        }

        // from语句
        $statement .= $this->getFrom();
        $fromAlias = $this->getFromAlias();

        if (!empty($fromAlias)) {
            $statement .= ' AS ' . $fromAlias;
        }
        // join语句
        $statement .= ' ' . $this->getJoinString();
        $statement = rtrim($statement);

        if (!empty($statement)) {
            $statement = 'FROM ' . $statement;
        }

        return $statement;
    }

    /**
     * join语句
     *
     * @return string
     */
    protected function getJoinString(): string
    {
        $statement = '';
        $join      = $this->builder->getJoin();
        foreach ($join as $i => $joinItem) {

            // join信息
            $type     = $joinItem['type'];
            $table    = $joinItem['table'];
            $alias    = $joinItem['alias'];
            $criteria = $joinItem['criteria'];

            // join类型
            $statement .= ' ' . $type . ' ' . $table;
            if ($alias !== null) {
                $statement .= ' AS ' . $alias;
            }

            // join条件
            if ($criteria !== null) {
                if ($alias !== null) {
                    $table = $alias;
                }
                $statement = $this->getJoinCriteria($i, $table, $statement, $criteria);
            }
        }

        $statement = trim($statement);

        return $statement;
    }

    /**
     * join条件
     *
     * @param int    $joinIndex
     * @param string $table
     * @param string $statement
     * @param  array $criteria
     *
     * @return string
     */
    protected function getJoinCriteria(int $joinIndex, string $table, string $statement, array $criteria): string
    {
        $statement .= ' ON ';
        foreach ($criteria as $x => $criterion) {
            // 多个条件连接使用and逻辑符号
            if ($x !== 0) {
                $statement .= ' ' . QueryBuilder::LOGICAL_AND . ' ';
            }

            // 条件里面不包含'='符号,默认关联上一个join表
            if (strpos($criterion, '=') === false) {
                $statement .= $this->getJoinCriteriaUsingPreviousTable($joinIndex, $table, $criterion);
                continue;
            }
            $statement .= $criterion;
        }

        return $statement;
    }

    /**
     * 前一个join条件
     *
     * @param int    $joinIndex
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    protected function getJoinCriteriaUsingPreviousTable(int $joinIndex, string $table, string $column): string
    {
        $joinCriteria      = '';
        $previousJoinIndex = $joinIndex - 1;

        $join = $this->builder->getJoin();
        if (array_key_exists($previousJoinIndex, $join)) {
            // 上一个join存在
            $previousTable = $join[$previousJoinIndex]['table'];
            if ($join[$previousJoinIndex]['alias'] !== null) {
                $previousTable = $join[$previousJoinIndex]['alias'];
            }
        } elseif ($this->isSelect()) {
            // 查询
            $previousTable = $this->getFrom();
            $alias         = $this->getFromAlias();
            if (!empty($alias)) {
                $previousTable = $alias;
            }
        } elseif ($this->isUpdate()) {
            // 更新
            $previousTable = $this->getUpdate();
        } else {
            $previousTable = false;
        }

        // 上一个inner关联存在
        if ($previousTable && strpos($column, '.') === false) {
            $joinCriteria .= $previousTable . '.';
        }

        $joinCriteria .= $column . ' ' . QueryBuilder::OPERATOR_EQ . ' ' . $table . '.' . $column;
        return $joinCriteria;
    }

    /**
     * where语句
     *
     * @return string
     */
    protected function getWhereString(): string
    {
        $where     = $this->builder->getWhere();
        $statement = $this->getCriteriaString($where);

        if (!empty($statement)) {
            $statement = 'WHERE' . ($statement[0] === ' ' ? $statement : ' ' . $statement);
        }

        return $statement;
    }

    /**
     * where条件
     *
     * @param array $criteria
     *
     * @return string
     */
    protected function getCriteriaString(array &$criteria): string
    {
        $statement    = '';
        $useConnector = false;

        foreach ($criteria as $i => $criterion) {
            // 是括号符
            if (array_key_exists('bracket', $criterion)) {
                if (strcmp($criterion['bracket'], QueryBuilder::BRACKET_OPEN) === 0) {
                    if ($useConnector) {
                        $statement .= ' ' . $criterion['connector'] . ' ';
                    }
                    $useConnector = false;
                } else {
                    $useConnector = true;
                }

                $statement .= $criterion['bracket'];
                continue;
            }

            if ($useConnector) {
                $statement .= ' ' . $criterion['connector'] . ' ';
            }

            // 没有括号
            $useConnector = true;
            $value        = $this->getCriteriaWithoutBracket($criterion['operator'], $criterion['value'], $criterion['column']);
            $column       = $criterion['column'];
            $column       = strpos($column, '.') === false ? " `{$column}` " : " $column ";
            $statement    .= $column . $criterion['operator'] . ' ' . $value;
        }

        return $statement;
    }

    /**
     * 没有括号条件处理
     *
     * @param string $operator
     * @param  mixed $criterionVaue
     *
     * @return bool|string
     */
    protected function getCriteriaWithoutBracket(string $operator, $criterionVaue, $columnName)
    {
        switch ($operator) {
            case QueryBuilder::BETWEEN:
            case QueryBuilder::NOT_BETWEEN:
                $end   = $this->getQuoteValue($criterionVaue[1]);
                $start = $this->getQuoteValue($criterionVaue[0]);
                $value = $start . ' ' . QueryBuilder::LOGICAL_AND . ' ' . $end;
                break;

            case QueryBuilder::IN:
            case QueryBuilder::NOT_IN:
                $value = QueryBuilder::BRACKET_OPEN;
                // 数组处理
                foreach ($criterionVaue ?? [] as $criterionValue) {
                    $criterionValue = $this->getQuoteValue($criterionValue);
                    $value          .= $criterionValue . ', ';
                }
                $value = substr($value, 0, -2);
                $value .= QueryBuilder::BRACKET_CLOSE;
                break;
            case QueryBuilder::IS:
            case QueryBuilder::IS_NOT:
                $value = $criterionVaue;
                $value = $this->getQuoteValue($value);
                break;
            default:
                $value = $criterionVaue;
                $value = $this->getQuoteValue($value);
                break;
        }

        return $value;
    }

    /**
     * group语句
     *
     * @return string
     */
    protected function getGroupByString(): string
    {
        $statement = '';
        $groupBys  = $this->builder->getGroupBy();
        foreach ($groupBys as $groupBy) {
            $statement .= $groupBy['column'];
            if ($groupBy['order']) {
                $statement .= ' ' . $groupBy['order'];
            }
            $statement .= ', ';
        }

        $statement = substr($statement, 0, -2);
        if (!empty($statement)) {
            $statement = 'GROUP BY ' . $statement;
        }

        return $statement;
    }

    /**
     * having语句
     *
     * @return string
     */
    protected function getHavingString(): string
    {
        $having    = $this->builder->getHaving();
        $statement = $this->getCriteriaString($having);
        if (!empty($statement)) {
            $statement = 'HAVING ' . $statement;
        }

        return $statement;
    }

    /**
     * orderBy语句
     *
     * @return string
     */
    protected function getOrderByString(): string
    {
        $statement = '';
        $orderBys  = $this->builder->getOrderBy();
        foreach ($orderBys as $orderBy) {
            $statement .= $orderBy['column'] . ' ' . $orderBy['order'] . ', ';
        }

        $statement = substr($statement, 0, -2);
        if (!empty($statement)) {
            $statement = 'ORDER BY ' . $statement;
        }

        return $statement;
    }

    /**
     * limit语句
     *
     * @return string
     */
    protected function getLimitString(): string
    {
        $statement = '';
        $limit     = $this->builder->getLimit();
        if (!$limit) {
            return $statement;
        }

        $isUpdateOrDelete = $this->isDelete() || $this->isUpdate();
        if ($isUpdateOrDelete && $limit['limit']) {
            return sprintf('LIMIT %d', $limit['limit']);
        }

        $size      = $limit['limit'];
        $offset    = $limit['offset'];
        $statement = sprintf('LIMIT %d,%d', $offset, $size);

        return $statement;
    }

    /**
     * @return string
     */
    protected function getInsertValuesString(): string
    {
        $statement    = ' ';
        $values       = $this->builder->getInsertValues();
        $columns      = $values['columns'];
        $columnValues = $values['values'];

        $columns   = array_map(function ($v) {
            return "`{$v}`";
        }, $columns);
        $statement .= sprintf('(%s)', implode(',', $columns));
        $statement .= ' values ';
        foreach ($columnValues as $row) {
            foreach ($row as &$rowValue) {
                $rowValue = $this->getQuoteValue($rowValue);
            }
            $statement .= sprintf('(%s)', implode(',', $row)) . ', ';
        }

        $statement = substr($statement, 0, -2);

        return $statement;
    }

    /**
     * @return string
     */
    protected function getUpdateValuesString(): string
    {
        $statement = '';
        $values    = $this->builder->getUpdateValues();
        foreach ($values as $column => $value) {
            $statement .= '`' . $column . '` ' . QueryBuilder::OPERATOR_EQ . ' ' . $this->getQuoteValue($value) . ', ';
        }
        $statement = substr($statement, 0, -2);
        if (!empty($statement)) {
            $statement = 'SET ' . $statement;
        }

        if (empty($statement)) {
            $statement .= ' set ';
        }
        $statement .= $this->getCounterValueString();

        return $statement;
    }

    /**
     * @return string
     */
    protected function getCounterValueString(): string
    {
        $statement = '';
        $values    = $this->builder->getCounterValues();
        foreach ($values as $coloumn => $value) {
            $statement .= !empty($statement) ? ',' : $statement;
            if ($value > 0) {
                $statement .= sprintf(' `%s` = `%s`+%d ', $coloumn, $coloumn, $value);
            } else {
                $statement .= sprintf(' `%s` = `%s`-%d ', $coloumn, $coloumn, abs($value));
            }
        }

        return $statement;
    }

    /**
     * insert语句
     *
     * @return string
     */
    protected function getInsertString(): string
    {
        $statement = '';
        if (!$this->builder->getInsert()) {
            return $statement;
        }

        $statement .= $this->getInsert();
        if (!empty($statement)) {
            $statement = 'INSERT INTO ' . $statement;
        }

        return $statement;
    }

    /**
     * update语句
     *
     * @return string
     */
    protected function getUpdateString(): string
    {
        $statement = '';
        if (!$this->builder->getUpdate()) {
            return $statement;
        }

        $statement .= $this->getUpdate();

        // join条件
        $statement .= ' ' . $this->getJoinString();
        $statement = rtrim($statement);
        if (!empty($statement)) {
            $statement = 'UPDATE ' . $statement;
        }

        return $statement;
    }

    /**
     * delete语句
     *
     * @return string
     */
    protected function getDeleteString(): string
    {
        $statement = '';

        $delete = $this->builder->isDelete();
        if (!$delete && !$this->isDeleteTableFrom()) {
            return $statement;
        }

        if (\is_array($delete)) {
            $statement .= implode(', ', $delete);
        }

        if ($statement || $this->isDeleteTableFrom()) {
            $statement = 'DELETE ' . $statement;
            $statement = trim($statement);
        }

        return $statement;
    }

    /**
     * @param mixed $key
     *
     * @return bool
     */
    protected function hasParameter($key): bool
    {
        return array_key_exists($key, $this->builder->getParameters());
    }

    /**
     * insert表
     *
     * @return string
     */
    protected function getInsert(): string
    {
        return $this->builder->getInsert();
    }

    /**
     * update表
     *
     * @return mixed
     */
    protected function getUpdate()
    {
        return $this->builder->getUpdate();
    }

    /**
     * 是否是select
     *
     * @return bool
     */
    protected function isSelect(): bool
    {
        return !empty($this->builder->getSelect());
    }

    /**
     * @return bool
     */
    protected function isAggregate(): bool
    {
        return !empty($this->builder->getAggregate());
    }

    /**
     * 是否是insert
     *
     * @return bool
     */
    protected function isInsert(): bool
    {
        return !empty($this->builder->getInsert());
    }

    /**
     * 是否是删除
     *
     * @return bool
     */
    protected function isDelete(): bool
    {
        return !empty($this->builder->isDelete());
    }

    /**
     * 是否是删除from
     *
     * @return bool
     */
    protected function isDeleteTableFrom(): bool
    {
        $delete = $this->builder->isDelete();

        return $delete === true;
    }

    /**
     * 是否是update
     *
     * @return bool
     */
    protected function isUpdate(): bool
    {
        return !empty($this->builder->getUpdate());
    }

    /**
     * from表
     *
     * @return string
     */
    protected function getFrom(): string
    {
        $from = $this->builder->getFrom();
        return $from['table'] ? '`' . $from['table'] . '`' : '';
    }

    /**
     * 别名
     *
     * @return string
     */
    protected function getFromAlias(): string
    {
        $from = $this->builder->getFrom();

        return $from['alias'] ? '`' . $from['alias'] . '`' : '';
    }

    /**
     * 字符串转换
     *
     * @param $value
     *
     * @return string
     */
    protected function getQuoteValue($value): string
    {
        $key = uniqid();
        $this->builder->setParameter($key, $value);

        return ":{$key}";
    }
}
