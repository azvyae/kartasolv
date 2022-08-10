<?php

namespace App\Controllers\Sandbox;

use App\Controllers\BaseController;
use App\Libraries\ImageUploader;

class Home extends BaseController
{
    public function sandboxOne($string = '')
    {
        if (getMethod('put')) {
            $imageUploader = new ImageUploader;
            $opt = [
                'upload_path' => 'pmks/1',
                'max_size' => 300,
                'name' => 'image_a',
                'multi' => false,
                'private' => true
            ];
            $images = $imageUploader->upload($opt);
        }
        $gbr = objectify([
            'image_a' => base_url('img/default.webp')
        ]);
        $data = [
            'gbr' => $gbr,
            'sidebar' => 1,
            'debug' => $images ?? null
        ];
        return view('sandbox/home/sandbox_one', $data);
    }

    public function sandboxTwo(...$params)
    {
        if (getMethod('put')) {
            $imageUploader = new ImageUploader;
            $opt = [
                'upload_path' => '/contents/',
                'max_size' => 300,
                'name' => 'image_a',
                'multi' => true
            ];

            $images = $imageUploader->upload($opt);
        }
        $gbr = objectify([
            'image_a' => base_url('img/default.webp')
        ]);
        $data = [
            'gbr' => $gbr,
            'sidebar' => 1,
            'debug' => $images ?? null
        ];
        return view('sandbox/home/sandbox_two', $data);
    }


    public function phpinfo()
    {
        phpinfo();
    }
}
