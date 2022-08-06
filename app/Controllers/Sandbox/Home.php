<?php

namespace App\Controllers\Sandbox;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index($string = '')
    {
        echo APPPATH . 'Helpers/security_helper.php';
        echo '<br>';
    }
}
