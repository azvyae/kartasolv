<?php

namespace App\Controllers\Data;

use App\Controllers\BaseController;

class Messages extends BaseController
{
    protected $msm;
    public function __construct()
    {
        $this->msm = new \App\Models\MessagesModel();
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

        $data = [
            'title' => "Pesan | Karta Sarijadi",
            'sidebar' => true,
        ];
        return view('data/messages/index', $data);
    }

    private function _datatable()
    {
        if ($referrer = acceptFrom('data/pesan')) {
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

    private function _updateStatus()
    {
        if ($referrer = acceptFrom('data/pesan')) {
            return redirect()->to($referrer);
        }

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

    private function _delete()
    {
        if ($referrer = acceptFrom('data/pesan')) {
            return redirect()->to($referrer);
        }
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
