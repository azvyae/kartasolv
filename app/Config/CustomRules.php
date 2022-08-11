<?php

namespace Config;

use Config\Services;

class CustomRules
{
    public function verify_recaptcha($response)
    {

        $client = service('curlrequest');
        $request = Services::request();
        $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => getenv('recaptcha.secretkey'),
                'response' => $response,
                'remoteip' => $request->getIPAddress()
            ],
        ]);
        // Make and decode POST request:
        $recaptcha = json_decode($response->getBody());
        // Take action based on the score returned:
        // dd($recaptcha);
        if (($recaptcha->score ?? 0) >= 0.5) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
