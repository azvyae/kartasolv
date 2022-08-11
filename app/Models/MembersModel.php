<?php

namespace App\Models;

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
}
