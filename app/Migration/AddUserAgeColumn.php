<?php declare(strict_types=1);


namespace App\Migration;


use Swoft\Devtool\Annotation\Mapping\Migration;
use Swoft\Devtool\Migration\Migration as BaseMigration;

/**
 * Class AddUserAgeColumn
 *
 * @since 2.0
 *
 * @Migration(time=20190725172848)
 */
class AddUserAgeColumn extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        //

    }

    /**
     * @return void
     */
    public function down(): void
    {

    }
}
