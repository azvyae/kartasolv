<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Karang Taruna Ngajomantara Kelurahan Sarijadi'
        ];
        return view('home/index', $data);
    }
    public function history()
    {
        return 'history';
    }

    public function contactUs()
    {
        return 'contact-us';
    }
}
