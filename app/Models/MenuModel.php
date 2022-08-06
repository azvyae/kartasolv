<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'menu_id';
    protected $useTimestamps = 'true';
    protected $allowedFields = ['menu_string', 'menu_name', 'menu_icon'];
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;

    public function getMenuId($controllerName)
    {
        $where = [
            'menu_string' => $controllerName
        ];
        return $this->where($where)->get()->getRow();
    }
}
