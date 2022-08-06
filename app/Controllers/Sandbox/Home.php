<?php

namespace App\Controllers\Sandbox;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index($string = '')
    {
        $reset = date('Y-m-d H:i:s');
        $numeric = strtotime($reset);
        echo "$reset is $numeric<br>";
        $encoded = encode($numeric, 'test');
        echo "Encoded: $encoded<br>";
        $decoded = decode($encoded, 'test');
        echo "Decoded: $decoded";
    }
}
