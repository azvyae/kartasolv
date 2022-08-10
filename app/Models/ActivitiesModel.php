<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivitiesModel extends Model
{
    protected $table = 'info_activities';
    protected $primaryKey = 'id';
    protected $useTimestamps = 'true';
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
    protected $useSoftDeletes = true;
}
