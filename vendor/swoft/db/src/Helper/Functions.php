<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
if (!function_exists('get_last_sql')) {

    /**
     * @return string
     */
    function get_last_sql(): string
    {
        $contextSqlKey = \Swoft\Db\Helper\DbHelper::getContextSqlKey();
        /* @var \SplStack $stack */
        $stack = \Swoft\Core\RequestContext::getContextDataByKey($contextSqlKey, new \SplStack());

        if ($stack->isEmpty()) {
            return '';
        }
        return $stack->pop();
    }
}
