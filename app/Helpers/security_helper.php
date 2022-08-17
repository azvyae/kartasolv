<?php

use Config\Services;
use Hashids\Hashids;

/**
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
            if ($controllerName === 'Auth' && !isExcluded(getMethod() . '::' . $router->methodName())) {
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
 * @package Kartasolv\Helpers\security
 */
function isExcluded($str)
{
    return in_array($str, [
        'delete::index',
        'get::verifyEmail'
    ]);
}

/**
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
 * @package Kartasolv\Helpers\security
 * @param	mixed	$var		The input string or array of strings to be escaped.
 * @param	bool	$double_encode	$double_encode set to FALSE prevents escaping twice.
 * @return	mixed			The escaped string or array of strings as a result.
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
 * @package Kartasolv\Helpers\security
 */
function encode($data, $type = '')
{
    $hashids = new Hashids($type . substr(getenv('encryption.kartaKey'), strlen($type)), 16);
    return $hashids->encode($data);
}

/**
 * @package Kartasolv\Helpers\security
 */
function decode($data, $type = '')
{
    $hashids = new Hashids($type . substr(getenv('encryption.kartaKey'), strlen($type)), 16);
    $decoded = $hashids->decode($data);
    return $decoded[0] ?? NULL;
}

/**
 * @package Kartasolv\Helpers\security
 */
function show404()
{
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
}

/**
 * @package Kartasolv\Helpers\security
 */
function getCaptchaSitekey()
{
    return getenv('recaptcha.sitekey');
}

/**
 * @package Kartasolv\Helpers\security
 */
function kartaPasswordHash(String $password)
{
    return password_hash($password, PASSWORD_ARGON2I, ['cost' => 10]) . strlen($password);
}

/**
 * @package Kartasolv\Helpers\security
 */
function kartaPasswordVerify(String $password, String $hash)
{
    return password_verify($password, substr($hash, 0, strlen($hash) - strlen((string)strlen($password))));
}

/**
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
