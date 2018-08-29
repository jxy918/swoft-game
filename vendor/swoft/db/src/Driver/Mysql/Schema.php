<?php

namespace Swoft\Db\Driver\Mysql;

use Swoft\Db\Types;

/**
 * Schema
 */
class Schema extends \Swoft\Db\Driver\Schema
{
    /**
     * @var array
     */
    public static $typeMap = [
            'tinyint'    => self::TYPE_TINYINT,
            'bit'        => self::TYPE_INTEGER,
            'smallint'   => self::TYPE_SMALLINT,
            'mediumint'  => self::TYPE_INTEGER,
            'int'        => self::TYPE_INTEGER,
            'integer'    => self::TYPE_INTEGER,
            'bigint'     => self::TYPE_BIGINT,
            'float'      => self::TYPE_FLOAT,
            'double'     => self::TYPE_DOUBLE,
            'real'       => self::TYPE_FLOAT,
            'decimal'    => self::TYPE_DECIMAL,
            'numeric'    => self::TYPE_DECIMAL,
            'tinytext'   => self::TYPE_TEXT,
            'mediumtext' => self::TYPE_TEXT,
            'longtext'   => self::TYPE_TEXT,
            'longblob'   => self::TYPE_BINARY,
            'blob'       => self::TYPE_BINARY,
            'text'       => self::TYPE_TEXT,
            'varchar'    => self::TYPE_STRING,
            'string'     => self::TYPE_STRING,
            'char'       => self::TYPE_CHAR,
            'datetime'   => self::TYPE_DATETIME,
            'year'       => self::TYPE_DATE,
            'date'       => self::TYPE_DATE,
            'time'       => self::TYPE_TIME,
            'timestamp'  => self::TYPE_TIMESTAMP,
            'enum'       => self::TYPE_STRING,
            'varbinary'  => self::TYPE_BINARY,
            'json'       => self::TYPE_JSON,
        ];

    /**
     * @var array
     */
    public static $phpMap
        = [
            'tinyint'    => Types::INT,
            'bit'        => Types::INT,
            'smallint'   => Types::INT,
            'mediumint'  => Types::INT,
            'int'        => Types::INT,
            'integer'    => Types::INT,
            'bigint'     => Types::INT,
            'float'      => Types::FLOAT,
            'double'     => Types::FLOAT,
            'real'       => Types::FLOAT,
            'decimal'    => Types::FLOAT,
            'numeric'    => Types::FLOAT,
            'tinytext'   => Types::STRING,
            'mediumtext' => Types::STRING,
            'longtext'   => Types::STRING,
            'longblob'   => Types::STRING,
            'blob'       => Types::STRING,
            'text'       => Types::STRING,
            'varchar'    => Types::STRING,
            'string'     => Types::STRING,
            'char'       => Types::STRING,
            'datetime'   => Types::STRING,
            'year'       => Types::STRING,
            'date'       => Types::STRING,
            'time'       => Types::STRING,
            'timestamp'  => Types::STRING,
            'enum'       => Types::STRING,
            'varbinary'  => Types::STRING,
            'json'       => Types::STRING,
        ];
}