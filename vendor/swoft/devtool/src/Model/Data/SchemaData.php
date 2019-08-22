<?php declare(strict_types=1);

namespace Swoft\Devtool\Model\Data;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Exception\DbException;
use Swoft\Devtool\Model\Dao\SchemaDao;
use Swoft\Stdlib\Helper\StringHelper;
use function mb_substr;
use function mt_rand;
use function preg_match;
use function preg_replace;
use function ucfirst;

/**
 * Class SchemaData
 *
 * @Bean()
 * @since 2.0
 */
class SchemaData
{
    /**
     * @Inject()
     *
     * @var SchemaDao
     */
    protected $schemaDao;

    /**
     * Get schema columns
     *
     * @param string $pool
     * @param string $table
     * @param string $fieldPrefix
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     * @throws DbException
     */
    public function getSchemaColumnsData(string $pool, string $table, string $fieldPrefix = ''): array
    {
        $columnSchemas = $this->schemaDao->getColumnsSchema($pool, $table);
        foreach ($columnSchemas as &$columnSchema) {
            if (empty($fieldPrefix)) {
                $mappingName = $columnSchema['name'];
            } else {
                $mappingName = StringHelper::replaceFirst($fieldPrefix, '', $columnSchema['name']);
            }

            $columnSchema['mappingName'] = $this->getSafeMappingName($mappingName);
        }
        unset($columnSchema);
        return $columnSchemas;
    }

    /**
     * Get schema table
     *
     * @param string $pool
     * @param string $table
     *
     * @param string $exclude
     * @param string $tablePrefix
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     * @throws DbException
     */
    public function getSchemaTableData(string $pool, string $table, string $exclude, string $tablePrefix): array
    {
        $schemas = $this->schemaDao->getTableSchema($pool, $table, $exclude, $tablePrefix);
        foreach ($schemas as $originTableName => &$schema) {
            $schema['mapping'] = $this->getSafeMappingName($originTableName, true);
        }
        unset($schema);
        return $schemas;
    }

    /**
     * @param string $mapping
     * @param bool   $ucFirst
     *
     * @return string
     */
    private function getSafeMappingName(string $mapping, bool $ucFirst = false)
    {
        $mapping = preg_replace("#[^\w|^\u{4E00}-\u{9FA5}]+#is", '', $mapping);
        $first   = $mapping ? mb_substr($mapping, 0, 1) : '';
        if ($first && !preg_match("#[^[A-Za-z_]|^\u{4E00}-\u{9FA5}]+#is", $first)) {
            return $ucFirst ? ucfirst(StringHelper::camel($mapping)) : StringHelper::camel($mapping);
        }
        if (empty($first)) {
            return $ucFirst ? 'Db' . mt_rand(1, 100) : 'db' . mt_rand(100, 1000);
        }
        return $ucFirst ? 'Db' . $mapping : 'db' . $mapping;
    }
}
