<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $useTimestamps = 'true';
    protected $allowedFields = ['role_id', 'user_name', 'user_email', 'user_password', 'user_temp_mail', 'user_change_mail', 'user_reset_attempt', 'user_last_login'];
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;

    public function getFromEmail($email)
    {
        return $this->join('roles', 'roles.role_id = users.role_id')->where('user_email', $email)->get()->getRow();
    }
}
