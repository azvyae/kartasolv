<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {

        $roleName = checkAuth('roleName');
        $data = [
            'title' => "Dasbor $roleName | Karta Sarijadi",
            'sidebar' => true,
        ];
        return view('user/home/index', $data);
    }
}
