<?php

function isSamePage($pageToCheck)
{
    $router = service('router');
    return $pageToCheck === str_replace("\App\Controllers\\", '', $router->controllerName()) . '::' . $router->methodName();
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
