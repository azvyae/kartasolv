<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleAccessModel extends Model
{
    protected $table = 'role_access';
    protected $primaryKey = 'role_access_id';
    protected $useTimestamps = 'true';
    protected $allowedFields = ['role_id', 'menu_id'];
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;

    public function getRoleAccessId($roleId, $menuId)
    {
        // dd($roleId, $menuId);
        $where = [
            'role_id' => $roleId,
            'menu_id' => $menuId
        ];
        return $this->where($where)->first();
    }
}
