<?php declare(strict_types=1);


namespace SwoftTest\Db\Unit\Schema;

use ReflectionException;
use Swoft\Db\Schema;
use SwoftTest\Db\Unit\TestCase;
use Swoft\Db\Schema\Builder;
use Swoft\Db\Schema\Blueprint;

/**
 * Class BuilderTest
 *
 * @since 2.0
 */
class SchemaBuilderTest extends TestCase
{

    /**
     *
     *
     * @return Builder
     * @throws ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \Swoft\Db\Exception\DbException
     */
    public function getBuilder(): Builder
    {
        $pool    = [
            'db.pool',
            'db.pool'
        ];
        $builder = Builder::new($pool[array_rand($pool)]);

        return $builder;
    }

    public function testColumn()
    {
        $builder = $this->getBuilder();

        $res = $builder->getColumnListing('user');

        $this->assertTrue(in_array('id', $res));
        $this->assertTrue(in_array('age', $res));

        $this->assertFalse($builder->hasColumn('user', 'xxxx'));
        $this->assertTrue($builder->hasColumn('user', 'age'));

        $this->assertTrue($builder->hasColumns('user', ['id', 'age', 'user_desc']));
        $this->assertFalse($builder->hasColumns('user', ['id', 'age', 'xxx']));
    }

    public function testTable()
    {
        $builder = $this->getBuilder();
        $this->assertTrue($builder->hasTable('user'));
        $this->assertFalse($builder->hasTable('user' . mt_rand(10, 111)));
    }

    public function testCreate()
    {
        $builder = $this->getBuilder();

        $rename = 'user2';
        $table  = 'user11';

        Schema::dropIfExists($rename);
        Schema::dropIfExists($table);

        Schema::create($table, function (Blueprint $blueprint) {
            $blueprint->comment('test table');
            $blueprint->json('test_json');
            $blueprint->integer('id')->autoIncrement();
            $blueprint->bigInteger('uid')->index();
            $blueprint->tinyInteger('status')->index('idx_status')->default(1);
            $blueprint->uuid('uuid')->unique();
            $blueprint->integer('create_time')->comment('create_time');
            $blueprint->index(['uid', 'id']);
            $blueprint->jsonb('test_json_1');
            $blueprint->unique(['uuid', 'id'], 'unq_uuid_id');
            $blueprint->softDeletes();
        });

        Schema::createIfNotExists($table, function (Blueprint $blueprint) {
            $blueprint->integer('id');
            $blueprint->integer('create_time');
        });

        // Bind db pool
        Schema::getSchemaBuilder('db.pool2')->table($table, function (Blueprint $blueprint) use ($rename) {
            // Rename index
            $blueprint->renameIndex('idx_status', 'idx_sta');
            // Rename table
            $blueprint->rename($rename);
            $blueprint->addColumn('integer', 'add_id')->after('id');
            $blueprint->integer('t_id')->after('add_id');

        });

        Schema::rename($rename, $table);

        // Bind db pool
        Schema::table($table, function (Blueprint $blueprint) {
            // Rename column
            $blueprint->renameColumn('id', 'user_id', 'bigint', 20);

        });
        Schema::enableForeignKeyConstraints();
        Schema::disableForeignKeyConstraints();

        $this->assertTrue(true);
    }
}
