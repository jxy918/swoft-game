<?php
if (! function_exists('service_packer')) {
    /**
     * @return \Swoft\Rpc\Packer\ServicePacker
     */
    function service_packer()
    {
        return \Swoft\App::getBean('servicePacker');
    }
}
