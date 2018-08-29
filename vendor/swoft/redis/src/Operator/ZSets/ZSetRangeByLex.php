<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetRangeByLex extends ZSetRange
{
    /**
     * [ZSet] zRangeByLex
     *
     * @return string
     */
    public function getId()
    {
        return 'zRangeByLex';
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareOptions($options)
    {
        $opts = array_change_key_case($options, CASE_UPPER);
        $finalizedOpts = array();

        if (isset($opts['LIMIT']) && is_array($opts['LIMIT'])) {
            $limit = array_change_key_case($opts['LIMIT'], CASE_UPPER);

            $finalizedOpts[] = 'LIMIT';
            $finalizedOpts[] = isset($limit['OFFSET']) ? $limit['OFFSET'] : $limit[0];
            $finalizedOpts[] = isset($limit['COUNT']) ? $limit['COUNT'] : $limit[1];
        }

        return $finalizedOpts;
    }

    /**
     * {@inheritdoc}
     */
    protected function withScores()
    {
        return false;
    }
}
