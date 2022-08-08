<?php

namespace App\Controllers\Sandbox;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index($string = '')
    {
        helper('date');
        $data = [
            'name' => 'Yousef',
            'link' => 'https://google.com'
        ];
        return view('layout/email/reset_password', $data);
        $date = date('Y-m-d H:i:s', strtotime('+15 minutes', now()));
        dd();
        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => 'mail.kartasarijadi.com',
            'SMTPUser' => 'no-reply@kartasarijadi.com',
            'SMTPPass' => getenv('app.emailpass'),
            'SMTPPort' => '587',
            'mailType' => 'html',
        ];
        $email = \Config\Services::email($config);
        $email->setFrom('no-reply@kartasarijadi.com', 'NoReply - Karang Taruna Sarijadi');
        $email->setTo('erstevn@gmail.com');

        $email->setSubject('Verifikasi Proses Atur Ulang Kata Sandi');

        $email->setMessage();
        return $email->send();
        // return view('sandbox/home/index');
    }


    public function login()
    {
        $data = [
            'name' => 'Yousef',
            'link' => 'https://google.com'
        ];
        return view('layout/email/reset_password', $data);
    }

    public function logout()
    {
        session_destroy();
    }
}
