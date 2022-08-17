<?php

namespace App\Controllers\Data;

use App\Controllers\BaseController;
use App\Libraries\ImageUploader;

/**
 * This controller shows PSKS data.
 * 
 * This controller basicly shows messages data with Datatables, this controller also have some
 * procedure to delete and toggle Community Status shown in the Datatables.
 * 
 * @package Controllers\Data
 */
class Psks extends BaseController
{
    /**
     * CommunitiesModel initiator.
     * @var \App\Models\CommunitiesModel $cm
     */
    protected $cm;

    /**
     *  PmpsksImgModel initiator. 
     * @var \App\Models\PmpsksImgModel $pim
     */
    protected $pim;

    /** 
     * PmpsksModel initiator.
     * @var \App\Models\PmpsksModel $pm
     */
    protected $pm;

    /**
     * Constructor provided to prepare every model.
     */
    public function __construct()
    {
        $this->cm = new \App\Models\CommunitiesModel();
        $this->pim = new \App\Models\PmpsksImgModel();
        $this->pm = new \App\Models\PmpsksModel();
    }

    /**
     * Prepare basic view for PSKS table.
     * It can also accept get, put and delete HTTP method.
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void View, Redirection, or AJAX Response.
     */
    public function index()
    {
        if ($this->request->isAJAX()) {
            switch (getMethod()) {
                case 'get':
                    return $this->_datatable();
                    break;
                case 'put':
                    return $this->_updateStatus();
                    break;
                case 'delete':
                    return $this->_delete();
                    break;
            }
        }
        $psks_types = array_map(function ($e) {
            return [
                'value' => $e->pmpsks_name,
                'text' => $e->pmpsks_name,
            ];
        }, $this->pm->select('pmpsks_name')->where('pmpsks_type', 'PSKS')->find());

        $data = [
            'title' => "Data PSKS | Karta Sarijadi",
            'sidebar' => true,
            'psks_types' => json_encode($psks_types)
        ];
        return view('data/psks/index', $data);
    }

    /**
     * PSKS Datatables generator.
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
    private function _datatable()
    {
        if ($referrer = acceptFrom('data/psks')) {
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
        $communities = $this->cm->getPSKSDatatable($condition);
        $data = $ids = [];
        foreach ($communities->result as $field) {
            $community_id = encode($field->community_id, 'psks');
            $ids[] = $community_id;
            $row = [
                'unique_id' => $community_id,
                'community_name' => $field->community_name,
                'community_identifier' => $field->community_identifier ?? '',
                'community_address' => $field->community_address,
                'pmpsks_name' => $field->pmpsks_name,
                'community_status' => $field->community_status,
            ];
            $data[] = $row;
        }

        $output = [
            "draw" => $this->request->getGet('draw'),
            "recordsFiltered" => $communities->totalRows,
            "recordsTotal" => $communities->totalRows,
            "data" => $data,
            "ids" => $ids,
        ];
        echo json_encode($output);
    }

    /**
     * PSKS update read/unread ajax call.
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
    private function _updateStatus()
    {
        if ($referrer = acceptFrom('data/psks')) {
            return redirect()->to($referrer);
        }

        $communityIds = array_map(function ($e) {
            return decode($e, 'psks');
        }, $this->request->getPost('selections'));
        foreach ($communityIds as $id) {
            $data = ['community_id' => $id];
            if ($this->cm->find($id, true)->community_status === 'Disetujui') {
                $data += [
                    'community_status' => 'Belum Disetujui'
                ];
            } else {
                $data += [
                    'community_status' => 'Disetujui'
                ];
            }
            $this->cm->save($data);
        }
        $flash = [
            'message' => count($communityIds) . ' Data PSKS Berhasil Diperbarui',
            'type' => 'success'
        ];
        setFlash($flash);
        $response = [
            // If reload filled with false, it wont reload after ajax request
            'reload' => true
        ];
        echo json_encode($response);
    }

    /**
     * Delete PSKS data ajax call.
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
    private function _delete()
    {
        if ($referrer = acceptFrom('data/psks')) {
            return redirect()->to($referrer);
        }
        $deleteData = $this->request->getPost('selections');
        $totalData = count($deleteData);
        $response = false;
        $data = array_map(function ($e) {
            return decode($e, 'psks');
        }, $deleteData);
        if ($data) {
            if ($this->cm->delete($data)) {
                $this->pim->deleteImages($data);
                $flash = [
                    'message'   => "$totalData data PSKS berhasil dihapus",
                    'type'        => 'success',
                ];
                setFlash($flash);
                $response = $totalData;
            } else {
                $flash = [
                    'message'   => "Data PSKS gagal dihapus",
                    'type'        => 'danger',
                ];
                setFlash($flash);
            }
        }
        echo json_encode($response);
    }

    /**
     * Create form view for creating
     * and updating PSKS data.
     * 
     * @param string $communityId If no parameter provided, this method will show
     * create PSKS data form view, otherwise it will show existing PSKS data.
     * 
     * @throws \CodeIgniter\Exceptions\PageNotFoundException 404 Not Found
     * 
     * @return string|\CodeIgniter\HTTP\RedirectResponse View or Redirection.
     */
    public function crud($communityId = '')
    {
        helper('form');
        switch (getMethod()) {
            case 'post':
                $this->_crud();
                break;
            case 'put':
                $this->_crud($communityId);
                break;
            default:
                break;
        }
        $data = [
            'title' => 'Tambah Data PSKS | Karta Sarijadi',
            'crudType' => 'Tambah Data PSKS'
        ];
        if ($communityId) {
            $id = decode($communityId, 'psks');
            $community = $this->cm->find($id, true);
            if (!$community) {
                return show404();
            }
            $data = [
                'title' => 'Ubah Data PSKS | Karta Sarijadi',
                'crudType' => 'Ubah Data PSKS',
                'community' => $community,
                'communityId' => $communityId,
                'pmpsksImg' => $this->pim->getImages($id)
            ];
        }
        $data += [
            'psksTypes' => $this->pm->select(['pmpsks_id', 'pmpsks_name'])->where('pmpsks_type', 'PSKS')->findAll(),
            'sidebar' => true
        ];
        return view('data/psks/crud', $data);
    }

    /**
     * Create form view for creating
     * PSKS data with Spreadsheet.
     * @return \CodeIgniter\HTTP\RedirectResponse|string View or Redirection.
     */
    public function spreadsheetCrud()
    {
        if (getMethod('post')) {
            if (!$this->validate('spreadsheet')) {
                return redirect()->to('data/psks/tambah-spreadsheet')->withInput();
            }
            $excelFile = $this->request->getFile('file_excel');
            $ext = $excelFile->getClientExtension();
            if ($ext == 'xls') {
                $render = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else {
                $render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            $spreadsheet = $render->load($excelFile);
            $data = $spreadsheet->getActiveSheet()->toArray();
            $totalData = count($data) - 1;
            $successfulInsert = 0;
            foreach ($data as $i => $row) {
                if ($i == 0) {
                    continue;
                }

                $name = trim($row[0]);
                $address = trim($row[1]);
                $identifier = !empty($identifier = trim($row[2])) ? $identifier : null;
                $type = trim($row[3]);
                $status = trim($row[4]);

                if ($this->cm->find($identifier, true)) {
                    continue;
                } else {
                    if (empty($name) || empty($address) || empty($type) || $type < 27 || $type >= 38 || ($status != 'Disetujui' && $status != 'Belum Disetujui')) {
                        continue;
                    }
                    $data = [
                        'community_name' => $name,
                        'community_address' => $address,
                        'community_identifier' => $identifier,
                        'pmpsks_type' => $type,
                        'community_status' => $status
                    ];
                    if ($this->cm->skipValidation(true)->save($data)) {
                        $successfulInsert++;
                    }
                }
            }
            if ($successfulInsert > 0) {
                $flash = [
                    'message' => "Berhasil menambahkan $successfulInsert data dari $totalData data.",
                    'type' => 'success'
                ];
                setFlash($flash);
            } else {
                $flash = [
                    'message' => "Ada kegagalan saat menambahkan data.",
                    'type' => 'danger'
                ];
                setFlash($flash);
            }
            return redirect()->to('data/psks/tambah-spreadsheet');
        }
        $data = [
            'title' => 'Tambah Data Dengan Sheet | Karta Sarijadi',
        ];
        $data += [
            'sidebar' => true
        ];
        return view('data/psks/spreadsheet_crud', $data);
    }

    /**
     * Form validation and procedure
     * to save data with PmpsksModel.
     * 
     * @param string $communityId If no parameter provided, this method will show
     * create PSKS data form view, otherwise it will show existing PSKS data.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection.
     */
    private function _crud($communityId = null)
    {
        if ($referrer = acceptFrom('data/psks/' . ($communityId ?? 'tambah'))) {
            return redirect()->to($referrer);
        }
        $communityStatus = $this->request->getPost('community_status');
        $decodedCommunityID = decode($communityId, 'psks');
        if (!$communityStatus) {
            $communityStatus = 'Belum Disetujui';
        }
        $rules = $this->cm->getValidationRules(['except' => ['pmks_type']]);
        if ($decodedCommunityID) {
            $rules['community_identifier']['rules'] .= '|is_unique[communities.community_identifier,community_identifier,{community_identifier}]';
        } else {
            $rules['community_identifier']['rules'] .= '|is_unique[communities.community_identifier]';
        }
        if (($imgCount = count($img = $this->request->getFileMultiple('pmpsks_img_loc'))) > 0) {
            if ($img[0]->getSize() > 0) {
                $rules += $this->pim->getValidationRules();
            }
        }
        if (!$this->validate($rules)) {
            return redirect()->to('data/psks/' . ($communityId ?? 'tambah'))->withInput();
        }
        /**
         * Base update data
         */
        $data = [
            'community_name' => $this->request->getPost('community_name'),
            'community_address' => $this->request->getPost('community_address'),
            'community_identifier' => !empty($identifier = trim($this->request->getPost('community_identifier'))) ? $identifier : null,
            'pmpsks_type' => $this->request->getPost('psks_type'),
            'community_status' => $communityStatus,
        ];

        if ($decodedCommunityID) {
            $data += [
                'community_id' => $decodedCommunityID
            ];
        }

        /**
         * Image upload handler
         */
        if ($imgCount > 0) {
            if ($img[0]->getSize() > 0) {
                $imageUploader = new ImageUploader;
                $opt = [
                    'upload_path' => 'psks',
                    'name' => 'pmpsks_img_loc',
                    'multi' => true,
                    'private' => true
                ];
                if ($paths = $imageUploader->upload($opt)) {
                    $newImages = array_map(function ($e) {
                        return base_url("gambar-privat?q=$e");
                    }, $paths);
                }
            }
        }

        $result = $communityId === null ? $this->cm->skipValidation(true)->insert($data) : $this->cm->skipValidation(true)->save($data);
        if ($result) {
            if ($newImages ?? false) {
                $imgs = [];
                if (is_int($result)) {
                    foreach ($newImages as $newImagePaths) {
                        array_push($imgs, [
                            'community_id' => $result,
                            'pmpsks_img_loc' => $newImagePaths
                        ]);
                    }
                } else if ($decodedCommunityID) {
                    $this->pim->deleteImages([$decodedCommunityID]);
                    foreach ($newImages as $newImagePaths) {
                        array_push($imgs, [
                            'community_id' => $decodedCommunityID,
                            'pmpsks_img_loc' => $newImagePaths
                        ]);
                    }
                }
                $this->pim->skipValidation(true)->insertBatch($imgs);
            }
            $flash = [
                'message' => 'Data PSKS berhasil diperbarui.',
                'type' => 'success'
            ];
            setFlash($flash);
            return redirect()->to('data/psks/' . ($communityId ?? 'tambah'));
        }
        $flash = [
            'message' => 'Data PSKS gagal diperbarui.',
            'type' => 'danger'
        ];
        setFlash($flash);
        return redirect()->to('data/psks/' . ($communityId ?? 'tambah'))->withInput();
    }

    /**
     * Get PSKS data images ajax call.
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
    public function getImages()
    {
        if ($referrer = acceptFrom('data/pmks')) {
            return redirect()->to($referrer);
        }
        $communityId = decode($this->request->getGet('uuid'), 'psks');
        if (!$this->request->isAJAX() || !$communityId) {
            return redirect()->to('data/psks');
        }
        echo json_encode(array_map(function ($e) {
            return $e->pmpsks_img_loc ?? null;
        }, $this->pim->getImages($communityId)));
    }
}
