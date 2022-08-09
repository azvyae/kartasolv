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
        if (!$this->request->getPost()) {
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
            'user_last_login' => date('Y-m-d H:i:s'),
            'user_reset_attempt' => null
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
        if (!$this->request->getPost()) {
            $data = [
                'title' => 'Lupa Kata Sandi | Karta Sarijadi'
            ];
            return view('auth/forget_password', $data);
        } else if (!$this->validate('forgetPassword')) {
            return redirect()->to(base_url('lupa-kata-sandi'))->withInput();
        }
        $email = $this->request->getPost('user_email', FILTER_SANITIZE_EMAIL);
        if ($user = $this->usersModel->getUserFromEmail($email)) {
            if ($last = strtotime($user->user_reset_attempt)) {
                $now = strtotime(date('Y-m-d H:i:s'));
                $selisih = $last - $now;
                if (date('i:s', $selisih) > '10:00') {
                    $flash = [
                        'message' => 'Kamu baru saja melakukan permintaan atur ulang kata sandi, tunggu 5 menit lagi.',
                        'type' => 'warning'
                    ];
                    setFlash($flash);
                    return redirect()->to(base_url('lupa-kata-sandi'));
                }
            }


            $time = date('Y-m-d H:i:s', strtotime('+15 minutes', time()));
            $updateData = [
                'user_id' => $user->user_id,
                'user_reset_attempt' => $time
            ];
            if ($this->usersModel->save($updateData)) {
                $config = [
                    'protocol' => 'smtp',
                    'SMTPHost' => 'mail.kartasarijadi.com',
                    'SMTPUser' => 'no-reply@kartasarijadi.com',
                    'SMTPPass' => getenv('app.emailpass'),
                    'SMTPPort' => '587',
                    'mailType' => 'html',
                ];
                $email = \Config\Services::email($config);
                $email->setFrom('no-reply@kartasarijadi.com', 'No Reply - Karang Taruna Sarijadi');
                $email->setTo($user->user_email);

                $email->setSubject('Verifikasi Proses Atur Ulang Kata Sandi');
                $uuid = encode($user->user_id, 'resetPassword');
                $attempt = encode(strtotime($time), 'resetPassword');
                $data = [
                    'name' => $user->user_name,
                    'link' => base_url("atur-ulang-kata-sandi?uuid=$uuid&attempt=$attempt")
                ];
                $email->setMessage(view('layout/email/reset_password', $data));
                if ($email->send()) {
                    $flash = [
                        'message' => 'Silakan cek emailmu untuk melanjutkan.',
                        'type' => 'success'
                    ];
                    setFlash($flash);
                    return redirect()->to(base_url('masuk'));
                }
            }

            $flash = [
                'message' => 'Email yang kamu masukkan tidak ditemukan!',
                'type' => 'success'
            ];
            setFlash($flash);
            return redirect()->to(base_url('lupa-kata-sandi'));
        }
        $flash = [
            'message' => 'Email yang kamu tulis tidak ditemukan!',
            'type' => 'danger'
        ];
        setFlash($flash);
        return redirect()->to(base_url('lupa-kata-sandi'))->withInput();
    }

    public function resetPassword()
    {
        $uuid = decode($this->request->getGet('uuid'), 'resetPassword');
        $attempt = date('Y-m-d H:i:s', decode($this->request->getGet('attempt'), 'resetPassword'));
        if ($uuid && $attempt) {
            if ($user = $this->usersModel->getUserFromValidationAttempt($uuid, $attempt)) {
                if ($user->user_reset_attempt <= date('Y-m-d H:i:s') or $user->user_reset_attempt !== $attempt) {
                    $flash = [
                        'message' => 'Link tidak valid/kadaluarsa.',
                        'type' => 'warning'
                    ];
                    setFlash($flash);
                    return redirect()->to(base_url('lupa-kata-sandi'));
                }
                if (!$this->request->getPost()) {
                    $data = [
                        'title' => 'Atur Ulang Kata Sandi | Karta Sarijadi'
                    ];
                    return view('auth/reset_password', $data);
                } else if (!$this->validate('resetPassword')) {
                    return redirect()->to(base_url('atur-ulang-kata-sandi?uuid=' . $this->request->getGet('uuid') . '&attempt=' . $this->request->getGet('attempt')))->withInput();
                }

                $data = [
                    'user_id' => $user->user_id,
                    'user_password' => kartaPasswordHash($this->request->getPost('user_password')),
                    'user_reset_attempt' => null
                ];
                if ($this->usersModel->save($data)) {
                    $flash = [
                        'message' => 'Berhasil mengubah kata sandi.',
                        'type' => 'success'
                    ];
                    setFlash($flash);
                    return redirect()->to(base_url('masuk'));
                }
                $flash = [
                    'message' => 'Gagal mengubah kata sandi.',
                    'type' => 'danger'
                ];
                setFlash($flash);
                return redirect()->to(base_url('atur-ulang-kata-sandi?uuid=' . $this->request->getGet('uuid') . '&attempt=' . $this->request->getGet('attempt')))->withInput();
            }
        }
        return redirect()->to(base_url('lupa-kata-sandi'));
    }
}
