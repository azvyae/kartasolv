<?php

namespace App\Controllers\Content;

use App\Controllers\BaseController;

class OrganizationProfile extends BaseController
{
    public function index()
    {
        echo 'Pengaturan Profil Karang Taruna';
    }
    public function mainInfo()
    {
        echo 'Ubah Informasi Utama';
    }
    public function ourActivities()
    {
        echo 'Ubah Kegiatan Kami';
    }
    public function members()
    {
        echo getFlash('message');
        echo '<br>';
        echo 'Data Pengurus';
    }
    public function memberCrud($id = '')
    {
        $data = [
            'title' => 'Tambah Data Pengurus'
        ];
        if ($id) {
            $memberModel = model('App\Models\MembersModel');
            $id = decode($id, 'members');
            $member = $memberModel->getMember($id);
            if (!$member) {
                show404();
            }
            $data = [
                'title' => 'Ubah Data Pengurus',
                'member' => $member
            ];

            return $this->memberUpdate($id);
        }
        return view('content/organization_profile/member_crud', $data);
    }

    private function memberUpdate($id)
    {
    }

    private function memberCreate()
    {
        echo 'Tambah Data Pengurus';
    }
}
