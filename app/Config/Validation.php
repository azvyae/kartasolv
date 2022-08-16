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
            'rules' => 'verify_recaptcha',
            'errors' => [
                'verify_recaptcha' => 'Gagal verifikasi reCaptcha google!'
            ]
        ]
    ];

    public $spreadsheet = [
        'file_excel' => [
            'label' => 'Berkas Spreadsheet',
            'rules' => 'uploaded[file_excel]|ext_in[file_excel,xlsx,xls]',
            'errors' => [
                'ext_in' => 'Berkas yang dapat diunggah hanya XLSX atau XLS saja!'
            ]
        ]
    ];
}
