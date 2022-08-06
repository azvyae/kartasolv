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
        return 'logged-out';
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
