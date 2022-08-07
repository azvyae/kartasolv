<?php

function isSamePage($pageToCheck)
{
    $router = service('router');
    return $pageToCheck === str_replace("\App\Controllers\\", '', $router->controllerName()) . '::' . $router->methodName();
}
