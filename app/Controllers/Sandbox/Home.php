<?php

namespace App\Controllers\Sandbox;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index($string = '')
    {
        helper('form');
        $data = explode('<br />', nl2br($this->request->getPost('data')));
        $data = array_map(function ($e) {
            if (trim($e)) {
                [$mission, $desc] = explode('(', $e);
                if ($mission && $desc) {
                    return trim($mission) . '[' . str_replace(')', ']', $desc);
                }
            }
            return null;
        }, $data);
        $data = array_filter($data);
        $data = implode('<br/>', $data);
        $ale = [];
        d($data);
        if ($data) {
            $ale = [
                'sk' => str_ireplace('<br/>', "\r\n\r\n", str_replace(['[', ']'], [' (', ')'], $data))
            ];
        }
        return view('sandbox/home/index', $ale);
    }

    public function login()
    {
        $client = service('curlrequest');
        for ($i = 0; $i < 200; $i++) {
            $client->request('POST', base_url('masuk'), [
                'form_params' => [
                    'user_email' => 'karangtarunasarijadi@gmail.com',
                    'user_password' => '12345',
                ],
                'verify' => false
            ]);
        }
    }

    public function logout()
    {
        session_destroy();
    }
}
