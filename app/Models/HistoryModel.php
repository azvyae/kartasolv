<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoryModel extends Model
{
    protected $table = 'info_history';
    protected $primaryKey = 'id';
    protected $useTimestamps = 'true';
    protected $allowedFields = [
        'title_a',
        'subtitle_a',
        'title_b',
        'subtitle_b',
        'title_c',
        'subtitle_c',
        'title_d',
        'subtitle_d',
        'image_a',
        'image_b',
    ];
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
}
