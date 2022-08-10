<?php

namespace App\Controllers\Sandbox;

use App\Controllers\BaseController;
use App\Libraries\ImageConverter;
use CodeIgniter\Files\File;

class Home extends BaseController
{
    public function sandboxOne($string = '')
    {
        if (getMethod('put')) {
            return $this->_upload();
        }
        $gbr = objectify([
            'image_a' => base_url('img/default.webp')
        ]);
        $data = [
            'gbr' => $gbr,
            'sidebar' => 1,
            'debug' => $this->request->getFile('image_a')
        ];
        return view('sandbox/home/sandbox_one', $data);
    }
    private function _upload()
    {
        $uploadPath = 'img/uploads';
        $imageConverter = new ImageConverter;
        $img = $this->request->getFile('image_a');
        if (!$img->hasMoved()) {
            $unConverted = WRITEPATH . 'uploads' . $img->store('', $img->getRandomName());
            $filepath = $imageConverter->convertToWebp($unConverted, 300);
            unlink($unConverted);
            $file = new File($filepath);
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777);
            };
            $newPath = $file->move($uploadPath, $file->getRandomName());
            $gbr = objectify([
                'image_a' => base_url($uploadPath . '/' . $newPath->getFilename())
            ]);
            $data = [
                'gbr' =>  $gbr,
                'sidebar' => 1,
                'debug' => null
            ];
            return view('sandbox/home/sandbox_one', $data);
        }
        $data = ['debug' => 'The file has already been moved.'];

        return view('upload_form', $data);
    }
    public function sandboxTwo(...$params)
    {
        dd($params);
        $gbr = objectify([
            'image_a' => base_url('img/default.webp')
        ]);
        $data = [
            'gbr' => $gbr,
            'sidebar' => 1
        ];
        return view('sandbox/home/sandbox_two', $data);
    }


    public function phpinfo()
    {
        phpinfo();
    }
}
