<?php

/**
 * Removing protocol like http and https from url provided.
 * @param string $url Url that would like to protocol removed.
 * @return string Removed protocol url string.
 * @package KartasolvHelpers\string
 */
function removeProtocol(String $url)
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
 * Add protocol to provided url string.
 * @param string $url Url that would like to protocol appended.
 * @param string $protocol Appended protocol, choose between https:// or http://.
 * @return string Complete url string with its protocol.
 * @package KartasolvHelpers\string
 */
function addProtocol(String $url, $protocol = 'https://')
{
    $url = removeProtocol($url);
    return $protocol . $url;
}

/**
 * Change associative array/string into PHP Object.
 * @param mixed $data Things that would like to objectified.
 * @return object Objectified data.
 * @package KartasolvHelpers\string
 */
function objectify($data)
{
    return json_decode(json_encode($data));
}

/**
 * Set session flashdata with custom format according Codeigniter 4 session.
 * @param mixed $flash Flash data that would like to generated, better if use associative array with this kind of formatting:
 * ```php
 * $flash = [
 *      'message' => 'Message Data',
 *      'type' => 'danger'|'warning'|'success' |'info'
 * ];
 * setFlash($flash);
 * ```
 * @return void Only sets flash data.
 * @package KartasolvHelpers\string
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
 * Retrieve flash data.
 * @param mixed $key Better if use only 'message' to retrieve the data.
 * @return array|null|string Returned formatted flash data.
 * @package KartasolvHelpers\string
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
 * Parse mission string from the database into generated string for textarea input.
 * @param string $string Mission data taken from database.
 * @return string Parsed mission data to textarea.
 * @package KartasolvHelpers\string
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
 * Sets an input to be invalid by returning is-invalid class to the input.
 * @param string $name Input name.
 * @return string Returned string wether it is invalid or not after form validation input.
 * @package KartasolvHelpers\string
 */
function setInvalid($name)
{
    return service('validation')->hasError($name) ? ' is-invalid ' : '';
}

/**
 * Retrieves error message from form validation input.
 * @param string $name Input name.
 * @return string String wether error input message.
 * @package KartasolvHelpers\string
 */
function showInvalidFeedback($name)
{
    return service('validation')->getError($name);
}

use Config\Database;

/**
 * Data counter for certain table, currently the param only used for Communities table joined to PMPSKS Table.
 * @param string $table Table name.
 * @param string $param Only accepts PMKS or PSKS as a string.
 * @return int|string Counted result.
 * @package KartasolvHelpers\string
 */
function countTable($table, $param = '')
{
    if ($param) {
        return Database::connect()->table($table)->join('pmpsks_types', 'pmpsks_types.pmpsks_id = communities.pmpsks_type', 'full')->where('pmpsks_types.pmpsks_type', $param)->countAllResults();
    }
    return Database::connect()->table($table)->countAllResults();
}
