<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $useTimestamps = 'true';
    protected $allowedFields = ['role_id', 'user_name', 'user_email', 'user_passsword', 'user_temp_mail', 'user_last_login'];
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;

    public function getAllMembers()
    {
        return $this->where('member_active', 1)->orderBy('member_type, member_id', 'asc')->findAll();
    }

    public function getMember($memberId)
    {
        $where = [
            'member_id' => $memberId
        ];
        return $this->where($where)->get()->getRow();
    }
}
