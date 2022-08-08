<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\CreditCardRules;
use CodeIgniter\Validation\FileRules;
use CodeIgniter\Validation\FormatRules;
use CodeIgniter\Validation\Rules;

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
    public $login = [
        'user_email' => [
            'label' => 'Email',
            'rules' => 'required|valid_email',
        ],
        'user_password' => [
            'label' => 'Kata Sandi',
            'rules' => 'required',
        ],
        'g-recaptcha-response' => [
            'label' => 'reCaptcha',
            'rules' => 'required|verifyRecaptcha',
            'errors' => [
                'verifyRecaptcha' => 'Gagal verifikasi reCaptcha google!'
            ]
        ]
    ];
}
