<?php

namespace App\Models;

use App\Libraries\Model;


class LandingModel extends Model
{
    protected $table = 'info_landing';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['landing_title', 'landing_tagline', 'cta_text', 'cta_url', 'vision', 'landing_image', 'mission'];
    protected $returnType     = 'object';
    protected $beforeUpdate = ['setModifiedBy'];
    protected $validationRules = [
        'landing_title' => [
            'label' => 'Judul Utama',
            'rules' => 'required|max_length[64]|string',
        ],
        'landing_tagline' => [
            'label' => 'Tagline',
            'rules' => 'required|max_length[512]|string',
        ],
        'cta_text' => [
            'label' => 'Teks Call to Action',
            'rules' => 'required_with[cta_url]|max_length[16]|alpha_space|permit_empty',
        ],
        'cta_url' => [
            'label' => 'Url Call to Action',
            'rules' => 'required_with[cta_text]|max_length[256]|valid_url|permit_empty',
        ],
        'vision' => [
            'label' => 'Visi',
            'rules' => 'required|max_length[512]|string',
        ],
        'mission' => [
            'label' => 'Misi',
            'rules' => 'required|max_length[10000]|string',
        ],
        'landing_image' => [
            'label' => 'Gambar pada Landing (visi)',
            'rules' => 'is_image[landing_image]|ext_in[landing_image,png,jpg,jpeg,webp]|uploaded[landing_image]|max_size[landing_image,2048]',
        ],

    ];

    public function getCallToAction()
    {
        return $this->select(['cta_text', 'cta_url'])->find(1);
    }
}
