<?php declare(strict_types=1);


namespace Swoft\Devtool\Command;

use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandArgument;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandOption;
use Swoft\Db\Pool;
use Swoft\Devtool\Model\Logic\EntityLogic;
use Throwable;
use function input;

/**
 * Generate entity class by database table names[by <cyan>devtool</cyan>]
 *
 * @Command()
 * @since 2.0
 */
class EntityCommand
{

    /**
     * @Inject()
     *
     * @var EntityLogic
     */
    private $logic;

    /**
     * Generate database entity
     *
     * @CommandMapping(alias="c,gen")
     * @CommandArgument(name="table", desc="database table names", type="string")
     * @CommandOption(name="table", desc="database table names", type="string")
     * @CommandOption(name="pool", desc="choose default database pool", type="string", default="db.pool")
     * @CommandOption(name="path", desc="generate entity file path", type="string", default="@app/Model/Entity")
     * @CommandOption(name="y", desc="auto generate", type="string")
     * @CommandOption(name="field_prefix", desc="database field prefix ,alias is 'fp'", type="string")
     * @CommandOption(name="table_prefix", desc="like match database table prefix, alias is 'tp'", type="string")
     * @CommandOption(name="exclude", desc="expect generate database table entity, alias is 'exc'", type="string")
     * @CommandOption(name="td", desc="generate entity template path",type="string", default="@devtool/devtool/resource/template")
     *
     */
    public function create(): void
    {
        $table       = input()->get('table', input()->getOpt('table'));
        $pool        = input()->getOpt('pool', Pool::DEFAULT_POOL);
        $path        = input()->getOpt('path', '@app/Model/Entity');
        $isConfirm   = input()->getOpt('y', false);
        $fieldPrefix = input()->getOpt('field_prefix', input()->getOpt('fp'));
        $tablePrefix = input()->getOpt('table_prefix', input()->getOpt('tp'));
        $exclude     = input()->getOpt('exc', input()->getOpt('exclude'));
        $tplDir      = input()->getOpt('td', '@devtool/devtool/resource/template');

        try{
            $this->logic->create([
                (string)$table,
                (string)$tablePrefix,
                (string)$fieldPrefix,
                (string)$exclude,
                (string)$pool,
                (string)$path,
                (bool)$isConfirm,
                (string)$tplDir
            ]);
        } catch (Throwable $exception) {
            output()->colored($exception->getMessage(), 'error');
        }
    }
}
