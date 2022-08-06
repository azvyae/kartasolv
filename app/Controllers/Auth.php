<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function login()
    {
        return 'logged-in';
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
        return 'lupa-password';
    }

    public function resetPassword()
    {
        return 'reset-password';
    }
}
