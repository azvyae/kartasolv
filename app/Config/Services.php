<?php

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 * 
 * @package Kartasolv\Config
 * 
 */
class Services extends BaseService
{
    /**
     * Override redirect response base service with new one
     * 
     * @param mixed $config Configuration file.
     * @param bool $getShared Check will this method use shared params from original one.
     * 
     * @return mixed New RedirectResponse service.
     */
    public static function redirectresponse($config = null, $getShared = false)
    {
        if ($getShared) {
            return static::getSharedInstance('redirectresponse');
        }

        return new \App\Libraries\RedirectResponse(config('App'));
    }
}
