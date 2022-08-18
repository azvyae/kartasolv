<?php

namespace App\Models;

use App\Libraries\Model;

/**
 * Table Menu model.
 * @see https://codeigniter.com/user_guide/models/model.html for instructions.
 * @package KartasolvApp\Models
 */
class MenuModel extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'menu_id';
    protected $allowedFields = ['menu_string'];
    protected $returnType     = 'object';

    /**
     * Retrieve menu id based on controller name.
     * @param string $controllerName Menu to be searched.
     * @return mixed Menu data.
     */
    public function getMenuId($controllerName)
    {
        $where = [
            'menu_string' => $controllerName
        ];
        return $this->where($where)->get()->getRow();
    }
}
