<?php

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
