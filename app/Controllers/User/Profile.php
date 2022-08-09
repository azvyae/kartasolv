<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class Profile extends BaseController
{
    public function index()
    {
        $userModel = model('UsersModel');
        $user = $userModel->find(checkAuth('user_id'))[0];
        $optionalRules = '';
        $passwords = objectify($this->request->getPost(['user_password', 'user_new_password', 'password_verify']));
        if ($passwords->user_password or $passwords->user_new_password or $passwords->password_verify) {
            $optionalRules .= '|userPassword|userNewPassword|passwordVerify';
        }
        if (!$this->request->getPost()) {
            $data = [
                'title' => "Ubah Akun/Profil | Karta Sarijadi",
                'sidebar' => true,
                'user' => $user
            ];
            return view('user/profile/index', $data);
        } else if (!$this->validate("userEmail|updateProfile|gRecaptcha{$optionalRules}")) {
            return redirect()->to(base_url('profil'))->withInput();
        }

        $updateData = [
            'user_id' => $user->user_id,
            'user_name' => $this->request->getPost('user_name')
        ];
        $message = 'Berhasil melakukan perubahan.';
        if ($passwords->user_password) {
            if (!kartaPasswordVerify($passwords->user_password, $user->user_password)) {
                $flash = [
                    'message' => 'Kata sandi salah.',
                    'type' => 'danger'
                ];
                setFlash($flash);
                return redirect()->to(base_url('profil'))->withInput();
            }
            $updateData += [
                'user_password' => kartaPasswordHash($passwords->user_new_password)
            ];
            $message  .= ' Kata sandi berhasil diubah.';
        }
        if ($user->user_email !== ($tempMail = $this->request->getPost('user_temp_mail'))) {
            $time = date('Y-m-d H:i:s', strtotime('+30 minutes', time()));
            if ($this->verifyEmail($user, $tempMail, $time)) {
                $updateData += [
                    'user_temp_mail' => $tempMail,
                    'user_change_mail' => $time
                ];
                $message  .= ' Silakan cek emailmu untuk verifikasi perubahan email.';
            }
        }
        if ($userModel->save($updateData)) {
            $flash = [
                'message' => $message,
                'type' => 'success'
            ];
            setFlash($flash);
            return redirect()->to(base_url('profil'));
        }
        $flash = [
            'message' => 'Gagal melakukan perubahan.',
            'type' => 'danger'
        ];
        setFlash($flash);
        return redirect()->to(base_url('profil'))->withInput();
    }

    private function verifyEmail($user, $tempMail, $time)

    {
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
        $email->setTo($tempMail);
        $email->setSubject('Verifikasi Emailmu');
        $uuid = encode($user->user_id, 'changeEmail');
        $attempt = encode(strtotime($time), 'changeEmail');
        $data = [
            'oldEmail' => $user->user_email,
            'newEmail' => $tempMail,
            'name' => $user->user_name,
            'link' => base_url("verifikasi?uuid=$uuid&attempt=$attempt")
        ];
        $email->setMessage(view('layout/email/email_change', $data));
        return $email->send();
    }
}
