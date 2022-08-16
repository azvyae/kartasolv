<?php

namespace App\Models;

use App\Libraries\Model;


class PmpsksImgModel extends Model
{
    protected $table = 'pmpsks_img';
    protected $primaryKey = 'pmpsks_img_id';
    protected $allowedFields = ['community_id', 'pmpsks_img_loc'];
    protected $returnType     = 'object';
    protected $validationRules = [
        'pmpsks_img_loc' => [
            'label' => 'Foto Data PMKS/PSKS',
            'rules' => 'is_image[pmpsks_img_loc]|ext_in[pmpsks_img_loc,png,jpg,jpeg,webp]|uploaded[pmpsks_img_loc]|max_size[pmpsks_img_loc,1024]',
            'errors' => [
                'max_size' => 'Maksimal ukuran berkas adalah 1 MB!'
            ]
        ],

    ];

    public function getImages($communityId)
    {
        return $this->select('pmpsks_img_loc')->where('community_id', $communityId)->findAll();
    }

    public function deleteImages(array $communityIds)
    {
        foreach ($this->getImages($communityIds) as $e) {
            $img = explode(base_url('gambar-privat?q='), $e->pmpsks_img_loc);
            $name = str_replace('%2F', '/', end($img));
            $path = WRITEPATH . $name;
            if (file_exists($path)) {
                unlink($path);
            }
        }
        $this->builder();
        foreach ($communityIds as $communityId) {
            $this->builder->orWhere('community_id', $communityId);
        }
        return $this->builder->delete();
    }
}
