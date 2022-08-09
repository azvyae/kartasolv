<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class Profile extends BaseController
{
    public function index()
    {
        $data = [
            'title' => "Ubah Akun/Profil | Karta Sarijadi",
            'sidebar' => true,
        ];
        return view('user/profile/index', $data);
    }
}
