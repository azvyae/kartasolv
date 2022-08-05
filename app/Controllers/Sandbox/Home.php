<?php

namespace App\Controllers\Sandbox;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function getIndex()
    {
        echo base_url();
    }
}
