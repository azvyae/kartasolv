<?php

namespace App\Controllers\Data;

use App\Controllers\BaseController;
use App\Libraries\ImageUploader;

class Pmks extends BaseController
{
    protected $cm, $pim, $pm;
    public function __construct()
    {
        $this->cm = new \App\Models\CommunitiesModel();
        $this->pim = new \App\Models\PmpsksImgModel();
        $this->pm = new \App\Models\PmpsksModel();
    }

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
        $pmks_types = array_map(function ($e) {
            return [
                'value' => $e->pmpsks_name,
                'text' => $e->pmpsks_name,
            ];
        }, $this->pm->select('pmpsks_name')->where('pmpsks_type', 'PMKS')->find());

        $data = [
            'title' => "Data PMKS | Karta Sarijadi",
            'sidebar' => true,
            'pmks_types' => json_encode($pmks_types)
        ];
        return view('data/pmks/index', $data);
    }

    private function _datatable()
    {
        if ($referrer = acceptFrom('data/pmks')) {
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
        $communities = $this->cm->getPMKSDatatable($condition);
        $data = $ids = [];
        foreach ($communities->result as $field) {
            $community_id = encode($field->community_id, 'pmks');
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

    private function _updateStatus()
    {
        if ($referrer = acceptFrom('data/pmks')) {
            return redirect()->to($referrer);
        }

        $communityIds = array_map(function ($e) {
            return decode($e, 'pmks');
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
            'message' => count($communityIds) . ' Data PMKS Berhasil Diperbarui',
            'type' => 'success'
        ];
        setFlash($flash);
        $response = [
            // If reload filled with false, it wont reload after ajax request
            'reload' => true
        ];
        echo json_encode($response);
    }

    private function _delete()
    {
        if ($referrer = acceptFrom('data/pmks')) {
            return redirect()->to($referrer);
        }
        $deleteData = $this->request->getPost('selections');
        $totalData = count($deleteData);
        $response = false;
        $data = array_map(function ($e) {
            return decode($e, 'pmks');
        }, $deleteData);
        if ($data) {
            if ($this->cm->delete($data)) {
                $this->pim->deleteImages($data);
                $flash = [
                    'message'   => "$totalData data PMKS berhasil dihapus",
                    'type'        => 'success',
                ];
                setFlash($flash);
                $response = $totalData;
            } else {
                $flash = [
                    'message'   => "Data PMKS gagal dihapus",
                    'type'        => 'danger',
                ];
                setFlash($flash);
            }
        }
        echo json_encode($response);
    }

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
            'title' => 'Tambah Data PMKS | Karta Sarijadi',
            'crudType' => 'Tambah Data PMKS'
        ];
        if ($communityId) {
            $id = decode($communityId, 'pmks');
            $community = $this->cm->find($id, true);
            if (!$community) {
                return show404();
            }
            $data = [
                'title' => 'Ubah Data PMKS | Karta Sarijadi',
                'crudType' => 'Ubah Data PMKS',
                'community' => $community,
                'communityId' => $communityId,
                'pmpsksImg' => $this->pim->getImages($id)
            ];
        }
        $data += [
            'pmksTypes' => $this->pm->select(['pmpsks_id', 'pmpsks_name'])->where('pmpsks_type', 'PMKS')->findAll(),
            'sidebar' => true
        ];
        return view('data/pmks/crud', $data);
    }

    public function spreadsheetCrud()
    {
        if (getMethod('post')) {
            if (!$this->validate('spreadsheet')) {
                return redirect()->to('data/pmks/tambah-spreadsheet')->withInput();
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
                    if (empty($name) || empty($address) || empty($type) || $type < 1 || $type >= 26 || ($status != 'Disetujui' && $status != 'Belum Disetujui')) {
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
            }
            return redirect()->to('data/pmks/tambah-spreadsheet');
        }
        $data = [
            'title' => 'Tambah Data Dengan Sheet | Karta Sarijadi',
        ];
        $data += [
            'sidebar' => true
        ];
        return view('data/pmks/spreadsheet_crud', $data);
    }

    private function _crud($communityId = null)
    {
        if ($referrer = acceptFrom('data/pmks/' . ($communityId ?? 'tambah'))) {
            return redirect()->to($referrer);
        }
        $communityStatus = $this->request->getPost('community_status');
        $decodedCommunityID = decode($communityId, 'pmks');
        if (!$communityStatus) {
            $communityStatus = 'Belum Disetujui';
        }
        $rules = $this->cm->getValidationRules(['except' => ['psks_type']]);
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
            return redirect()->to('data/pmks/' . ($communityId ?? 'tambah'))->withInput();
        }
        /**
         * Base update data
         */
        $data = [
            'community_name' => $this->request->getPost('community_name'),
            'community_address' => $this->request->getPost('community_address'),
            'community_identifier' => !empty($identifier = trim($this->request->getPost('community_identifier'))) ? $identifier : null,
            'pmpsks_type' => $this->request->getPost('pmks_type'),
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
                    'upload_path' => 'pmks',
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
                    $this->pim->deleteImages($decodedCommunityID);
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
                'message' => 'Data PMKS berhasil diperbarui.',
                'type' => 'success'
            ];
            setFlash($flash);
            return redirect()->to('data/pmks/' . ($communityId ?? 'tambah'));
        }
        $flash = [
            'message' => 'Data PMKS gagal diperbarui.',
            'type' => 'danger'
        ];
        setFlash($flash);
        return redirect()->to('data/pmks/' . ($communityId ?? 'tambah'))->withInput();
    }

    public function getImages()
    {
        $communityId = decode($this->request->getGet('uuid'), 'pmks');
        if (!$this->request->isAJAX() || !$communityId) {
            return redirect()->to('data/pmks');
        }
        echo json_encode(array_map(function ($e) {
            return $e->pmpsks_img_loc ?? null;
        }, $this->pim->getImages($communityId)));
    }
}
