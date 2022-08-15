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
        if ($referrer = acceptFrom('konten/profil-karang-taruna/info-utama')) {
            return redirect()->to($referrer);
        }
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
        $postData['mission'] = implode('\n', array_filter(
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
        $savedImagePath = '';
        if ($img->getSize() > 0) {
            $imageUploader = new ImageUploader;
            $opt = [
                'upload_path' => 'organization-profile',
                'max_size' => 300,
                'name' => 'landing_image',
            ];
            if ($path = $imageUploader->upload($opt)) {
                $savedImageName = explode('/', $this->lm->find(1, true)->landing_image);
                $savedImageName = end($savedImageName);
                $updateData += [
                    'landing_image' => base_url($path)
                ];
                $savedImagePath = ROOTPATH . 'public_html/uploads/' . $opt['upload_path'] . "/$savedImageName";
            }
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
            if ($savedImagePath) {
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
        if (getMethod('put')) {

            return $this->_updateOurActivities();
        }
        $data = [
            'title' => "Ubah Kegiatan Kami | Karta Sarijadi",
            'sidebar' => true,
            'activities' => $this->am->find(1, true)
        ];
        return view('content/organization_profile/our_activities', $data);
    }

    private function _updateOurActivities()
    {
        if ($referrer = acceptFrom('konten/profil-karang-taruna/kegiatan-kami')) {
            return redirect()->to($referrer);
        }
        $rules = $this->am->getValidationRules(['except' => ['image_a', 'image_b', 'image_c']]);
        $images = $this->request->getFiles();
        $postData = $this->request->getPost();
        foreach ($images as $field => $img) {
            if ($img->getSize() > 0) {
                $rules += $this->am->getValidationRules(['only' => [$field]]);
            } else {
                unset($images[$field]);
            }
        }
        if (!$this->validate($rules)) {
            return redirect()->to('konten/profil-karang-taruna/kegiatan-kami')->withInput();
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
        ];

        /**
         * Image upload handler
         */
        $savedImagePaths = [];
        foreach ($images as $field => $img) {
            if ($img->getSize() > 0) {
                $imageUploader = new ImageUploader;
                $opt = [
                    'upload_path' => 'activities',
                    'max_size' => 300,
                    'name' => $field,
                ];
                if ($path = $imageUploader->upload($opt)) {
                    $savedImageName = explode('/', $this->am->find(1, true)->$field);
                    $savedImageName = end($savedImageName);
                    $updateData += [
                        $field => base_url($path)
                    ];
                    $savedImagePath = ROOTPATH . 'public_html/uploads/' . $opt['upload_path'] . "/$savedImageName";
                    array_push($savedImagePaths, $savedImagePath);
                }
            }
        }

        if ($this->am->skipValidation(true)->save($updateData)) {
            $flash = [
                'message' => 'Kegiatan berhasil diperbarui.',
                'type' => 'success'
            ];
            setFlash($flash);
            foreach ($savedImagePaths as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            return redirect()->to('konten/profil-karang-taruna/kegiatan-kami');
        }
        $flash = [
            'message' => 'Kegiatan gagal diperbarui.',
            'type' => 'danger'
        ];
        setFlash($flash);
        return redirect()->to('konten/profil-karang-taruna/kegiatan-kami')->withInput();
    }

    public function members()
    {
        if ($this->request->isAJAX()) {

            switch (getMethod()) {
                case 'get':
                    return $this->_membersDatatable();
                    break;
                case 'delete':
                    return $this->_delete();
                    break;
            }
        }
        $data = [
            'title' => "Data Pengurus | Karta Sarijadi",
            'sidebar' => true,
        ];
        return view('content/organization_profile/members', $data);
    }

    private function _membersDatatable()
    {
        if ($referrer = acceptFrom('konten/profil-karang-taruna/pengurus')) {
            return redirect()->to($referrer);
        }
        $condition = [
            'limit' => $this->request->getGet('length'),
            'offset' => $this->request->getGet('start'),
            'filter' => $this->request->getGet('searchBuilder'),
            'order' => $this->request->getGet('order')[0] ?? '',
            'search' => $this->request->getGet('search')['value'] ?? '',
            'columnSearch' => $this->request->getGet('searchable'),
            "orderable" => $this->request->getGet('orderable')
        ];
        $members = $this->mm->getDatatable($condition);
        $data = $ids = [];
        foreach ($members->result as $field) {
            $member_id = encode($field->member_id, 'members');
            $ids[] = $member_id;
            $row = [
                'unique_id' => $member_id,
                'member_name' => $field->member_name,
                'member_image' => $field->member_image,
                'member_position' => $field->member_position,
                'member_type' => $field->member_type,
                'member_active' => $field->member_active,
            ];
            $data[] = $row;
        }

        $output = [
            "draw" => $this->request->getGet('draw'),
            "recordsFiltered" => $members->totalRows,
            "recordsTotal" => $members->totalRows,
            "data" => $data,
            "ids" => $ids,
        ];
        echo json_encode($output);
    }

    private function _delete()
    {
        if ($referrer = acceptFrom('konten/profil-karang-taruna/pengurus')) {
            return redirect()->to($referrer);
        }
        $deleteData = $this->request->getPost('selections');
        $totalData = count($deleteData);
        $response = false;
        $data = array_map(function ($e) {
            return decode($e, 'members');
        }, $deleteData);
        if ($data) {
            if ($this->mm->delete($data)) {
                $flash = [
                    'message'   => "$totalData data pengurus berhasil dihapus",
                    'type'        => 'success',
                ];
                setFlash($flash);
                $response = $totalData;
            } else {
                $flash = [
                    'message'   => "Data pengurus gagal dihapus",
                    'type'        => 'danger',
                ];
                setFlash($flash);
            }
        }
        echo json_encode($response);
    }

    public function memberCrud($memberId = '')
    {
        helper('form');
        switch (getMethod()) {
            case 'post':
                $this->_memberCrud();
                break;
            case 'put':
                $this->_memberCrud($memberId);
                break;
            default:
                break;
        }
        $data = [
            'title' => 'Tambah Data Pengurus | Karta Sarijadi',
            'crudType' => 'Tambah Data Pengurus'
        ];
        if ($memberId) {
            $id = decode($memberId, 'members');
            $member = $this->mm->find($id, true);
            if (!$member) {
                return show404();
            }
            $data = [
                'title' => 'Ubah Data Pengurus | Karta Sarijadi',
                'crudType' => 'Ubah Data Pengurus',
                'member' => $member,
                'memberId' => $memberId
            ];
        }
        $data += [
            'sidebar' => true
        ];
        return view('content/organization_profile/member_crud', $data);
    }

    private function _memberCrud($memberId = null)
    {
        if ($referrer = acceptFrom('konten/profil-karang-taruna/pengurus/' . ($memberId ?? 'tambah'))) {
            return redirect()->to($referrer);
        }
        $memberActive = $this->request->getPost('member_active');
        $decodedMemberId = decode($memberId, 'members');
        if (!$memberActive) {
            $memberActive = 'Nonaktif';
        }
        $rules = $this->mm->getValidationRules(['except' => ['member_image']]);
        if (($img = $this->request->getFile('member_image'))->getSize() > 0) {
            $rules += $this->mm->getValidationRules();
        }
        if (!$this->validate($rules)) {
            return redirect()->to('konten/profil-karang-taruna/pengurus/' . ($memberId ?? 'tambah'))->withInput();
        }
        /**
         * Base update data
         */
        $data = [
            'member_name' => $this->request->getPost('member_name'),
            'member_position' => $this->request->getPost('member_position'),
            'member_type' => $this->request->getPost('member_type'),
            'member_active' => $memberActive
        ];

        /**
         * Image upload handler
         */
        $savedImagePath = '';
        if ($img->getSize() > 0) {
            $imageUploader = new ImageUploader;
            $opt = [
                'upload_path' => 'members',
                'max_size' => 300,
                'name' => 'member_image',
            ];
            if ($path = $imageUploader->upload($opt)) {
                if ($decodedMemberId) {
                    $savedImageName = explode('/', $this->mm->find($decodedMemberId, true)->member_image);
                    $savedImageName = end($savedImageName);
                    $savedImagePath = ROOTPATH . 'public_html/uploads/' . $opt['upload_path'] . "/$savedImageName";
                }
                $data += [
                    'member_image' => base_url($path)
                ];
            }
        }
        if ($decodedMemberId) {
            $data += [
                'member_id' => $decodedMemberId
            ];
        }
        if ($this->mm->skipValidation(true)->save($data)) {
            $flash = [
                'message' => 'Data Pengurus berhasil diperbarui.',
                'type' => 'success'
            ];
            setFlash($flash);
            if ($savedImagePath) {
                if (file_exists($savedImagePath)) {
                    unlink($savedImagePath);
                }
            }
            return redirect()->to('konten/profil-karang-taruna/pengurus/' . ($memberId ?? 'tambah'));
        }
        $flash = [
            'message' => 'Data Pengurus gagal diperbarui.',
            'type' => 'danger'
        ];
        setFlash($flash);
        return redirect()->to('konten/profil-karang-taruna/pengurus/' . ($memberId ?? 'tambah'))->withInput();
    }
}
