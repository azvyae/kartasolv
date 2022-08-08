<?php

use Hashids\Hashids;

function checkAuth($data = null)
{
    $session = session();
    if ($data) {
        if (strtolower($data) == 'all') {
            return $session->user ?? null;
        } else {
            return $session->user->$data ?? null;
        }
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
            if ($controllerName === 'Auth' && $router->methodName() !== 'logout') {
                return redirect()->to(base_url('dasbor'));
            }
            if (!$isGranted) {
                $flash = [
                    'message' => 'Kamu tidak dapat mengakses halaman tersebut!',
                    'type' => 'danger'
                ];
                setFlash($flash);
                return redirect()->to(base_url('dasbor'));
            }
        } else if (!$isGranted) {
            $flash = [
                'message' => 'Masuk untuk akses ke halaman!',
                'type' => 'danger'
            ];
            setFlash($flash);
            return redirect()->to(base_url('masuk'));
        }
    }
    return null;
}

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
 *
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

    if (!is_array($var) && !is_string($var)) {
        return $var;
    } else {
        return htmlspecialchars($var, ENT_QUOTES, config('charset'), $double_encode);
    }
}

function encode($data, $type = '')
{
    $hashids = new Hashids($type . substr(getenv('encryption.kartaKey'), strlen($type)), 16);
    return $hashids->encode($data);
}

function decode($data, $type = '')
{
    $hashids = new Hashids($type . substr(getenv('encryption.kartaKey'), strlen($type)), 16);
    $decoded = $hashids->decode($data);
    if (count($decoded) > 1) {
        return $decoded;
    }
    return $decoded[0] ?? NULL;
}

function show404()
{
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
}

function getCaptchaSitekey()
{
    return getenv('recaptcha.sitekey');
}

function kartaPasswordHash(String $password)
{
    return password_hash($password, PASSWORD_ARGON2I, ['cost' => 10]) . strlen($password);
}

function kartaPasswordVerify(String $password, String $hash)
{
    return password_verify($password, substr($hash, 0, strlen($hash) - strlen((string)strlen($password))));
}
