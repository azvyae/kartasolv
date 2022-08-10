<?php

function removeProtocol($url)
{
    $disallowed = array('http://', 'https://');
    foreach ($disallowed as $d) {
        if (strpos($url, $d) === 0) {
            return str_replace($d, '', $url);
        }
    }
    return $url;
}

function objectify($data)
{
    return json_decode(json_encode($data));
}

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
 * Style your flashdata here!
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

function parseMission($string)
{
    $mission = objectify(getMissions($string));
    $txt = '';
    foreach ($mission as $m) {
        $txt .= "-$m->mission ($m->desc)\n";
    }
    return $txt;
}


function setInvalid($name)
{

    return service('validation')->hasError($name) ? ' is-invalid ' : '';
}

function showInvalidFeedback($name)
{
    return service('validation')->getError($name);
}
