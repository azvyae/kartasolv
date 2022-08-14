<?php

namespace App\Models;

use App\Libraries\DatabaseManager;
use App\Libraries\Model;


class CommunitiesModel extends Model
{
    protected $table = 'communities';
    protected $primaryKey = 'community_id';
    protected $useTimestamps = true;
    protected $allowedFields = ['community_identifier', 'community_name', 'community_address', 'pmpsks_type', 'pmpsks_status'];
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $beforeInsert = ['setCreatedBy'];
    protected $beforeUpdate = ['setModifiedBy'];
    protected $afterDelete = ['setDeletedBy'];
    protected $validationRules = [
        'community_identifier' => [
            'label' => 'NIK',
            'rules' => 'required|min_length[16]|max_length[16]|numeric',
        ],
        'community_name' => [
            'label' => 'Nama',
            'rules' => 'required|max_length[128]|string',
        ],
        'community_address' => [
            'label' => 'Alamat ',
            'rules' => 'required|in_list[1,2,3,4]',
            'errors' => [
                'in_list[1,2,3,4]' => 'Kamu hanya dapat memilih antara Ketua, Top Level, Kabid, atau Anggota!'
            ]
        ],
        'pmpsks_type' => [
            'label' => 'Aktif',
            'rules' => 'in_list[Aktif,Nonaktif]|permit_empty',
            'errors' => [
                'in_list[Aktif,Nonaktif]' => ['Kamu Hanya Dapat Memilih Opsi Aktif/Nonaktif']
            ]
        ],
        'pmpsks_status' => [
            'label' => 'Foto Pengurus',
            'rules' => 'is_image[member_image]|ext_in[member_image,png,jpg,jpeg,webp]|uploaded[member_image]',
        ],

    ];

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
