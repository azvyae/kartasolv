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
