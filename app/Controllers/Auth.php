<?php

namespace App\Controllers;


class Auth extends BaseController
{
    private $usersModel;
    public function __construct()
    {
        $this->usersModel = model('App\Models\UsersModel');
    }
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
        $email = $this->request->getPost('user_email', FILTER_SANITIZE_EMAIL);
        $pass = $this->request->getPost('user_password');
        if ($user = $this->usersModel->getUserFromEmail($email)) {
            if (kartaPasswordVerify($pass, $user->user_password)) {
                return $this->setSession($user);
            } else if (md5($pass) === $user->user_password) {
                $new_data = [
                    'user_id' => $user->user_id,
                    'user_password' => kartaPasswordHash($pass)
                ];
                $this->usersModel->save($new_data);
                return $this->setSession($user);
            }
        }
        $flash = [
            'message' => 'Email atau Kata Sandi Salah!',
            'type' => 'danger'
        ];
        setFlash($flash);
        return redirect()->to(base_url('masuk'))->withInput();
    }

    private function setSession($user)
    {
        $session = session();
        $sessionData = [
            'user' => objectify([
                'userId' => $user->user_id,
                'roleId' => $user->role_id,
                'roleString' => $user->role_string,
                'roleName' => $user->role_name,
            ])
        ];
        $data = [
            'user_id' => $user->user_id,
            'user_last_login' => date('Y-m-d H:i:s')
        ];
        $this->usersModel->save($data);
        $session->set($sessionData);
        return redirect()->to(base_url('dasbor'));
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
