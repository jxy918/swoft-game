<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db\Entity;

class Generator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * @var string $db 数据库
     */
    private $db = null;

    /**
     * @var string $instance 需要指定的数据实例别名
     */
    private $instance = null;

    /**
     * @var array $tablesEnabled 操作的表
     */
    private $tablesEnabled = [];

    /**
     * @var array $tablesDisabled 不操作的表
     */
    private $tablesDisabled = [];

    /**
     * @var array $tables 实体表
     */
    private $tables = [];

    /**
     * @var string $removeTablePrefix 需要移除的表前缀
     */
    private $removeTablePrefix = '';

    /**
     * @const string SchemaTables表
     */
    const SCHEMA_TABLES = 'information_schema.`tables`';

    /**
     * @const string SchemaColumn表
     */
    const SCHEMA_COLUMN = 'information_schema.`columns`';

    /**
     * 开始执行生成实体
     *
     * @param Schema $schema schema对象
     *
     * @return void
     */
    public function execute(Schema $schema)
    {
        $tables = $this->getSchemaTables();
        $instance = $this->getInstance();
        foreach ($tables as $table) {
            $columns = $this->getTableColumns($table['name']);
            $this->parseProperty($table['name'], $table['comment'], $columns, $schema, $instance);
        }
    }

    /**
     * 获取当前db的所有表
     *
     * @return array
     */
    public function getSchemaTables(): array
    {
        if (empty($this->db)) {
            return [];
        }
        $schemaTable = self::SCHEMA_TABLES;
        $where[]     = "TABLE_TYPE = 'BASE TABLE'";
        $where[]     = "TABLE_SCHEMA = '{$this->db}'";
        if (!empty($this->tablesEnabled)) {
            $tablesEnabled = array_map(function ($item) {
                return "'{$item}'";
            }, $this->tablesEnabled);
            $where[]       = 'TABLE_NAME IN (' . implode(',', $tablesEnabled) . ')';
        }
        if (!empty($this->tablesDisabled)) {
            $tablesDisabled = array_map(function ($item) {
                return "'{$item}'";
            }, $this->tablesDisabled);
            $where[]        = 'TABLE_NAME NOT IN (' . implode(',', $tablesDisabled) . ')';
        }
        $where = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : null;

        $querySql = "SELECT `TABLE_NAME` AS `name`,`TABLE_COMMENT` as `comment` FROM {$schemaTable} {$where}";
        $this->dbHandler->prepare($querySql);
        $this->dbHandler->execute([]);
        $this->tables = $this->dbHandler->fetch();

        return !empty($this->tables) ? $this->tables : [];
    }

    /**
     * 获取表列名
     *
     * @param string $table 表名
     *
     * @return array
     */
    public function getTableColumns(string $table): array
    {
        $schemaTable = self::SCHEMA_COLUMN;

        $where[] = "TABLE_NAME = '{$table}'";
        $where[] = "TABLE_SCHEMA = '{$this->db}'";
        $where   = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : null;

        $querySql = "SELECT `COLUMN_NAME` as `name`,`DATA_TYPE` as `type`,`CHARACTER_MAXIMUM_LENGTH` as `length`,`COLUMN_DEFAULT` as `default` ,`COLUMN_KEY` as `key`,`IS_NULLABLE` as `nullable`,`COLUMN_TYPE` as `column_type`,`COLUMN_COMMENT` as `column_comment` FROM {$schemaTable} {$where}";
        $this->dbHandler->prepare($querySql);
        $this->dbHandler->execute([]);
        $columns = $this->dbHandler->fetch();

        return !empty($columns) ? $columns : [];
    }

    /**
     * 设置数据库
     *
     * @param string $value 数据库
     *
     * @return $this
     */
    public function setDb(string $value): self
    {
        $this->db = $value;

        return $this;
    }

    /**
     * 获取数据库
     *
     * @return string|null
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * 设置数据库实例别名
     * @param string $value 数据库实例别名
     * @return $this
     */
    public function setInstance($value): self
    {
        $this->instance = $value;

        return $this;
    }

    /**
     * 获取数据库实例别名
     * @return string|null
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * 设置扫描的表
     *
     * @param array $value 需要扫描的表
     *
     * @return $this;
     */
    public function setTablesEnabled(array $value): self
    {
        $this->tablesEnabled = $value;

        return $this;
    }

    /**
     * 返回需要扫描的表
     *
     * @return array
     */
    public function getTablesEnabled(): array
    {
        return $this->tablesEnabled;
    }

    /**
     * 设置不需要扫描的表
     *
     * @param array $value 不需要扫描的表
     *
     * @return $this;
     */
    public function setTablesDisabled(array $value): self
    {
        $this->tablesDisabled = $value;

        return $this;
    }

    /**
     * 返回不需要扫描的表
     *
     * @retrun array
     */
    public function getTablesDisabled(): array
    {
        return $this->tablesDisabled;
    }

    /**
     * 设置移除的表前缀
     *
     * @param string $value 移除的表前缀
     *
     * @return $this;
     */
    public function setRemoveTablePrefix(string $value): self
    {
        $this->removeTablePrefix = $value;

        return $this;
    }

    /**
     * 返回移除的表前缀
     *
     * @retrun string
     */
    public function getRemoveTablePrefix(): string
    {
        return $this->removeTablePrefix;
    }

    /**
     * 设置实体基类
     *
     * @param string $extends 实体基类
     *
     * @return $this
     */
    public function setExtends(string $extends): self
    {
        $this->extends = $extends;
        return $this;
    }

    /**
     * 返回实体基类
     *
     * @retrun string
     */
    public function getExtends(): string
    {
        return $this->extends;
    }

    /**
     * __get()
     *
     * @override
     *
     * @param string $name 参数名
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        if (method_exists($this, 'set' . ucfirst($name))) {
            throw new \RunTimeException('the property only access write' . \get_class($this) . '::' . $name);
        }

        throw new \RunTimeException('unknown the property' . \get_class($this) . '::' . $name);
    }

    /**
     * __set()
     *
     * @override
     *
     * @param string $name  参数名
     * @param mixed  $value 参数值
     *
     * @return self
     * @throws \RuntimeException
     */
    public function __set($name, $value): self
    {
        // TODO add pair method __isset()
        $method = 'set' . ucfirst($name);
        if (\method_exists($this, $method)) {
            return $this->$method($value);
        }

        if (\method_exists($this, 'get' . ucfirst($name))) {
            throw new \RunTimeException('the property only access read' . \get_class($this) . '::' . $name);
        }

        throw new \RunTimeException('unknown the property' . \get_class($this) . '::' . $name);
    }
}
