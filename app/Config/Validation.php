<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\CreditCardRules;
use CodeIgniter\Validation\FileRules;
use CodeIgniter\Validation\FormatRules;
use CodeIgniter\Validation\Rules;
use Config\CustomRules;

/**
 * Validation Rule Groups
 * 
 * Provides ruleset declaration, custom rules, and error templates for app.
 * 
 * @package Kartasolv\Config
 */
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
    /**
     * Recaptcha rule.
     * @var (string|string[])[][] $gRecaptcha
     */
    public $gRecaptcha = [
        'g-recaptcha-response' => [
            'label' => 'reCaptcha',
            'rules' => 'verify_recaptcha',
            'errors' => [
                'verify_recaptcha' => 'Gagal verifikasi reCaptcha google!'
            ]
        ]
    ];

    /**
     * Spreadsheet upload rule.
     * @var (string|string[])[][] $spreadsheet
     */
    public $spreadsheet = [
        'file_excel' => [
            'label' => 'Berkas Spreadsheet',
            'rules' => 'uploaded[file_excel]|ext_in[file_excel,xlsx,xls]|max_size[file_excel,1024]',
            'errors' => [
                'ext_in' => 'Berkas yang dapat diunggah hanya XLSX atau XLS saja!',
                'max_size' => 'Ukuran maksimal file adalah 1 MB'
            ]
        ]
    ];
}
