<?php

function isSamePage($pageToCheck)
{
    helper('url');
    return ($pageToCheck === uri_string()) ? ' active ' : '';
}

function getSidebarMenu()
{
    $roleId = checkAuth('roleId');
    $roleAccessModel = model('App\Models\RoleAccessModel');
    return $roleAccessModel->getPageByRole($roleId);
}

function isSameController($controllerToCheck)
{
    $router = service('router');
    if (is_array($controllerToCheck)) {
        return in_array(str_replace("\App\Controllers\\", '', $router->controllerName()), $controllerToCheck);
    }
    return $controllerToCheck === str_replace("\App\Controllers\\", '', $router->controllerName());
}

function getCallToAction()
{
    $landingModel = model('App\Models\LandingModel');
    $data = $landingModel->getCallToAction();
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

function getMissions($data)
{
    $points = explode('<br/>', $data);
    return array_map(function ($e) {
        [$m, $d] = explode('[', $e);
        return [
            'mission' => $m,
            'desc' => str_replace(']', '', $d)
        ];
    }, $points);
}

function setInvalid($name)
{

    return service('validation')->hasError($name) ? ' is-invalid ' : '';
}

function showInvalidFeedback($name)
{
    return service('validation')->getError($name);
}
