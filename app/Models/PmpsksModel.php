<?php

namespace App\Models;

use App\Libraries\Model;

/**
 * Table Pmpsks_types model.
 * @see https://codeigniter.com/user_guide/models/model.html for instructions.
 * @package KartasolvApp\Models
 */
class PmpsksModel extends Model
{
    protected $table = 'pmpsks_types';
    protected $primaryKey = 'pmpsks_id';
    protected $returnType     = 'object';
}
