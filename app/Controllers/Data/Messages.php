<?php

namespace App\Controllers\Data;

use App\Controllers\BaseController;

/**
 * This controller shows Messages data.
 * 
 * This controller basicly shows messages data with Datatables, this controller also have some
 * procedure to delete and toggle read/unread messages shown in the Datatables.
 * 
 * @package KartasolvApp\Controllers\Data
 */
class Messages extends BaseController
{
    /**  
     * MessagesModel initiator.
     * @var \App\Models\MessagesModel $msm 
     */
    protected $msm;

    /**
     * Prepare MessagesModel.
     */
    public function __construct()
    {
        $this->msm = new \App\Models\MessagesModel();
    }

    /**
     * Prepare basic view for messages.
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

        $data = [
            'title' => "Pesan | Karta Sarijadi",
            'sidebar' => true,
        ];
        return view('data/messages/index', $data);
    }

    /**
     * Messages Datatables generator.
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
    private function _datatable()
    {
        // @codeCoverageIgnoreStart
        if ($referrer = acceptFrom('data/pesan')) {
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
        $messages = $this->msm->getDatatable($condition);
        $data = $ids = [];
        setlocale(LC_ALL, 'IND');
        foreach ($messages->result as $field) {
            $message_id = encode($field->message_id, 'messages');
            $ids[] = $message_id;
            $row = [
                'unique_id' => $message_id,
                'message_sender' => $field->message_sender,
                'message_whatsapp' => $field->message_whatsapp,
                'message_type' => $field->message_type,
                'message_text' => $field->message_text,
                'message_status' => $field->message_status,
                'created_at' => date('D, j M y - H:i', strtotime($field->created_at)),
            ];
            $data[] = $row;
        }

        $output = [
            "draw" => $this->request->getGet('draw'),
            "recordsFiltered" => $messages->totalRows,
            "recordsTotal" => $messages->totalRows,
            "data" => $data,
            "ids" => $ids,
        ];
        echo json_encode($output);
    }

    /**
     * Messages update read/unread ajax call.
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
    private function _updateStatus()
    {
        // @codeCoverageIgnoreStart
        if ($referrer = acceptFrom('data/pesan')) {
            return redirect()->to($referrer);
        }
        // @codeCoverageIgnoreEnd

        $messageIds = array_map(function ($e) {
            return decode($e, 'messages');
        }, $this->request->getPost('selections'));
        foreach ($messageIds as $id) {
            $data = ['message_id' => $id];
            if ($this->msm->find($id, true)->message_status === 'Dibaca') {
                $data += [
                    'message_status' => 'Belum Dibaca'
                ];
            } else {
                $data += [
                    'message_status' => 'Dibaca'
                ];
            }
            $this->msm->save($data);
        }
        $flash = [
            'message' => count($messageIds) . ' Data Pesan Berhasil Diperbarui',
            'type' => 'success'
        ];
        setFlash($flash);
        $response = [
            'reload' => true
        ];
        echo json_encode($response);
    }

    /**
     * Delete messages ajax call.
     * @return string|\CodeIgniter\HTTP\RedirectResponse|false|void AJAX Response or Redirection.
     */
    private function _delete()
    {
        // @codeCoverageIgnoreStart
        if ($referrer = acceptFrom('data/pesan')) {
            return redirect()->to($referrer);
        }
        // @codeCoverageIgnoreEnd
        $deleteData = $this->request->getPost('selections');
        $totalData = count($deleteData);
        $response = false;
        $data = array_map(function ($e) {
            return decode($e, 'messages');
        }, $deleteData);
        if ($data) {
            if ($this->msm->delete($data)) {
                $flash = [
                    'message'   => "$totalData data Pesan berhasil dihapus",
                    'type'        => 'success',
                ];
                setFlash($flash);
                $response = $totalData;
            } else {
                $flash = [
                    'message'   => "Data Pesan gagal dihapus",
                    'type'        => 'danger',
                ];
                setFlash($flash);
            }
        }
        echo json_encode($response);
    }
}
