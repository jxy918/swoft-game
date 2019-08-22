<?php declare(strict_types=1);


namespace Swoft\Db\Schema\Grammars;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Grammar as BaseGrammar;
use Swoft\Db\Query\Expression;
use Swoft\Db\Schema\Blueprint;
use Swoft\Stdlib\Fluent;

/**
 * Class Grammar
 *
 * @since 2.0
 */
abstract class Grammar extends BaseGrammar
{
    /**
     * php type
     *
     * @var string
     */
    public const INT     = 'int';      // php integer type
    public const STRING  = 'string';   // php string type
    public const FLOAT   = 'float';    // php float type
    public const BOOLEAN = 'boolean';  // php boolean type
    public const BOOL    = 'bool';     // php boolean type
    public const ARRAY   = 'array';    // php array type


    /**
     * @var array
     */
    public $phpMap = [];

    /**
     * If this Grammar supports schema changes wrapped in a transaction.
     *
     * @var bool
     */
    protected $transactions = false;

    /**
     * The commands to be executed outside of create or alter command.
     *
     * @var array
     */
    protected $fluentCommands = [];

    /**
     * The possible column modifiers.
     *
     * @var array
     */
    protected $modifiers = [];

    /**
     * Get the commands for the grammar.
     *
     * @return mixed
     */
    public function getFluentCommands()
    {
        return $this->fluentCommands;
    }

    /**
     * Compile a foreign key command.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $command
     *
     * @return string
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function compileForeign(Blueprint $blueprint, Fluent $command)
    {
        // We need to prepare several of the elements of the foreign key definition
        // before we can create the SQL, such as wrapping the tables and convert
        // an array of columns to comma-delimited strings for the SQL queries.
        $sql = sprintf('alter table %s add constraint %s ',
            $this->wrapTable($blueprint),
            $this->wrap($command['index'])
        );

        // Once we have the initial portion of the SQL statement we will add on the
        // key name, table name, and referenced columns. These will complete the
        // main portion of the SQL statement and this SQL will almost be done.
        $sql .= sprintf('foreign key (%s) references %s (%s)',
            $this->columnize($command['columns']),
            $this->wrapTable($command['on']),
            $this->columnize((array)$command['references'])
        );

        // Once we have the basic foreign key creation statement constructed we can
        // build out the syntax for what should happen on an update or delete of
        // the affected columns, which will get something like "cascade", etc.
        if (!is_null($command['onDelete'])) {
            $sql .= " on delete {$command['onDelete']}";
        }

        if (!is_null($command['onUpdate'])) {
            $sql .= " on update {$command['onUpdate']}";
        }

        return $sql;
    }

    /**
     * Compile the blueprint's column definitions.
     *
     * @param Blueprint $blueprint
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     */
    protected function getColumns(Blueprint $blueprint)
    {
        $columns = [];

        foreach ($blueprint->getAddedColumns() as $column) {
            // Each of the column types have their own compiler functions which are tasked
            // with turning the column definition into its SQL format for this platform
            // used by the connection. The column's modifiers are compiled and added.
            $sql = $this->wrap($column) . ' ' . $this->getType($column);

            $columns[] = $this->addModifiers($sql, $blueprint, $column);
        }

        return $columns;
    }

    /**
     * Get the SQL for the column data type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function getType(Fluent $column)
    {
        return $this->{'type' . ucfirst($column['type'])}($column);
    }


    /**
     * Add the column modifiers to the definition.
     *
     * @param string    $sql
     * @param Blueprint $blueprint
     * @param Fluent    $column
     *
     * @return string
     */
    protected function addModifiers($sql, Blueprint $blueprint, Fluent $column)
    {
        foreach ($this->modifiers as $modifier) {
            if (method_exists($this, $method = "modify{$modifier}")) {
                $sql .= $this->{$method}($blueprint, $column);
            }
        }

        return $sql;
    }


    /**
     * Get the primary key command if it exists on the blueprint.
     *
     * @param Blueprint $blueprint
     * @param string    $name
     *
     * @return Fluent|null
     */
    protected function getCommandByName(Blueprint $blueprint, $name)
    {
        $commands = $this->getCommandsByName($blueprint, $name);

        if (count($commands) > 0) {
            return reset($commands);
        }
        return null;
    }

    /**
     * Get all of the commands with a given name.
     *
     * @param Blueprint $blueprint
     * @param string    $name
     *
     * @return array
     */
    protected function getCommandsByName(Blueprint $blueprint, $name)
    {
        return array_filter($blueprint->getCommands(), function ($value) use ($name) {
            return $value->name == $name;
        });
    }

    /**
     * Add a prefix to an array of values.
     *
     * @param string $prefix
     * @param array  $values
     *
     * @return array
     */
    public function prefixArray($prefix, array $values)
    {
        return array_map(function ($value) use ($prefix) {
            return $prefix . ' ' . $value;
        }, $values);
    }

    /**
     * Wrap a table in keyword identifiers.
     *
     * @param mixed $table
     *
     * @return string
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function wrapTable($table)
    {
        return parent::wrapTable(
            $table instanceof Blueprint ? $table->getTable() : $table
        );
    }

    /**
     * Wrap a value in keyword identifiers.
     *
     * @param Expression|string $value
     * @param bool              $prefixAlias
     *
     * @return string
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function wrap($value, $prefixAlias = false)
    {
        return parent::wrap(
            $value instanceof Fluent ? $value['name'] : $value, $prefixAlias
        );
    }

    /**
     * Format a value so that it can be used in "default" clauses.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function getDefaultValue($value)
    {
        if ($value instanceof Expression) {
            return $value;
        }

        return is_bool($value)
            ? "'" . (int)$value . "'"
            : "'" . (string)$value . "'";
    }

    /**
     * Convert database type To PHP Type
     *
     * @param string|array $databaseType
     *
     * @return string
     */
    public function convertType($databaseType): string
    {
        return $this->phpMap[$databaseType] ?? self::STRING;
    }

    /**
     * Check if this Grammar supports schema changes wrapped in a transaction.
     *
     * @return bool
     */
    public function supportsSchemaTransactions()
    {
        return $this->transactions;
    }

    /**
     * Compile the command to enable foreign key constraints.
     *
     * @return string
     */
    public function compileEnableForeignKeyConstraints(): string
    {
        return '';
    }

    /**
     * Compile the command to disable foreign key constraints.
     *
     * @return string
     */
    public function compileDisableForeignKeyConstraints(): string
    {
        return '';
    }

    /**
     * Compile the SQL needed to retrieve all table names.
     *
     * @return string
     */
    public function compileGetAllTables(): string
    {
        return '';
    }

    /**
     * Compile the SQL needed to retrieve all view names.
     *
     * @return string
     */
    public function compileGetAllViews(): string
    {
        return '';
    }

    /**
     * Compile the query to determine if a table exists.
     *
     * @return string
     */
    public function compileTableExists(): string
    {
        return '';
    }

    /**
     * Compile the query to determine the list of columns.
     *
     * @return string
     */
    public function compileColumnListing(): string
    {
        return '';
    }

    /**
     * Compile the SQL needed to drop all tables.
     *
     * @param array $tables
     *
     * @return string
     */
    public function compileDropAllTables(array $tables): string
    {
        return '';
    }

    /**
     * Compile the SQL needed to drop all views.
     *
     * @param array $views
     *
     * @return string
     */
    public function compileDropAllViews(array $views): string
    {
        return '';
    }
}
