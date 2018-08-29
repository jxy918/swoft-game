<?php
if (! function_exists('session')) {
    /**
     * Get Session
     *
     * @return \Swoft\Session\SessionInterface
     */
    function session()
    {
        /** @var \Swoft\Session\SessionManager $sessionManager */
        $sessionManager = \Swoft\App::getBean('sessionManager');
        return $sessionManager->getSession();
    }
}