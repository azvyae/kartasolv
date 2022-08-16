<?php

use Config\Database;

function countTable($table, $param = '')
{
    if ($param) {
        return Database::connect()->table($table)->join('pmpsks_types', 'pmpsks_types.pmpsks_id = communities.pmpsks_type', 'full')->where('pmpsks_types.pmpsks_type', $param)->countAllResults();
    }
    return Database::connect()->table($table)->countAllResults();
}
