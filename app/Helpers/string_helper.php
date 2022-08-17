<?php

/**
 * @package Kartasolv\Helpers\string
 */
function removeProtocol($url)
{
    $disallowed = ['http://', 'https://'];
    foreach ($disallowed as $d) {
        if (strpos($url, $d) === 0) {
            return str_replace($d, '', $url);
        }
    }
    return $url;
}

/**
 * @package Kartasolv\Helpers\string
 */
function addProtocol($url, $protocol = 'https://')
{
    $url = removeProtocol($url);
    return $protocol . $url;
}

/**
 * @package Kartasolv\Helpers\string
 */
function objectify($data)
{
    return json_decode(json_encode($data));
}

/**
 * @package Kartasolv\Helpers\string
 */
function setFlash($flash)
{
    $session = session();
    $session->setFlashdata('message', $flash['message']);
    $session->setFlashdata('type', $flash['type']);
    if (array_key_exists('condition', $flash)) {
        $session->setFlashdata('condition', $flash['condition']);
    }
}

/**
 * @package Kartasolv\Helpers\string
 */
function getFlash($key)
{

    $session = session();
    $type = $session->getFlashdata('type');
    $allowedType = ['success', 'warning', 'danger', 'info'];
    if (in_array($type, $allowedType)) {
        return "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>" . $session->getFlashdata($key) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    } else {
        return $session->getFlashdata($key);
    }
}

/**
 * @package Kartasolv\Helpers\string
 */
function parseMission($string)
{
    $mission = objectify(getMissions($string));
    $txt = '';
    foreach ($mission as $m) {
        $txt .= "- $m->mission [$m->desc]\n";
    }
    return $txt;
}


/**
 * @package Kartasolv\Helpers\string
 */
function setInvalid($name)
{

    return service('validation')->hasError($name) ? ' is-invalid ' : '';
}

/**
 * @package Kartasolv\Helpers\string
 */
function showInvalidFeedback($name)
{
    return service('validation')->getError($name);
}

use Config\Database;

/**
 * @package Kartasolv\Helpers\string
 */
function countTable($table, $param = '')
{
    if ($param) {
        return Database::connect()->table($table)->join('pmpsks_types', 'pmpsks_types.pmpsks_id = communities.pmpsks_type', 'full')->where('pmpsks_types.pmpsks_type', $param)->countAllResults();
    }
    return Database::connect()->table($table)->countAllResults();
}
