<?php

namespace App\Controllers\Content;

use App\Controllers\BaseController;
use App\Libraries\ImageUploader;

class OrganizationProfile extends BaseController
{
    protected $lm, $am, $mm;
    public function __construct()
    {
        $this->lm = new \App\Models\LandingModel();
        $this->am = new \App\Models\ActivitiesModel();
        $this->mm = new \App\Models\MembersModel();
    }
    public function index()
    {
        $data = [
            'title' => "Pengaturan Profil Karta | Karta Sarijadi",
            'sidebar' => true,
        ];
        return view('content/organization_profile/index', $data);
    }
    public function mainInfo()
    {
        if (getMethod('put')) {
            return $this->_updateMainInfo();
        }
        $data = [
            'title' => "Ubah Informasi Utama | Karta Sarijadi",
            'sidebar' => true,
            'landing' => $this->lm->find(1, true)
        ];
        return view('content/organization_profile/main_info', $data);
    }
    private function _updateMainInfo()
    {
        $oldData = $this->lm->find(1, true);
        $rules = $this->lm->getValidationRules(['except' => ['landing_image']]);
        if (($img = $this->request->getFile('landing_image'))->getSize() > 0) {
            $rules += $this->lm->getValidationRules();
        }
        if (!$this->validate($rules)) {
            return redirect()->to('konten/profil-karang-taruna/info-utama')->withInput();
        }
        $postData = $this->request->getPost();
        /**
         * Parsing regular textarea string to list of missions
         */
        $postData['mission'] = implode('<br/>', array_filter(
            array_map(function ($e) {
                $e = explode('[', ltrim(trim($e), '-'));
                if ($e[0] && ($e[1] ?? false)) {
                    return trim(preg_replace('/\s+/', ' ', $e[0])) . '[' . trim(preg_replace('/\s+/', ' ', $e[1])) . ']';
                }
            }, explode(']', $postData['mission']))
        ));

        /**
         * Base update data
         */
        $updateData = [
            'id' => 1,
            'landing_title' => $postData['landing_title'],
            'landing_tagline' => $postData['landing_tagline'],
            'vision' => $postData['vision'],
            'mission' => $postData['mission']
        ];

        /**
         * Image upload handler
         */
        $savedImageName = explode('/', $this->lm->find(1, true)->landing_image);
        $savedImageName = end($savedImageName);
        if ($img->getSize() > 0) {
            $imageUploader = new ImageUploader;
            $opt = [
                'upload_path' => 'organization-profile',
                'max_size' => 300,
                'name' => 'landing_image',
            ];
            if ($path = $imageUploader->upload($opt)) {
                $updateData += [
                    'landing_image' => base_url($path)
                ];
            }
            $savedImagePath = ROOTPATH . 'public_html/uploads/' . $opt['upload_path'] . "/$savedImageName";
        }

        /**
         * Call to action update data
         */
        if ($postData['cta_text']) {
            $updateData += [
                'cta_text' => $postData['cta_text'],
                'cta_url' => addProtocol($postData['cta_url']),
            ];
        }

        if ($this->lm->skipValidation(true)->save($updateData)) {
            $flash = [
                'message' => 'Info utama berhasil diperbarui.',
                'type' => 'success'
            ];
            setFlash($flash);
            if ($savedImagePath ?? false) {
                if (file_exists($savedImagePath)) {
                    unlink($savedImagePath);
                }
            }
            return redirect()->to('konten/profil-karang-taruna/info-utama');
        }
        $flash = [
            'message' => 'Info utama gagal diperbarui.',
            'type' => 'danger'
        ];
        setFlash($flash);
        return redirect()->to('konten/profil-karang-taruna/info-utama')->withInput();
    }
    public function ourActivities()
    {
        if ($this->request->getPost()) {
            return $this->_updateOurActivities();
        }
        $data = [
            'title' => "Ubah Kegiatan Kami | Karta Sarijadi",
            'sidebar' => true,
        ];
        return view('content/organization_profile/our_activities', $data);
    }

    private function _updateOurActivities()
    {
        # validation here
    }

    public function members()
    {
        $data = [
            'title' => "Data Pengurus | Karta Sarijadi",
            'sidebar' => true,
        ];
        return view('content/organization_profile/members', $data);
    }

    public function memberCrud($id = '')
    {
        $data = [
            'title' => 'Tambah Data Pengurus'
        ];
        $this->request->getPost();
        if ($id) {
            $mm = new \App\Models\MembersModel();
            $id = decode($id, 'members');
            $member = $mm->find($id, true);
            if (!$member) {
                return show404();
            }
            $data = [
                'title' => 'Ubah Data Pengurus',
                'member' => $member
            ];
        }
        return view('content/organization_profile/member_crud', $data);
    }

    private function _memberCrud()
    {
        # validation here based on request (post or put)
    }

    private function _member()
    {
        # code...
    }
}
