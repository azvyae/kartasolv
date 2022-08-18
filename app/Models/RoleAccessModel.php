<?php

namespace App\Models;

use App\Libraries\Model;

/**
 * Table Role_access model.
 * @see https://codeigniter.com/user_guide/models/model.html for instructions.
 * @package KartasolvApp\Models
 */
class RoleAccessModel extends Model
{
    protected $table = 'role_access';
    protected $primaryKey = 'role_access_id';
    protected $allowedFields = ['role_id', 'menu_id'];
    protected $returnType     = 'object';

    /**
     * Retrieve role access id, used for checkAuth() functon.
     * 
     * @see checkAuth() for information.
     * @param mixed $roleId
     * @param mixed $menuId
     * @return array|object|null
     */
    public function getRoleAccessId($roleId, $menuId)
    {
        $where = [
            'role_id' => $roleId,
            'menu_id' => $menuId
        ];
        return $this->where($where)->first();
    }

    /**
     * Retrieve page menu on the sidebar based on roles.
     * @param mixed $roleId
     * @return mixed
     */
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
