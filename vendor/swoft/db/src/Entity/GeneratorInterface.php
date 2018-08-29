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

interface GeneratorInterface
{
    /**
     * 执行入口
     *
     * @param Schema $schema schema对象
     *
     * @return void
     */
    public function execute(Schema $schema);

    /**
     * 获取当前db的所有表
     *
     * @return array
     */
    public function getSchemaTables(): array;

    /**
     * 获取当前表的所有字段信息
     *
     * @param string $table 表名
     *
     * @return array
     */
    public function getTableColumns(string $table): array;
}
