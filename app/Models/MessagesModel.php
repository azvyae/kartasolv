<?php

namespace App\Models;

use App\Libraries\DatabaseManager;
use App\Libraries\Model;

/**
 * Table Messages model.
 * @see https://codeigniter.com/user_guide/models/model.html for instructions.
 * @package KartasolvApp\Models
 */
class MessagesModel extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'message_id';
    protected $useTimestamps = true;
    protected $allowedFields = ['message_sender', 'message_whatsapp', 'message_type', 'message_text', 'message_status'];
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $beforeUpdate = ['setModifiedBy'];
    protected $afterDelete = ['setDeletedBy'];
    protected $validationRules = [
        'message_sender' => [
            'label' => 'Nama',
            'rules' => 'required|max_length[64]|alpha_numeric_space',
        ],
        'message_whatsapp' => [
            'label' => 'No Whatsapp',
            'rules' => 'required|max_length[32]|numeric|phone_number',
            'errors' => [
                'phone_number' => 'Nomor Whatsapp Salah!'
            ]
        ],
        'message_type' => [
            'label' => 'Jenis Pesan',
            'rules' => 'required|in_list[Kritik & Saran,Laporan/Aduan]',
            'errors' => [
                'in_list' => 'Ada Kesalahan Pada Jenis Pesan'
            ]
        ],
        'message_text' => [
            'label' => 'Pesan',
            'rules' => 'required|max_length[280]|string',

        ]
    ];

    /**
     * Retrieve Datatables.
     * @see \App\Libraries\DatabaseManager for complete instructions.
     * @param mixed $condition Condition retrieved from the controller.
     * @return object Objectified result.
     */
    public function getDatatable($condition)
    {
        $dbMan = new DatabaseManager;
        $query = [
            'result' => 'result',
            'table'  => 'messages',
            'select' => ['message_id', 'message_sender', 'message_whatsapp', 'message_type', 'message_text', 'message_status', 'created_at'],
        ];

        $query += $dbMan->filterDatatables($condition);
        $query['orderBy'] .= ', message_id ASC';
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
