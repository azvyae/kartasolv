<?php

namespace App\Models;

use App\Libraries\Model;

/**
 * @package Kartasolv\Models
 */
class HistoryModel extends Model
{
    protected $table = 'info_history';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'title_a',
        'desc_a',
        'title_b',
        'desc_b',
        'title_c',
        'desc_c',
        'title_d',
        'desc_d',
        'image_a',
        'image_b',
    ];
    protected $afterUpdate = ['setModifiedBy'];
    protected $validationRules = [
        'title_a' => [
            'label' => 'Nama Kegiatan 1',
            'rules' => 'required|max_length[64]|string',
        ],
        'desc_a' => [
            'label' => 'Deskripsi Kegiatan 1',
            'rules' => 'required|max_length[512]|string',
        ],
        'title_b' => [
            'label' => 'Nama Kegiatan 2',
            'rules' => 'required|max_length[64]|string',
        ],
        'desc_b' => [
            'label' => 'Deskripsi Kegiatan 2',
            'rules' => 'required|max_length[512]|string',
        ],
        'title_c' => [
            'label' => 'Nama Kegiatan 3',
            'rules' => 'required|max_length[64]|string',
        ],
        'desc_c' => [
            'label' => 'Deskripsi Kegiatan 3',
            'rules' => 'required|max_length[512]|string',
        ],
        'title_d' => [
            'label' => 'Nama Kegiatan 3',
            'rules' => 'required|max_length[64]|string',
        ],
        'desc_d' => [
            'label' => 'Deskripsi Kegiatan 3',
            'rules' => 'required|max_length[512]|string',
        ],
        'image_a' => [
            'label' => 'Gambar 1',
            'rules' => 'is_image[image_a]|ext_in[image_a,png,jpg,jpeg,webp]|uploaded[image_a]|max_size[image_a,1024]',
            'errors' => [
                'max_size' => 'Maksimal ukuran berkas adalah 1 MB!'
            ]
        ],
        'image_b' => [
            'label' => 'Gambar 2',
            'rules' => 'is_image[image_b]|ext_in[image_b,png,jpg,jpeg,webp]|uploaded[image_b]|max_size[image_b,1024]',
            'errors' => [
                'max_size' => 'Maksimal ukuran berkas adalah 1 MB!'
            ]
        ],

    ];
    protected $returnType     = 'object';
}
