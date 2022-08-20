<?php

namespace App\Controllers\Content;

use App\Controllers\BaseController;
use App\Libraries\ImageUploader;

/**
 * This controller provides content management controller for displaying contents in host/sejarah route.
 * 
 * This controller only include form with its form validation.
 * 
 * @package KartasolvApp\Controllers\Content
 * 
 */
class History extends BaseController
{
    /**
     * HistoryModel initiator.
     * @var \App\Models\HistoryModel 
     */
    protected $hm;

    /**
     * Prepare HistoryModel.
     */
    public function __construct()
    {
        $this->hm = new \App\Models\HistoryModel();
    }

    /**
     * Create form view of History Content.
     * 
     *  @return string|\CodeIgniter\HTTP\RedirectResponse View or Redirection.
     */
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

    /** 
     * Crud form validation for History Content.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection.
     */
    private function _updateHistory()
    {
        // @codeCoverageIgnoreStart
        if ($referrer = acceptFrom('konten/sejarah')) {
            return redirect()->to($referrer);
        }
        // @codeCoverageIgnoreEnd
        $rules = $this->hm->getValidationRules(['except' => ['image_a', 'image_b']]);
        $postData = $this->request->getPost();
        // @codeCoverageIgnoreStart
        $images = $this->request->getFiles();
        foreach ($images as $field => $img) {
            if ($img->getSize() > 0) {
                $rules += $this->hm->getValidationRules(['only' => [$field]]);
            } else {
                unset($images[$field]);
            }
        }
        // @codeCoverageIgnoreEnd
        if (!$this->validate($rules)) {
            return redirect()->to('konten/sejarah')->withInput();
        }

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

        // @codeCoverageIgnoreStart
        $savedImagePaths = [];
        foreach ($images as $field => $img) {
            if ($img->getSize() > 0) {
                $imageUploader = new ImageUploader;
                $opt = [
                    'upload_path' => 'history',
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
        // @codeCoverageIgnoreEnd

        if ($this->hm->skipValidation(true)->save($updateData)) {
            $flash = [
                'message' => 'Info Sejarah berhasil diperbarui.',
                'type' => 'success'
            ];
            setFlash($flash);
            // @codeCoverageIgnoreStart
            foreach ($savedImagePaths as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            // @codeCoverageIgnoreEnd
            return redirect()->to('konten/sejarah');
        }
        // @codeCoverageIgnoreStart
        $flash = [
            'message' => 'Info Sejarah gagal diperbarui.',
            'type' => 'danger'
        ];
        setFlash($flash);
        return redirect()->to('konten/sejarah')->withInput();
        // @codeCoverageIgnoreEnd
    }
}
