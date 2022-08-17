<?php

namespace App\Models;

use App\Libraries\DatabaseManager;
use App\Libraries\Model;


class CommunitiesModel extends Model
{
    protected $table = 'communities';
    protected $primaryKey = 'community_id';
    protected $useTimestamps = true;
    protected $allowedFields = ['community_name', 'community_address', 'community_identifier', 'pmpsks_type', 'community_status'];
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $beforeInsert = ['setCreatedBy'];
    protected $beforeUpdate = ['setModifiedBy'];
    protected $afterDelete = ['setDeletedBy'];
    protected $validationRules = [
        'community_name' => [
            'label' => 'Nama',
            'rules' => 'required|max_length[128]|alpha_numeric_space',
        ],
        'community_address' => [
            'label' => 'Alamat',
            'rules' => 'required|string|max_length[256]',
        ],
        'community_identifier' => [
            'label' => 'NIK/No KK/ID',
            'rules' => 'string',
        ],
        'psks_type' => [
            'label' => 'Jenis PSKS',
            'rules' => 'required|greater_than_equal_to[27]|less_than_equal_to[38]|numeric',
            'errors' => [
                'greater_than_equal_to' => 'Kamu hanya dapat memilih kategori PSKS yang ada',
                'less_than_equal_to' => 'Kamu hanya dapat memilih kategori PSKS yang ada',
            ]
        ],
        'pmks_type' => [
            'label' => 'Jenis PMKS',
            'rules' => 'required|greater_than_equal_to[1]|less_than_equal_to[26]|numeric',
            'errors' => [
                'greater_than_equal_to' => 'Kamu hanya dapat memilih kategori PMKS yang ada',
                'less_than_equal_to' => 'Kamu hanya dapat memilih kategori PMKS yang ada',
            ]
        ],
        'community_status' => [
            'label' => 'Status',
            'rules' => 'in_list[Disetujui,Belum Disetujui]|permit_empty',
        ],

    ];

    public function getPMKSDatatable($condition)
    {
        $dbMan = new DatabaseManager;
        $query = [
            'result' => 'result',
            'table'  => 'communities',
            'select' => ['community_id', 'community_name', 'community_address', 'community_identifier', 'pmpsks_name', 'community_status'],
            'join' => [
                ['pmpsks_types', 'communities.pmpsks_type = pmpsks_id', 'full']
            ],
            'where' => [
                'pmpsks_types.pmpsks_type' => 'PMKS'
            ]
        ];

        $query += $dbMan->filterDatatables($condition);
        $query['orderBy'] .= ', community_id ASC';
        $data = [
            'totalRows' => $dbMan->countAll($query),
            'result' => $dbMan->read($query),
            'searchable' => array_map(function ($e) {
                return $e . ":name";
            }, $condition['columnSearch'])
        ];

        return objectify($data);
    }

    public function getPSKSDatatable($condition)
    {
        $dbMan = new DatabaseManager;
        $query = [
            'result' => 'result',
            'table'  => 'communities',
            'select' => ['community_id', 'community_name', 'community_address', 'community_identifier', 'pmpsks_name', 'community_status'],
            'join' => [
                ['pmpsks_types', 'communities.pmpsks_type = pmpsks_id', 'full']
            ],
            'where' => [
                'pmpsks_types.pmpsks_type' => 'PSKS'
            ]
        ];

        $query += $dbMan->filterDatatables($condition);
        $query['orderBy'] .= ', community_id ASC';
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
