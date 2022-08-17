<?php

/**
 * @package Kartasolv\Helpers\view
 */

function isSamePage($pageToCheck)
{
    helper('url');
    return ($pageToCheck === uri_string()) ? ' active ' : '';
}

/**
 * @package Kartasolv\Helpers\view
 */
function getSidebarMenu()
{
    $roleId = checkAuth('roleId');
    $ram = new \App\Models\RoleAccessModel();
    return $ram->getPageByRole($roleId);
}

/**
 * @package Kartasolv\Helpers\view
 */
function isSameController($controllerToCheck)
{
    $router = service('router');
    if (is_array($controllerToCheck)) {
        return in_array(str_replace("\App\Controllers\\", '', $router->controllerName()), $controllerToCheck);
    }
    return $controllerToCheck === str_replace("\App\Controllers\\", '', $router->controllerName());
}

/**
 * @package Kartasolv\Helpers\view
 */
function getCallToAction()
{
    $lm = new \App\Models\LandingModel();
    $data = $lm->getCallToAction();
    if ($data->cta_text && $data->cta_url) {
        if (parse_url($data->cta_url)['host'] === parse_url(base_url())['host']) {
            $data->target = '_self';
        } else {
            $data->target = 'blank';
        }
        return $data;
    }
    return false;
}

/**
 * @package Kartasolv\Helpers\view
 */
function getUserName()
{
    $um = new \App\Models\UsersModel;
    return $um->select('user_name')->find(checkAuth('userId'), true)->user_name;
}

/**
 * @package Kartasolv\Helpers\view
 * @return array
 */
function getMissions($data)
{
    $points = explode('\n', $data);
    return array_map(function ($e) {
        [$m, $d] = explode('[', $e);
        return [
            'mission' => $m,
            'desc' => str_replace(']', '', $d)
        ];
    }, $points);
}
