<?php

namespace App\Models;

use App\Libraries\Model;


class UsersModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $useTimestamps = 'true';
    protected $allowedFields = ['user_name', 'user_email', 'user_password', 'user_temp_mail', 'user_change_mail', 'user_reset_attempt', 'user_last_login'];
    protected $returnType     = 'object';
    protected $validationRules = [
        'user_name' => [
            'label' => 'Nama',
            'rules' => 'required|max_length[64]',
        ],
        'user_email' => [
            'label' => 'Email',
            'rules' => 'required|valid_email|max_length[64]',
        ],
        'user_password' => [
            'label' => 'Kata Sandi',
            'rules' => 'required|min_length[6]',
        ],
        'user_new_password' => [
            'label' => 'Kata Sandi Baru',
            'rules' => 'required|min_length[6]',
        ],
        'password_verify' => [
            'label' => 'Verifikasi Kata Sandi',
            'rules' => 'required_with[user_new_password]|matches[user_new_password]',
            'errors' => [
                'matches'  => 'Kata Sandi Harus Sama!'
            ]
        ],
        'user_temp_mail' => [
            'label' => 'Email',
            'rules' => 'required|valid_email|max_length[64]|is_unique[users.user_email,user_email,{user_email}]',
            'errors' => [
                'is_unique' => 'Email sudah digunakan!'
            ]
        ],
    ];

    public function getFromEmail($email)
    {
        return $this->join('roles', 'roles.role_id = users.role_id')->where('user_email', $email)->get()->getRow();
    }
}
