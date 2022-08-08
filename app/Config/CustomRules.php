<?php

namespace Config;

use Config\Services;

class CustomRules
{
    public function verify_recaptcha()
    {

        $client = service('curlrequest');
        $request = Services::request();
        $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => getenv('recaptcha.secretkey'),
                'response' => $request->getPost('g-recaptcha-response'),
                'remoteip' => $request->getIPAddress()
            ],
        ]);
        // Make and decode POST request:
        $recaptcha = json_decode($response->getBody());
        // Take action based on the score returned:
        if (($recaptcha->score ?? 0) >= 0.5) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
