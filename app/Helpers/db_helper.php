<?php

use Config\Database;

function countTable($table)
{
    return Database::connect()->table($table)->countAllResults();
}
