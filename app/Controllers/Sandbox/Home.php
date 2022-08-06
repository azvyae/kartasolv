<?php

namespace App\Controllers\Sandbox;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index($string = '')
    {
        echo "OK";
    }

    public function login()
    {
        $session = session();
        $sessionData = [
            'user' => objectify([
                'userId' => 1,
                'roleId' => 1,
                'roleString' => 'admin',
                'roleName' => 'Administrator',
            ])
        ];

        $session->set($sessionData);
    }

    public function logout()
    {
        session_destroy();
    }
}
