<?php

namespace App\Controllers\Data;

use App\Controllers\BaseController;
use App\Libraries\ImageUploader;
use CodeIgniter\HTTP\URI;
use Config\Database;

/**
 * This controller shows PMKS/PSKS data.
 * 
 * This controller basicly shows messages data with Datatables, this controller also have some
 * procedure to delete and toggle Community Status shown in the Datatables.
 * 
 * @package KartasolvApp\Controllers\Data
 */
class Pmpsks extends BaseController
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
     * Constructor provided to prepare every model.
     */


    protected $uri;
    public function __construct()
    {
        $this->uri = new URI(current_url());
        $this->uri = $this->uri->getSegment(3);
        $this->cm = new \App\Models\CommunitiesModel();
        $this->pim = new \App\Models\PmpsksImgModel();
    }

    /**
     * Prepare basic view for PMKS/PSKS table.
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
                default:
                    // @codeCoverageIgnoreStart
                    break;
                    // @codeCoverageIgnoreEnd
            }
        }
        $type = strtoupper($this->uri);
        $func = $this->uri . '_types';
        ${$func} = array_map(function ($e) {
            return [
                'value' => $e->pmpsks_name,
                'text' => $e->pmpsks_name,
            ];
        }, Database::connect()->table('pmpsks_types')->select('pmpsks_name')->where('pmpsks_type', $type)->get()->getResult());
        $data = [
            'title' => "Data $type | Karta Sarijadi",
            $func => json_encode(${$func}),
            'sidebar' => true,
        ];
        return view('data/' . $this->uri . '/index', $data);
    }

    /**
     * PMKS/PSKS Datatables generator.
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
    private function _datatable()
    {
        // @codeCoverageIgnoreStart
        if ($referrer = acceptFrom('data/' . $this->uri)) {
            return redirect()->to($referrer);
        }
        // @codeCoverageIgnoreEnd
        $condition = [
            'limit' => $this->request->getGet('length'),
            'offset' => $this->request->getGet('start'),
            'filter' => $this->request->getGet('searchBuilder'),
            'order' => $this->request->getGet('order')[0] ?? '',
            'search' => $this->request->getGet('search')['value'] ?? '',
            'columnSearch' => $this->request->getGet('searchable'),
            "orderable" => $this->request->getGet('orderable')
        ];
        $func = "get" . strtoupper($this->uri) . "Datatable";
        $communities = $this->cm->$func($condition);
        $data = $ids = [];
        foreach ($communities->result as $field) {
            $community_id = encode($field->community_id, $this->uri);
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
     * PMKS/PSKS update read/unread ajax call.
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
    private function _updateStatus()
    {
        // @codeCoverageIgnoreStart
        if ($referrer = acceptFrom('data/' . $this->uri)) {
            return redirect()->to($referrer);
        }
        // @codeCoverageIgnoreEnd

        $communityIds = array_map(function ($e) {
            return decode($e, $this->uri);
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
            'message' => count($communityIds) . ' Data ' . strtoupper($this->uri) . ' Berhasil Diperbarui',
            'type' => 'success'
        ];
        setFlash($flash);
        $response = [
            'reload' => true
        ];
        echo json_encode($response);
    }

    /**
     * Delete PMKS/PSKS data ajax call.
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
    private function _delete()
    {
        // @codeCoverageIgnoreStart
        if ($referrer = acceptFrom('data/' . $this->uri)) {
            return redirect()->to($referrer);
        }
        // @codeCoverageIgnoreEnd
        $deleteData = $this->request->getPost('selections');
        $totalData = count($deleteData);
        $response = false;
        $data = array_map(function ($e) {
            return decode($e, $this->uri);
        }, $deleteData);
        if ($data) {
            if ($this->cm->delete($data)) {
                $this->pim->deleteImages($data);
                $flash = [
                    'message'   => "$totalData data " . strtoupper($this->uri) . " berhasil dihapus",
                    'type'        => 'success',
                ];
                setFlash($flash);
                $response = $totalData;
            } else {
                // @codeCoverageIgnoreStart
                $flash = [
                    'message'   => "Data " . strtoupper($this->uri) . " gagal dihapus",
                    'type'        => 'danger',
                ];
                setFlash($flash);
                // @codeCoverageIgnoreEnd
            }
        }
        echo json_encode($response);
    }

    /**
     * Create form view for creating
     * and updating PMKS/PSKS data.
     * 
     * @param string $communityId If no parameter provided, this method will show
     * create PMKS/PSKS data form view, otherwise it will show existing PMKS/PSKS data.
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
                return $this->_crud();
                break;
            case 'put':
                return $this->_crud($communityId);
                break;
            default:
                break;
        }
        $data = [
            'title' => 'Tambah Data ' . strtoupper($this->uri) . ' | Karta Sarijadi',
            'crudType' => 'Tambah Data ' . strtoupper($this->uri)
        ];
        if ($communityId) {
            $id = decode($communityId, $this->uri);
            $community = $this->cm->find($id, true);
            if (!$community) {
                return show404();
            }
            $data = [
                'title' => 'Ubah Data ' . strtoupper($this->uri) . ' | Karta Sarijadi',
                'crudType' => 'Ubah Data ' . strtoupper($this->uri),
                'community' => $community,
                'communityId' => $communityId,
                'pmpsksImg' => $this->pim->getImages($id)
            ];
        }
        $data += [
            $this->uri . 'Types' => Database::connect()->table('pmpsks_types')->select(['pmpsks_id', 'pmpsks_name'])->where('pmpsks_type', strtoupper($this->uri))->get()->getResult(),
            'sidebar' => true
        ];
        return view('data/' . $this->uri . '/crud', $data);
    }

    /**
     * Create form view for creating
     * PMKS/PSKS data with Spreadsheet.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|string View or Redirection.
     */
    public function spreadsheetCrud()
    {
        // @codeCoverageIgnoreStart
        if (getMethod('post')) {
            if ($referrer = acceptFrom("data/" . $this->uri . "/tambah-spreadsheet")) {
                return redirect()->to($referrer);
            }
            if (!$this->validate('spreadsheet')) {
                return redirect()->to("data/" . $this->uri . "/tambah-spreadsheet")->withInput();
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
            return redirect()->to('data/' . $this->uri . '/tambah-spreadsheet');
        }
        // @codeCoverageIgnoreEnd
        $data = [
            'title' => 'Tambah Data Dengan Sheet | Karta Sarijadi',
        ];
        $data += [
            'sidebar' => true
        ];
        return view('data/' . $this->uri . '/spreadsheet_crud', $data);
    }

    /**
     * Form validation and procedure
     * to save data with PmpsksModel.
     * 
     * @param string $communityId If no parameter provided, this method will show
     * create PMKS/PSKS data form view, otherwise it will show existing PMKS/PSKS data.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirection.
     */
    private function _crud($communityId = '')
    {
        // @codeCoverageIgnoreStart
        if ($referrer = acceptFrom("data/" . $this->uri . "/$communityId", "data/" . $this->uri . "/tambah")) {
            return redirect()->to($referrer);
        }
        // @codeCoverageIgnoreEnd
        $communityStatus = $this->request->getPost('community_status');
        $decodedCommunityID = decode($communityId, $this->uri);
        if (!$communityStatus) {
            $communityStatus = 'Belum Disetujui';
        }
        if ($this->uri == 'psks') {
            $type  = 'pmks_type';
        } else {
            $type  = 'psks_type';
        }
        $rules = $this->cm->getValidationRules(['except' => [$type]]);
        if ($decodedCommunityID) {
            $rules['community_identifier']['rules'] .= '|is_unique[communities.community_identifier,community_identifier,{community_identifier}]';
        } else {
            $rules['community_identifier']['rules'] .= '|is_unique[communities.community_identifier]';
        }
        // @codeCoverageIgnoreStart
        if (($img = $this->request->getFileMultiple('pmpsks_img_loc')) && ($imgCount = count($img)) > 0) {
            if ($img[0]->getSize() > 0) {
                $rules += $this->pim->getValidationRules();
            }
        }
        // @codeCoverageIgnoreEnd
        if (!$this->validate($rules)) {
            return redirect()->to('data/' . $this->uri . '/' . (!empty($communityId) ? $communityId : 'tambah'))->withInput();
        }
        /**
         * Base update data
         */
        $data = [
            'community_name' => $this->request->getPost('community_name'),
            'community_address' => $this->request->getPost('community_address'),
            'community_identifier' => !empty($identifier = trim($this->request->getPost('community_identifier'))) ? $identifier : null,
            'pmpsks_type' => $this->request->getPost($this->uri . '_type'),
            'community_status' => $communityStatus,
        ];

        if ($decodedCommunityID) {
            $data += [
                'community_id' => $decodedCommunityID
            ];
        }

        // @codeCoverageIgnoreStart
        if ($img && $imgCount > 0) {
            if ($img[0]->getSize() > 0) {
                $imageUploader = new ImageUploader;
                $opt = [
                    'upload_path' => $this->uri,
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
        // @codeCoverageIgnoreEnd
        $result = empty($communityId) ? $this->cm->skipValidation(true)->insert($data) : $this->cm->skipValidation(true)->save($data);
        if ($result) {
            // @codeCoverageIgnoreStart
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
            // @codeCoverageIgnoreEnd
            $flash = [
                'message' => 'Data ' . strtoupper($this->uri) . ' berhasil diperbarui.',
                'type' => 'success'
            ];
            setFlash($flash);
            return redirect()->to('data/' . $this->uri . '/' . (!empty($communityId) ? $communityId : 'tambah'));
        }
        // @codeCoverageIgnoreStart
        $flash = [
            'message' => 'Data ' . strtoupper($this->uri) . ' gagal diperbarui.',
            'type' => 'danger'
        ];
        setFlash($flash);
        return redirect()->to('data/' . $this->uri . '/' . (!empty($communityId) ? $communityId : 'tambah'))->withInput();
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get PMKS/PSKS data images ajax call.
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
    public function getImages()
    {
        // @codeCoverageIgnoreStart
        if ($referrer = acceptFrom('data/' . $this->uri)) {
            return redirect()->to($referrer);
        }
        // @codeCoverageIgnoreEnd
        $communityId = decode($this->request->getGet('uuid'), $this->uri);
        if (!$this->request->isAJAX() || !$communityId) {
            // @codeCoverageIgnoreStart
            return redirect()->to('data/' . $this->uri);
            // @codeCoverageIgnoreEnd
        }
        echo json_encode(array_map(function ($e) {
            return $e->pmpsks_img_loc ?? null;
        }, $this->pim->getImages($communityId)));
    }
}
