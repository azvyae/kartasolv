<?php

namespace App\Controllers\Content;

use App\Controllers\BaseController;
use App\Libraries\ImageUploader;

class History extends BaseController
{
    protected $hm;
    public function __construct()
    {
        $this->hm = new \App\Models\HistoryModel();
    }
    public function index()
    {
        if (getMethod('put')) {
            return $this->_updateHistory();
        }
        $data = [
            'title' => "Ubah Info Sejarah | Karta Sarijadi",
            'sidebar' => true,
            'history' => $this->hm->find(1, true)
        ];
        return view('content/history/index', $data);
    }

    private function _updateHistory()
    {
        $rules = $this->hm->getValidationRules(['except' => ['image_a', 'image_b']]);
        $images = $this->request->getFiles();
        $postData = $this->request->getPost();
        foreach ($images as $field => $img) {
            if ($img->getSize() > 0) {
                $rules += $this->hm->getValidationRules(['only' => [$field]]);
            } else {
                unset($images[$field]);
            }
        }
        if (!$this->validate($rules)) {
            return redirect()->to('konten/sejarah')->withInput();
        }
        /**
         * Base update data
         */
        $updateData = [
            'id' => 1,
            'title_a' => $postData['title_a'],
            'desc_a' => $postData['desc_a'],
            'title_b' => $postData['title_b'],
            'desc_b' => $postData['desc_b'],
            'title_c' => $postData['title_c'],
            'desc_c' => $postData['desc_c'],
            'title_d' => $postData['title_d'],
            'desc_d' => $postData['desc_d'],
        ];

        /**
         * Image upload handler
         */
        $savedImagePaths = [];
        foreach ($images as $field => $img) {
            if ($img->getSize() > 0) {
                $imageUploader = new ImageUploader;
                $opt = [
                    'upload_path' => 'history',
                    'max_size' => 300,
                    'name' => $field,
                ];
                if ($path = $imageUploader->upload($opt)) {
                    $savedImageName = explode('/', $this->hm->find(1, true)->$field);
                    $savedImageName = end($savedImageName);
                    $updateData += [
                        $field => base_url($path)
                    ];
                    $savedImagePath = ROOTPATH . 'public_html/uploads/' . $opt['upload_path'] . "/$savedImageName";
                    array_push($savedImagePaths, $savedImagePath);
                }
            }
        }

        if ($this->hm->skipValidation(true)->save($updateData)) {
            $flash = [
                'message' => 'Info Sejarah berhasil diperbarui.',
                'type' => 'success'
            ];
            setFlash($flash);
            foreach ($savedImagePaths as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            return redirect()->to('konten/sejarah');
        }
        $flash = [
            'message' => 'Info Sejarah gagal diperbarui.',
            'type' => 'danger'
        ];
        setFlash($flash);
        return redirect()->to('konten/sejarah')->withInput();
    }
}
