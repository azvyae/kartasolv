<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\CreditCardRules;
use CodeIgniter\Validation\FileRules;
use CodeIgniter\Validation\FormatRules;
use CodeIgniter\Validation\Rules;
use Config\CustomRules;

class Validation extends BaseConfig
{
    //--------------------------------------------------------------------
    // Setup
    //--------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        CustomRules::class
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    //--------------------------------------------------------------------
    // Rules
    //--------------------------------------------------------------------
    public $gRecaptcha = [
        'g-recaptcha-response' => [
            'label' => 'reCaptcha',
            'rules' => 'required|verify_recaptcha',
            'errors' => [
                'verify_recaptcha' => 'Gagal verifikasi reCaptcha google!'
            ]
        ]
    ];
    public $updateProfile = [
        'user_name' => [
            'label' => 'Nama',
            'rules' => 'required|max_length[64]',
        ],
        'user_temp_mail' => [
            'label' => 'Email',
            'rules' => 'required|valid_email|max_length[64]|is_unique[users.user_email,user_email,{user_email}]',
            'errors' => [
                'is_unique' => 'Email sudah digunakan!'
            ]
        ],

    ];
    public $userEmail = [
        'user_email' => [
            'label' => 'Email',
            'rules' => 'required|valid_email|max_length[64]',
        ],

    ];
    public $userPassword = [
        'user_password' => [
            'label' => 'Kata Sandi',
            'rules' => 'required|min_length[6]',
        ],
    ];
    public $userNewPassword = [
        'user_new_password' => [
            'label' => 'Kata Sandi Baru',
            'rules' => 'required|min_length[6]',
        ],
    ];
    public $passwordVerify = [
        'password_verify' => [
            'label' => 'Verifikasi Kata Sandi',
            'rules' => 'required|matches[user_new_password]',
            'errors' => [
                'matches'  => 'Kata Sandi Harus Sama!'
            ]
        ],

    ];
}
