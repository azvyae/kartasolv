<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dasbor | Karta Sarijadi',
            'sidebar' => true
        ];
        return view('user/home/index', $data);
    }
}
