<?php
namespace Swoft\Rpc\Packer;

use Swoft\App;

/**
 * Trait EofTrait
 *
 * @package Swoft\Rpc\Packer
 */
trait EofTrait
{

    /**
     * @return string
     */
    public function getEof():string
    {
        $eof = '';
        $tcpSettings = App::$server->getTcpSetting();
        if (isset($tcpSettings['package_eof'])) {
            $eof = $tcpSettings['package_eof'];
        }
        return $eof;
    }

}