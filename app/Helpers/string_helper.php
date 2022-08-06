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
    $result = '';

    switch ($session->getFlashdata('type')) {
        case 'success':
            $result = '<div x-data="{ alert: true }" x-show="alert" class="alert bg-teal-400 text-white relative w-full">
                <div class="mr-5"
                x-transition:enter="transition-all ease-out top-0 duration-300"
                x-transition:enter-start="transform opacity-0 -top-40 scale-50"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition-all ease-in top-0 duration-300"
                x-transition:leave-start="transform opacity-100"
                x-transition:leave-end="transform-all opacity-0 -top-40">
                ' . $session->getFlashdata($key) . '</div>
                <button type="button" class="absolute inset-y-0 right-0 mr-4 -mt-1 font-bold text-xl" @click="alert = ! alert">&times</button>
            </div>';
            break;

        case 'warning':
            $result = '<div x-data="{ alert: true }" x-show="alert" class="alert bg-orange-300 text-white relative w-full">
                <div class="mr-5"
                x-transition:enter="transition-all ease-out top-0 duration-300"
                x-transition:enter-start="transform opacity-0 -top-40 scale-50"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition-all ease-in top-0 duration-300"
                x-transition:leave-start="transform opacity-100"
                x-transition:leave-end="transform-all opacity-0 -top-40">
                ' . $session->getFlashdata($key) . '</div>
                <button type="button" class="absolute inset-y-0 right-0 mr-4 -mt-1 font-bold text-xl" @click="alert = ! alert">&times</button>
            </div>';
            break;

        case 'danger':
            $result = '<div x-data="{ alert: true }" x-show="alert" class="alert bg-rose-500 text-white relative w-full">
                <div class="mr-5"
                x-transition:enter="transition-all ease-out top-0 duration-300"
                x-transition:enter-start="transform opacity-0 -top-40 scale-50"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition-all ease-in top-0 duration-300"
                x-transition:leave-start="transform opacity-100"
                x-transition:leave-end="transform-all opacity-0 -top-40">
                ' . $session->getFlashdata($key) . '</div>
                <button type="button" class="absolute inset-y-0 right-0 mr-4 -mt-1 font-bold text-xl" @click="alert = ! alert">&times</button>
            </div>';
            break;

        default:
            $result = $session->getFlashdata($key);
            break;
    }
    return $result;
}
