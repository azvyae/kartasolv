<?php

namespace App\Controllers\Content;

use App\Controllers\BaseController;
use App\Libraries\ImageUploader;

/**
 * This controller provides content management controller for displaying contents in host route.
 * 
 * This controller has some methods including index page, *mainInfo* page that provides main information
 * on the landing page, updating main info, *ourActivities* methods are for providing information about
 * activities info section in the landing page, and lastly, this controller provides information about
 * *members* of the organization.
 * 
 * The *members* method is slightly different than other methods on this controller because it is
 * implemented dynamic *Datatables* with server side data processing. It also provides create update and
 * delete functions.
 * 
 * @package Controllers\Content
 */
class OrganizationProfile extends BaseController
{
    /**
     * LandingModel initiator 
     * @var \App\Models\LandingModel $lm
     */
    protected $lm;

    /** 
     * ActivitiesModel initiator
     * @var \App\Models\ActivitiesModel $am 
     */
    protected $am;

    /** MembersModel initiator
     * @var \App\Models\MembersModel $mm 
     */
    protected $mm;

    /**
     * Prepare LandingModel, ActivitiesModel, MembersModel for every
     * method available for simplicity.
     */
    public function __construct()
    {
        $this->lm = new \App\Models\LandingModel();
        $this->am = new \App\Models\ActivitiesModel();
        $this->mm = new \App\Models\MembersModel();
    }

    /**
     * Create view for index page navigation for
     * changing landing information.
     * 
     * @return string View.
     */
    public function index()
    {
        $data = [
            'title' => "Pengaturan Profil Karta | Karta Sarijadi",
            'sidebar' => true,
        ];
        return view('content/organization_profile/index', $data);
    }

    /**
     * Create view form for mainInfo.
     * 
     * @return string|\CodeIgniter\HTTP\RedirectResponse View or Redirection.
     */
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

    /**
     * Form validation and save data by model for mainInfo.
     * It also have string parser procedure when retrieving mission input.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection.
     */
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

        $postData['mission'] = implode('\n', array_filter(
            array_map(function ($e) {
                $e = explode('[', ltrim(trim($e), '-'));
                if ($e[0] && ($e[1] ?? false)) {
                    return trim(preg_replace('/\s+/', ' ', $e[0])) . '[' . trim(preg_replace('/\s+/', ' ', $e[1])) . ']';
                }
            }, explode(']', $postData['mission']))
        ));

        $updateData = [
            'id' => 1,
            'landing_title' => $postData['landing_title'],
            'landing_tagline' => $postData['landing_tagline'],
            'vision' => $postData['vision'],
            'mission' => $postData['mission']
        ];

        $savedImagePath = '';
        if ($img->getSize() > 0) {
            $imageUploader = new ImageUploader;
            $opt = [
                'upload_path' => 'organization-profile',
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

    /**
     * Create view form for ourActivities.
     * 
     * @return string|\CodeIgniter\HTTP\RedirectResponse View or Redirection.
     */
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

    /**
     * Form validation for ourActivities.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection.
     */
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

        $updateData = [
            'id' => 1,
            'title_a' => $postData['title_a'],
            'desc_a' => $postData['desc_a'],
            'title_b' => $postData['title_b'],
            'desc_b' => $postData['desc_b'],
            'title_c' => $postData['title_c'],
            'desc_c' => $postData['desc_c'],
        ];

        $savedImagePaths = [];
        foreach ($images as $field => $img) {
            if ($img->getSize() > 0) {
                $imageUploader = new ImageUploader;
                $opt = [
                    'upload_path' => 'activities',
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

    /**
     * Prepare basic view for members table before retrieve its data from datatables.
     * 
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void View, Redirection, or AJAX Response.
     */
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

    /**
     * Members Datatables generator.
     * 
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
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

    /**
     * Members delete ajax call.
     * 
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
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

    /**
     * Create form view for creating and updating member data.
     * 
     * @param string $memberId If no parameter provided, this method will show
     * create member data form view, otherwise it will show existing member data.
     * 
     * @throws \CodeIgniter\Exceptions\PageNotFoundException 404 Not Found
     * 
     * @return string|\CodeIgniter\HTTP\RedirectResponse View or Redirection
     */
    public function memberCrud($memberId = '')
    {
        helper('form');
        switch (getMethod()) {
            case 'post':
                return $this->_memberCrud();
                break;
            case 'put':
                return $this->_memberCrud($memberId);
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

    /**
     * Form validation and save data handler for members.
     * 
     * @param string $memberId If no parameter provided, this method will show
     * create member data form view, otherwise it will show existing member data.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection.
     */
    private function _memberCrud($memberId = '')
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

        $data = [
            'member_name' => $this->request->getPost('member_name'),
            'member_position' => $this->request->getPost('member_position'),
            'member_type' => $this->request->getPost('member_type'),
            'member_active' => $memberActive
        ];

        $savedImagePath = '';
        if ($img->getSize() > 0) {
            $imageUploader = new ImageUploader;
            $opt = [
                'upload_path' => 'members',
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
