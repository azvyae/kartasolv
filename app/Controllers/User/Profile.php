<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

/**
 * This controller purpose is to change user account details.
 *
 * This controller has 4 main method, index, _updateProfile, _verifyEmail, and public function verifyEmail.
 * Main purpose of this controller is to provide every logged in users to
 * change profile data like email, and password through the system.
 * 
 * 
 * @package KartasolvApp\Controllers\User
 */
class Profile extends BaseController
{
    /**
     * UsersModel initiator.
     * @var \App\Models\UsersModel $um 
     */
    protected $um;

    /**
     * Construct UsersModel.
     */
    public function __construct()
    {
        $this->um = new \App\Models\UsersModel();
    }

    /**
     * This method is to show form view of profile/account settings.
     * @return \CodeIgniter\HTTP\RedirectResponse|string View or Redirection.
     */
    public function index()
    {
        if (getMethod('put')) {
            return $this->_updateProfile();
        }
        $user = $this->um->find(checkAuth('userId'), true);
        $data = [
            'title' => "Ubah Akun/Profil | Karta Sarijadi",
            'sidebar' => true,
            'user' => $user
        ];
        return view('user/profile/index', $data);
    }

    /**
     * Validate and submit form to database.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection.
     */
    private function _updateProfile()
    {
        // @codeCoverageIgnoreStart
        if ($referrer = acceptFrom('profil')) {
            return redirect()->to($referrer);
        }
        // @codeCoverageIgnoreEnd
        $rules  = $this->um->getValidationRules(
            [
                'except' => ['user_password', 'user_new_password', 'password_verify'],
                'add' => ['gRecaptcha']
            ]
        );
        $user = $this->um->find(checkAuth('userId'), true);
        $passwords = $this->request->getPost(['user_password', 'user_new_password']);
        if ($passwords['user_password'] or $passwords['user_new_password']) {
            $rules += $this->um->getValidationRules();
        }
        if (!$this->validate($rules)) {
            return redirect()->to('profil')->withInput();
        }

        $data = [
            'user_id' => $user->user_id,
            'user_name' => $this->request->getPost('user_name')
        ];
        $message = 'Berhasil melakukan perubahan.';
        if ($passwords['user_password']) {
            if (!kartaPasswordVerify($passwords['user_password'], $user->user_password)) {
                $flash = [
                    'message' => 'Kata sandi salah.',
                    'type' => 'danger'
                ];
                setFlash($flash);
                return redirect()->to('profil')->withInput();
            }
            $data += [
                'user_password' => kartaPasswordHash($passwords['user_new_password'])
            ];
            $message  .= ' Kata sandi berhasil diubah.';
        }
        if ($user->user_email !== ($tempMail = $this->request->getPost('user_temp_mail', FILTER_SANITIZE_EMAIL))) {
            $time = date('Y-m-d H:i:s', strtotime('+30 minutes', time()));
            if ($this->_verifyEmail($user, $tempMail, $time)) {
                $data += [
                    'user_temp_mail' => $tempMail,
                    'user_change_mail' => $time
                ];
                $message  .= ' Silakan cek emailmu untuk verifikasi perubahan email.';
            } else {
                // @codeCoverageIgnoreStart
                $message  .= ' Namun gagal melakukan perubahan email.';
                // @codeCoverageIgnoreEnd
            }
        }
        if ($this->um->save($data)) {
            $flash = [
                'message' => $message,
                'type' => 'success'
            ];
            setFlash($flash);
            return redirect()->to('profil');
        }
    }

    /**
     * Send email method to new email posted on the form.
     * @param mixed $user   User data provided from updated profile.
     * @param mixed $tempMail   User temporary email.
     * @param mixed $time   Timestamp that will be used for attempt variable creation.
     * @return bool Sent/Not Sent Email.
     */
    private function _verifyEmail($user, $tempMail, $time)
    {
        $config = [
            'protocol' => getenv('email.protocol'),
            'SMTPHost' => 'mail.kartasarijadi.com',
            'SMTPUser' => 'no-reply@kartasarijadi.com',
            'SMTPPass' => getenv('email.pass'),
            'SMTPPort' => getenv('email.port'),
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

    /**
     * Control email verification or when cancelling email verification,
     * this method only accessible if url provided is valid.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection.
     */
    public function verifyEmail()
    {
        $uuid = decode($this->request->getGet('uuid'), 'changeEmail');
        $attempt = date('Y-m-d H:i:s', decode($this->request->getGet('attempt'), 'changeEmail'));
        $cancel = (bool) $this->request->getGet('cancel');

        if ($uuid && $attempt) {
            $user = $this->um->find($uuid, true);
            $loggedInUserId = checkAuth('userId');
            if ($user && $user->user_id == $loggedInUserId) {
                if ($cancel) {
                    $data = [
                        'user_id' => $user->user_id,
                        'user_change_mail' => null,
                        'user_temp_mail' => null
                    ];
                    if ($this->um->skipValidation()->save($data)) {
                        $flash = [
                            'message' => 'Berhasil membatalkan perubahan email.',
                            'type' => 'success'
                        ];
                        setFlash($flash);
                    }
                } else if ($user->user_change_mail <= date('Y-m-d H:i:s') or $user->user_change_mail !== $attempt) {
                    $flash = [
                        'message' => 'Link tidak valid/kadaluarsa.',
                        'type' => 'warning'
                    ];
                    setFlash($flash);
                } else {
                    if ($user->user_temp_mail) {
                        $data  = [
                            'user_id' => $user->user_id,
                            'user_email' => $user->user_temp_mail,
                            'user_change_mail' => null,
                            'user_temp_mail' => null
                        ];
                        if ($this->um->skipValidation()->save($data)) {
                            $flash = [
                                'message' => 'Berhasil mengubah email.',
                                'type' => 'success'
                            ];
                            setFlash($flash);
                        }
                    } else {
                        $flash = [
                            'message' => 'Gagal melakukan perubahan email.',
                            'type' => 'danger'
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
        }
        return redirect()->to('profil');
    }
}
