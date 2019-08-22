<?php declare(strict_types=1);


namespace Swoft\Config\Contract;


use Swoft\Config\Config;

/**
 * Interface ParserInterface
 */
interface ParserInterface
{
    /**
     * Parse files
     *
     * @param Config $config
     *
     * @return array
     */
    public function parse(Config $config):array ;
}