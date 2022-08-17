<?php

namespace App\Controllers;

/**
 * Authentication Controller.
 *
 * This controller has some crucial authentication methods from login,
 * logging out, forget password, reset password, verifying email, and
 * setting session.
 * 
 * @property \App\Models\UsersModel $um UsersModel initiator.
 * 
 * @package Controllers
 */
class Auth extends BaseController
{
    private $um;
    /**
     * Construct UsersModel.
     */
    public function __construct()
    {
        $this->um = new \App\Models\UsersModel();
    }

    /**
     * Shows login page and its form.
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function index()
    {
        switch (getMethod()) {
            case 'post':
                return $this->_login();
                break;
            case 'delete':
                return $this->_logout();
                break;
            default:
                break;
        }
        $data = [
            'title' => 'Masuk | Karta Sarijadi',
        ];
        return view('auth/index', $data);
    }

    /**
     * Validate login form, sending email through post form
     * and verify password.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    private function _login()
    {
        if ($referrer = acceptFrom('masuk')) {
            return redirect()->to($referrer);
        }
        $rules = $this->um->getValidationRules(['only' => ['user_email', 'user_password'], 'add' => ['gRecaptcha']]);
        if (!$this->validate($rules)) {
            return redirect()->to('masuk')->withInput();
        }
        $email = $this->request->getPost('user_email', FILTER_SANITIZE_EMAIL);
        $pass = $this->request->getPost('user_password');
        if ($user = $this->um->getFromEmail($email)) {
            if (kartaPasswordVerify($pass, $user->user_password)) {
                return $this->_setSession($user);
            } else if (md5($pass) === $user->user_password) {
                $data = [
                    'user_id' => $user->user_id,
                    'user_password' => kartaPasswordHash($pass)
                ];
                $this->um->save($data);
                return $this->_setSession($user);
            }
        }
        $flash = [
            'message' => 'Email atau Kata Sandi Salah!',
            'type' => 'danger'
        ];
        setFlash($flash);
        return redirect()->to('masuk')->withInput();
    }

    private function _logout()
    {
        if (!$this->validate('gRecaptcha')) {
            return redirect()->to('/');
        }
        if (!isset($_SESSION)) session_start();
        if (session_status() === PHP_SESSION_ACTIVE) session_destroy();
        return redirect()->to('masuk');
    }

    public function forgetPassword()
    {
        if (getMethod('post')) {
            return $this->_forgetPassword();
        }
        $data = [
            'title' => 'Lupa Kata Sandi | Karta Sarijadi'
        ];
        return view('auth/forget_password', $data);
    }

    private function _forgetPassword()
    {
        $rules = $this->um->getValidationRules(['only' => ['user_email'], 'add' => ['gRecaptcha']]);
        if (!$this->validate($rules)) {
            return redirect()->to('lupa-kata-sandi')->withInput();
        }
        $email = $this->request->getPost('user_email', FILTER_SANITIZE_EMAIL);
        if ($user = $this->um->getFromEmail($email)) {
            if ($last = strtotime($user->user_reset_attempt)) {
                $now = strtotime(date('Y-m-d H:i:s'));
                $selisih = $last - $now;
                if (date('i:s', $selisih) > '10:00') {
                    $flash = [
                        'message' => 'Kamu baru saja melakukan permintaan atur ulang kata sandi, tunggu 5 menit lagi.',
                        'type' => 'warning'
                    ];
                    setFlash($flash);
                    return redirect()->to('lupa-kata-sandi');
                }
            }
            $time = date('Y-m-d H:i:s', strtotime('+15 minutes', time()));
            $updateData = [
                'user_id' => $user->user_id,
                'user_reset_attempt' => $time
            ];
            if ($this->_verifyPassword($user, $time)) {
                if ($this->um->save($updateData)) {
                    $flash = [
                        'message' => 'Silakan cek emailmu untuk melanjutkan.',
                        'type' => 'success'
                    ];
                    setFlash($flash);
                    return redirect()->to('masuk');
                }
            }
        } else {
            $flash = [
                'message' => 'Email yang kamu tulis tidak ditemukan!',
                'type' => 'danger'
            ];
            setFlash($flash);
        }
        return redirect()->to('lupa-kata-sandi')->withInput();
    }

    public function resetPassword()
    {
        $uuid = decode($this->request->getGet('uuid'), 'resetPassword');
        $attempt = date('Y-m-d H:i:s', decode($this->request->getGet('attempt'), 'resetPassword'));
        if ($uuid && $attempt) {
            if ($user = $this->um->find($uuid, true)) {
                if ($user->user_reset_attempt <= date('Y-m-d H:i:s') or $user->user_reset_attempt !== $attempt) {
                    $flash = [
                        'message' => 'Link tidak valid/kadaluarsa.',
                        'type' => 'warning'
                    ];
                    setFlash($flash);
                    return redirect()->to('lupa-kata-sandi');
                }
                if (getMethod('put')) {
                    return $this->_resetPassword($uuid);
                }
                $data = [
                    'title' => 'Atur Ulang Kata Sandi | Karta Sarijadi'
                ];
                return view('auth/reset_password', $data);
            }
            $flash = [
                'message' => 'Pengguna tidak ditemukan.',
                'type' => 'danger'
            ];
            setFlash($flash);
        }
        return redirect()->to('lupa-kata-sandi');
    }

    private function _resetPassword($uuid)
    {
        $rules = $this->um->getValidationRules(['only' => ['user_new_password', 'password_verify'], 'add' => ['gRecaptcha']]);
        $user = $this->um->find($uuid, true);
        $rawUUID = $this->request->getGet('uuid');
        $rawAttempt = $this->request->getGet('attempt');
        if (!$this->validate($rules)) {
            return redirect()->to("atur-ulang-kata-sandi?uuid=$rawUUID&attempt=$rawAttempt")->withInput();
        }

        $data = [
            'user_id' => $user->user_id,
            'user_password' => kartaPasswordHash($this->request->getPost('user_new_password')),
            'user_reset_attempt' => null
        ];
        $this->um->save($data);
        $flash = [
            'message' => 'Berhasil mengubah kata sandi.',
            'type' => 'success'
        ];
        setFlash($flash);
        return redirect()->to('masuk');
    }

    public function verifyEmail()
    {
        $uuid = decode($this->request->getGet('uuid'), 'changeEmail');
        $attempt = date('Y-m-d H:i:s', decode($this->request->getGet('attempt'), 'changeEmail'));
        $cancel = (bool) $this->request->getGet('cancel');

        if ($uuid && $attempt) {
            $user = $this->um->find($uuid, true);
            if ($user) {
                if ($user->user_change_mail <= date('Y-m-d H:i:s') or $user->user_change_mail !== $attempt) {
                    $flash = [
                        'message' => 'Link tidak valid/kadaluarsa.',
                        'type' => 'warning'
                    ];
                    setFlash($flash);
                } else {
                    $data = [
                        'user_id' => $user->user_id,
                        'user_email' => $user->user_temp_mail,
                        'user_change_mail' => null,
                        'user_temp_mail' => null
                    ];
                    if ($this->um->save($data)) {
                        $flash = [
                            'message' => 'Berhasil mengubah email.',
                            'type' => 'success'
                        ];
                        setFlash($flash);
                    }
                }
            } else {
                $flash = [
                    'message' => 'Pengguna tidak ditemukan.',
                    'type' => 'danger'
                ];
                setFlash($flash);
            }
            if ($cancel) {
                $data = [
                    'user_id' => $user->user_id,
                    'user_change_mail' => null,
                    'user_temp_mail' => null
                ];
                if ($this->um->save($data)) {
                    $flash = [
                        'message' => 'Berhasil membatalkan perubahan email.',
                        'type' => 'success'
                    ];
                    setFlash($flash);
                }
            }
        }
        if (checkAuth('userId')) {
            return redirect()->to('profil');
        }
        return redirect()->to('masuk');
    }

    private function _verifyPassword($user, $time)
    {
        $config = [
            'protocol' => getenv('email.protocol'),
            'SMTPHost' => 'mail.kartasarijadi.com',
            'SMTPUser' => 'no-reply@kartasarijadi.com',
            'mailType' => 'html',
            'SMTPPass' => getenv('email.pass'),
            'SMTPPort' => getenv('email.port'),
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
        return $email->send(true);
    }

    private function _setSession($user)
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
        $this->um->save($data);
        $session->set($sessionData);
        return redirect()->to('dasbor');
    }
}
