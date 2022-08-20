<?php

namespace App\Models;

use App\Libraries\Model;

/**
 * Table Pmpsks_img model.
 * @see https://codeigniter.com/user_guide/models/model.html for instructions.
 * @package KartasolvApp\Models
 */
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

    /**
     * Retrieve PMPSKS image based on Community Id.
     * 
     * @param int|string $communityId Community_id field on table.
     * @return mixed Retrieved data.
     */
    public function getImages($communityId)
    {
        return $this->select('pmpsks_img_loc')->where('community_id', $communityId)->findAll();
    }

    /**
     * Delete image method and unlink/delete from directory function.
     * 
     * @param array $communityIds Community_id to be deleted.
     * @return bool|string Return if data is deleted or not.
     */
    public function deleteImages(array $communityIds)
    {
        foreach ($communityIds as $ci) {
            foreach ($this->getImages($ci) as $e) {
                $img = explode(base_url('gambar-privat?q='), $e->pmpsks_img_loc);
                $name = str_replace('%2F', '/', end($img));
                $path = WRITEPATH . $name;
                if (file_exists($path)) {
                    // @codeCoverageIgnoreStart
                    unlink($path);
                    // @codeCoverageIgnoreEnd
                }
            }
            $this->builder();
        }
        foreach ($communityIds as $communityId) {
            $this->builder->orWhere('community_id', $communityId);
        }
        return $this->builder->delete();
    }
}
