<?php

namespace Config;

use Config\Services;

class CustomRules
{
    public function verify_recaptcha($response)
    {
        $request = Services::request();
        $token = $request->getPost('g-recaptcha-response');
        if (getenv('CI_ENVIRONMENT') == 'testing') {
            return $token === 'random-token';
        }
        $client = service('curlrequest');
        if (!$token) {
            return false;
        }
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
        if (($recaptcha->score ?? 0) >= 0.5) {
            return TRUE;
        } else {

            return FALSE;
        }
    }

    public function phone_number($text)
    {
        if ($text[0] == '+' && $text[1] == '6' && $text[2] == '2' && $text[3] == '8') {
            return true;
        }
        if ($text[0] == '6' && $text[1] == '2' && $text[2] == '8') {
            return true;
        }
        if ($text[0] == '0'  && $text[1] == '8') {
            return true;
        }
        return false;
    }
}
