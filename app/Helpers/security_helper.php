<?php

use Config\Services;
use Hashids\Hashids;

/**
 * Main authentication function for Kartasolv application.
 * 
 * @param mixed $data Gather information from session.
 * @return mixed Could be redirection or session data.
 * @package Kartasolv\Helpers\security
 */
function checkAuth($data = null)
{
    $session = session();
    if ($data) {
        return $session->user->$data ?? null;
    } else {
        $menuModel = model('App\Models\MenuModel');
        $router = service('router');
        $controllerName = str_replace("\App\Controllers\\", '', $router->controllerName());
        $roleId = checkAuth('roleId');
        $thisMenu = $menuModel->getMenuId($controllerName)->menu_id ?? NULL;
        $isGranted = TRUE;
        if ($thisMenu) {
            $roleAccessModel = model('App\Models\RoleAccessModel');
            $isGranted = $roleAccessModel->getRoleAccessId($roleId, $thisMenu) !== NULL;
        }
        if ($session->user) {
            if ($controllerName === 'Auth' && !in_array(getMethod() . '::' . $router->methodName(), [
                'delete::index',
                'get::verifyEmail'
            ])) {
                return redirect()->to('dasbor');
            }
        }
        if (!$isGranted) {
            $flash = [
                'message' => 'Kamu tidak dapat mengakses halaman tersebut!',
                'type' => 'danger'
            ];
            setFlash($flash);
            return redirect()->to('masuk');
        }
    }
    return null;
}

/**
 * Filters data from Object, Array, or String to be escaped.
 * 
 * @param mixed $data Data that would like filtered.
 * @return mixed Filtered data.
 * @package Kartasolv\Helpers\security
 */
function filterOutput($data)
{
    if (is_object($data)) {
        $data = (object)htmlEscape(((array) $data));
    } else if (is_array($data)) {
        if (is_object(end($data))) {
            foreach ($data as $key => $d) {
                $data[$key] = (object)htmlEscape(((array) $d));
            }
        } else {
            $data = htmlEscape($data);
        }
    } else {
        $data = htmlEscape($data);
    }
    return $data;
}

/**
 * Returns HTML escaped variable.
 * @param	mixed	$var		The input string or array of strings to be escaped.
 * @param	bool	$double_encode	$double_encode set to FALSE prevents escaping twice.
 * @return	mixed			The escaped string or array of strings as a result.
 * @package Kartasolv\Helpers\security
 */
function htmlEscape($var, $double_encode = TRUE)
{
    if (empty($var)) {
        return $var;
    }
    if (is_array($var)) {
        foreach (array_keys($var) as $key) {
            $var[$key] = htmlEscape($var[$key], $double_encode);
        }
        return $var;
    }

    return htmlspecialchars($var, ENT_QUOTES, config('charset'), $double_encode);
}

/**
 * Encode data to random string. Useful for encode ID data.
 * 
 * @param string|int $data Data to be encoded, only accept integers.
 * @param string $type random key to be included when encoding data.
 * 
 * @return string Encoded data.
 * 
 * @package Kartasolv\Helpers\security
 */
function encode($data, $type = '')
{
    $hashids = new Hashids($type . substr(getenv('encryption.kartaKey'), strlen($type)), 16);
    return $hashids->encode($data);
}

/**
 * Decode data to random string. Useful for decode ID data.
 * 
 * @param string $data Data to be decoded, only accept string.
 * @param string $type random key to be included when encoding data.
 * 
 * @return mixed Decoded data.
 * 
 * @package Kartasolv\Helpers\security
 */
function decode($data, $type = '')
{
    $hashids = new Hashids($type . substr(getenv('encryption.kartaKey'), strlen($type)), 16);
    $decoded = $hashids->decode($data);
    return $decoded[0] ?? NULL;
}

/**
 * Showing 404 page.
 * 
 * @throws \CodeIgniter\Exceptions\PageNotFoundException 404 Not Found.
 * @package Kartasolv\Helpers\security
 */
function show404()
{
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
}

/**
 * Retrieve Captcha Sitekey.
 * 
 * @return string Captcha Site Key.
 * @package Kartasolv\Helpers\security
 */
function getCaptchaSitekey()
{
    return getenv('recaptcha.sitekey');
}

/**
 * Hash raw string to a password.
 * 
 * @param string $password Password input to be hashed (saved on database).
 * @return string Hashed password.
 * @package Kartasolv\Helpers\security
 */
function kartaPasswordHash(String $password)
{
    return password_hash($password, PASSWORD_ARGON2I, ['cost' => 10]) . strlen($password);
}

/**
 * Verify string input to hashed password.
 * 
 * @param string $password Raw string password input.
 * @param string $hash Hashed password
 * @return bool  Returns TRUE if the password and hash match, or FALSE otherwise.
 * @package Kartasolv\Helpers\security
 */
function kartaPasswordVerify(String $password, String $hash)
{
    return password_verify($password, substr($hash, 0, strlen($hash) - strlen((string)strlen($password))));
}

/**
 * Simplification retrieving method from Services.
 * @param mixed $method Method to be verified, if set null, this function
 * only retrieve HTTP Method sent.
 * @return bool|string Returns true/false if parameter provided, otherwise return method string.
 * @package Kartasolv\Helpers\security
 */
function getMethod($method = null)
{
    if (!$method) {
        return service('request')->getMethod();
    }
    return service('request')->getMethod() === $method;
}

/**
 * Redirection function when submitted form source/referrer is not from correct route.
 * @param string $routes Route to be checked.
 * @return bool|string Return false or go to referrer
 * @package Kartasolv\Helpers\security
 */
function acceptFrom($routes = '')
{
    if (getenv('CI_ENVIRONMENT') !== 'testing') {
        $referrer = Services::request()->getUserAgent()->getReferrer();
        if (!((base_url($routes) === $referrer) || (base_url("index.php/$routes") === $referrer))) {
            $flash = [
                'message' => 'Aksi tidak diperbolehkan!',
                'type' => 'danger'
            ];
            setFlash($flash);
            return $referrer;
        }
    }
    return false;
}
