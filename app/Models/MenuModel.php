<?php

namespace App\Models;

use App\Libraries\Model;


class MenuModel extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'menu_id';
    protected $allowedFields = ['menu_string'];
    protected $returnType     = 'object';

    public function getMenuId($controllerName)
    {
        $where = [
            'menu_string' => $controllerName
        ];
        return $this->where($where)->get()->getRow();
    }
}
