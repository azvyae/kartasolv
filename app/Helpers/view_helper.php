<?php

use Config\Database;

/**
 * Check wether same page or not for navigation bar.
 * @param mixed $pageToCheck Route uri string
 * @return string Active class for html.
 * @package KartasolvHelpers\view
 */

function isSamePage($pageToCheck)
{
    helper('url');
    return ($pageToCheck === uri_string()) ? ' active ' : '';
}

/**
 * Retrieve sidebar menus based on roles.
 * @return mixed Menu that could be accessed by certain role checked by checkAuth() function.
 * @package KartasolvHelpers\view
 */
function getSidebarMenu()
{
    $db = Database::connect()->table('role_access');
    $roleId = checkAuth('roleId');
    return $db
        ->join(
            'menu',
            'menu.menu_id = role_access.menu_id',
        )
        ->join(
            'pages',
            'menu.menu_id = pages.menu_id',

        )
        ->where([
            'role_id' => $roleId
        ])
        ->orderBy('pages.page_id', 'asc')
        ->get()->getResult();
}

/**
 * Check wether same controller or not for navigation bar.
 * @param array $controllerToCheck Route uri string.
 * @return bool Returned boolean result after checking same controller or not.
 * @package KartasolvHelpers\view
 */
function isSameController(array $controllerToCheck)
{
    $router = service('router');
    return in_array(str_replace("\App\Controllers\\", '', $router->controllerName()), $controllerToCheck);
}

/**
 * Retrieve Call to Action url and text from database.
 * @return array|object|null|false Call to action data or nothing to return.
 * @package KartasolvHelpers\view
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
 * Retrieve Logged In User Name from database.
 * @return mixed User Name.
 * @package KartasolvHelpers\view
 */
function getUserName()
{
    $um = new \App\Models\UsersModel;
    return $um->select('user_name')->find(checkAuth('userId'), true)->user_name;
}

/**
 * Function for displaying missions on the landing page.
 * @param string $data Mission data taken from database.
 * @return array Mission and its description.
 * @package KartasolvHelpers\view
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
