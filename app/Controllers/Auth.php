<?php

namespace App\Controllers;


class Auth extends BaseController
{
    public function login()
    {
        if ($this->request->getMethod() !== 'post') {
            $data = [
                'title' => 'Masuk | Karta Sarijadi',
            ];
            return view('auth/login', $data);
        } else if (!$this->validate('login')) {
            return redirect()->to(base_url('masuk'))->withInput();
        }
        if ($this->request->getPost('user_email') !== 'karangtarunasarijadi@gmail.com') {
            $flash = [
                'message' => 'Gagal login!',
                'type' => 'danger'
            ];
            setFlash($flash);
            return redirect()->to(base_url('masuk'))->withInput();
        }
        dd($this->request->getPost());
    }

    public function logout()
    {
        if (!isset($_SESSION))
            session_start();
        if (session_status() === PHP_SESSION_ACTIVE)
            session_destroy();
        return redirect()->to(base_url('masuk'));
    }

    public function forgetPassword()
    {
        $data = [
            'title' => 'Lupa Kata Sandi | Karta Sarijadi'
        ];
        return view('auth/forget_password', $data);
    }

    public function resetPassword()
    {
        $data = [
            'title' => 'Atur Ulang Kata Sandi | Karta Sarijadi'
        ];
        return view('auth/reset_password', $data);
    }
}
