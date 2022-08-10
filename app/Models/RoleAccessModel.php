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
        $where = [
            'role_id' => $roleId,
            'menu_id' => $menuId
        ];
        return $this->where($where)->first();
    }

    public function getPageByRole($roleId)
    {
        return $this
            ->join(
                'menu',
                'menu.menu_id = role_access.menu_id',
            )
            ->join(
                'pages',
                'menu.menu_id = pages.menu_id',

            )
            ->where([
                'role_id' => $roleId
            ])
            ->orderBy('pages.page_id', 'asc')
            ->get()->getResult();
    }
}
