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

use Swoft\Log\Log;

/**
 * DbDataResult
 */
class DbDataResult extends DbResult
{
    /**
     * @param array ...$params
     * @return mixed
     */
    public function getResult(...$params)
    {
        $result = $this->getResultByClassName();
        $this->release();
        foreach ($this->decorators ?? [] as $decorator) {
            $result = value($decorator($result));
        }

        Log::profileEnd($this->profileKey);
        return $result;
    }
}
