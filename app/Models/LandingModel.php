<?php

namespace App\Models;

use CodeIgniter\Model;

class LandingModel extends Model
{
    protected $table = 'info_landing';
    protected $primaryKey = 'id';
    protected $useTimestamps = 'true';
    protected $allowedFields = ['landing_title', 'landing_tagline', 'cta_text', 'cta_url', 'vision', 'landing_image', 'mission'];
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;

    public function getCallToAction()
    {
        return $this->select(['cta_text', 'cta_url'])->find(1);
    }
    public function getLandingInfo()
    {
        return $this->where('id', 1, true)->get()->getRow();
    }
}
