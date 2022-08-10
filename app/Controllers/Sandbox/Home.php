<?php

namespace App\Controllers\Sandbox;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index($string = '')
    {
        d($this->request->getMethod());
        return view('sandbox/home/index');
    }

    public function phpinfo()
    {
        phpinfo();
    }


    public function login()
    {
        $data = [
            'name' => 'Yousef',
            'link' => 'https://google.com/',
            'oldEmail' => 'wanda@gmail.com',
            'newEmail' => 'supersaiyan@gmail.com'
        ];
        return view('layout/email/email_change', $data);
    }
}
