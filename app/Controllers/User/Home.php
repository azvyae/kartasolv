<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {
        $roleName = checkAuth('roleName');
        $data = [
            'title' => "Dasbor $roleName | Karta Sarijadi",
            'sidebar' => true,
        ];
        return view('user/home/index', $data);
    }

    public function loadImage()
    {
        $imagePath = str_replace('..', '', $this->request->getGet('q'));
        $filename = basename($imagePath);
        $file_extension = strtolower(substr(strrchr($filename, "."), 1));

        switch ($file_extension) {
            case "gif":
                $ctype = "image/gif";
                break;
            case "png":
                $ctype = "image/png";
                break;
            case "webp":
                $ctype = "image/webp";
                break;
            case "jpeg":
            case "jpg":
                $ctype = "image/jpeg";
                break;
            case "svg":
                $ctype = "image/svg+xml";
                break;
            default:
        }

        if (file_exists(WRITEPATH . $imagePath)) {
            $this->response->setHeader('Content-Type', $ctype);
            $this->response->setCache([
                'max-age' => '7200',
                'must-revalidate',
                'private',
            ]);
            return readfile(WRITEPATH . $imagePath);
        }
        $this->response->setStatusCode(404);
        return show404();
    }
}
