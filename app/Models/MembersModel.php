<?php

namespace App\Models;

use App\Libraries\DatabaseManager;
use App\Libraries\Model;


class MembersModel extends Model
{
    protected $table = 'members';
    protected $primaryKey = 'member_id';
    protected $useTimestamps = 'true';
    protected $allowedFields = ['member_name', 'member_position', 'member_type', 'member_active', 'member_image'];
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $beforeInsert = ['setCreatedBy'];
    protected $beforeUpdate = ['setModifiedBy'];
    protected $beforeDelete = ['setModifiedBy'];

    public function getMembers()
    {
        return $this->where('member_active', 1)->orderBy('member_type, member_id', 'asc')->findAll();
    }
    public function getDatatable($condition)
    {
        $dbMan = new DatabaseManager;
        $query = [
            'result' => 'result',
            'table'  => 'members',
            'select' => ['member_id', 'member_name', 'member_position', 'member_type', 'member_active', 'member_image'],
        ];

        $query += $dbMan->filterDatatables($condition);
        $data = [
            'totalRows' => $dbMan->countAll($query),
            'result' => $dbMan->read($query),
            'searchable' => array_map(function ($e) {
                return $e . ":name";
            }, $condition['columnSearch'])
        ];

        return objectify($data);
    }
}
