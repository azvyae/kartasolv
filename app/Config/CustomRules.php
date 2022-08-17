<?php

namespace Config;

use Config\Services;

/**
 * CustomRules is a Class that provides custom form validation rules for this app.
 * 
 * Only two custom rules provided for this application.
 * 
 * @author Azvya Erstevan I
 * 
 * @package Kartasolv\Config
 * 
 */
class CustomRules
{
    /**
     * Verify Recaptcha is a basic function for sending and verifying Recaptcha V3 token to Google.
     * 
     * @see https://developers.google.com/recaptcha/docs/v3
     * 
     * @param mixed $response Is request sent as recaptcha token to the system after post/put/delete request.
     * @return bool Validation Response.
     */
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

    /**
     * Simple validation for correct phone number format. (Only accept Indonesian Country Code).
     * 
     * 
     * @param string $text User input from Whatsapp User Input form.
     * @return bool Validation Response.
     */
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
