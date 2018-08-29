<?php

namespace Swoft\Session\Bootstrap;

use Swoft\Bean\Annotation\BootBean;
use Swoft\Core\BootBeanInterface;

/**
 * @BootBean()
 */
class CoreBean implements BootBeanInterface
{

    /**
     * @return array
     */
    public function beans()
    {
        return [
            'sessionManager' => [
                'class' => \Swoft\Session\SessionManager::class,
                'config' => [
                    'driver' => 'file',
                    'lifetime' => 120,
                    'expire_on_close' => false,
                    'encrypt' => false,
                    'storage' => '@runtime/sessions',
                    'name' => 'SWOFT_SESSION_ID',
                ],
            ],
        ];
    }

}