<?php

namespace App\Models;

use App\Libraries\Model;

/**
 * @package Kartasolv\Models
 */
class PmpsksModel extends Model
{
    protected $table = 'pmpsks_types';
    protected $primaryKey = 'pmpsks_id';
    protected $returnType     = 'object';
}
