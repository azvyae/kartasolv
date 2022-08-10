<?php

namespace App\Controllers\Content;

use App\Controllers\BaseController;

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
        if ($this->request->getPost()) {
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
        # validation here
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
            $memberModel = model('App\Models\MembersModel');
            $id = decode($id, 'members');
            $member = $memberModel->getMember($id);
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
