<?php

namespace App\Models;

use App\Libraries\Model;

class ActivitiesModel extends Model
{
    protected $table = 'info_activities';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'title_a',
        'desc_a',
        'image_a',
        'title_b',
        'desc_b',
        'image_b',
        'title_c',
        'desc_c',
        'image_c',
    ];
    protected $returnType     = 'object';
    protected $beforeUpdate = ['setModifiedBy'];
    protected $validationRules = [
        'title_a' => [
            'label' => 'Nama Kegiatan 1',
            'rules' => 'required|max_length[64]|string',
        ],
        'desc_a' => [
            'label' => 'Deskripsi Kegiatan 1',
            'rules' => 'required|max_length[512]|string',
        ],
        'image_a' => [
            'label' => 'Gambar Kegiatan 1',
            'rules' => 'is_image[image_a]|ext_in[image_a,png,jpg,jpeg,webp]|uploaded[image_a]',
        ],
        'title_b' => [
            'label' => 'Nama Kegiatan 2',
            'rules' => 'required|max_length[64]|string',
        ],
        'desc_b' => [
            'label' => 'Deskripsi Kegiatan 2',
            'rules' => 'required|max_length[512]|string',
        ],
        'image_b' => [
            'label' => 'Gambar Kegiatan 2',
            'rules' => 'is_image[image_b]|ext_in[image_b,png,jpg,jpeg,webp]|uploaded[image_b]',
        ],
        'title_c' => [
            'label' => 'Nama Kegiatan 3',
            'rules' => 'required|max_length[64]|string',
        ],
        'desc_c' => [
            'label' => 'Deskripsi Kegiatan 3',
            'rules' => 'required|max_length[512]|string',
        ],
        'image_c' => [
            'label' => 'Gambar Kegiatan 3',
            'rules' => 'is_image[image_c]|ext_in[image_c,png,jpg,jpeg,webp]|uploaded[image_c]',
        ],

    ];
}
