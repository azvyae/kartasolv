<?php

declare(strict_types=1);

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox #### TS-03 Cek fungsi mengubah isi profil Karang Taruna
 */
class Scenario03Test extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    protected $sessionData, $um, $tc;
    protected function setUp(): void
    {
        parent::setUp();
        $this->um = new \App\Models\UsersModel();
        $this->sessionData = [
            'user' => objectify([
                'userId' => 2,
                'roleId' => 1,
                'roleString' => 'admin',
                'roleName' => 'Administrator',
            ])
        ];
        $this->tc = [
            'step' => [],
            'data' => [],
            'expected' => '',
            'actual' => ''
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        if (!isset($_SESSION))
            session_start();
        if (session_status() === PHP_SESSION_ACTIVE)
            session_destroy();
        Services::validation()->reset();
        parseTest($this->tc);
        $this->assertTrue($this->tc['expected'] === $this->tc['actual'], "expected: " . $this->tc['expected'] . "\n" . 'actual: ' . $this->tc['actual']);
    }

    /**
     * @testdox TC-01 Mengakses halaman pengaturan profil Karang Taruna
     */
    public function testIndex()
    {
        $this->tc['expected'] = "Menampilkan halaman Pengaturan Profil Karta";
        $this->tc['step'] = [
            "Masuk ke halaman pengaturan Profil Karta"
        ];
        $result = $this->withSession($this->sessionData)->call('get', 'konten/profil-karang-taruna');
        $result->assertOK();
        $result->assertSee('Pengaturan Profil Karta', 'h1');
        $result->assertSee('Ubah Informasi Utama');
        $result->assertSee('Ubah Kegiatan Kami');
        $result->assertSee('Data Pengurus');
        $this->tc['actual'] = "Menampilkan halaman Pengaturan Profil Karta";
    }

    /**
     * @testdox TC-02 Mengubah isi informasi utama
     */
    public function testMainInfo()
    {
        $this->tc['expected'] = "Menampilkan pesan Info utama berhasil diperbarui.";
        $this->tc['step'] = [
            'Masuk ke halaman Ubah Informasi Utama',
            "Ubah data pada input",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'landing_title: Test Landing Title',
            'landing_tagline: Test Tagline',
            'cta_text: Call to Action',
            'cta_url: https://test.com',
            'vision: Test vision',
            "mission: - Test mission 1 [Mission description 1.]\n- Test mission 2 [Mission description 2.]",
            "landing_image: test.jpg"
        ];
        $result = $this->withSession($this->sessionData)->call('get', 'konten/profil-karang-taruna/info-utama');
        $result->assertOK();
        $result->assertSee('Ubah Informasi Utama', 'h1');
        $result->assertSeeElement('input[name=landing_title]');
        $result->assertSeeElement('input[name=landing_tagline]');
        $result->assertSeeElement('input[name=cta_text]');
        $result->assertSeeElement('input[name=cta_url]');
        $result->assertSeeElement('input[name=vision]');
        $result->assertSeeElement('input[name=landing_image]');
        $result->assertSeeElement('textarea[name=mission]');

        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', 'konten/profil-karang-taruna/info-utama', 'Content\OrganizationProfile::mainInfo']
        ])->withSession(
            $this->sessionData
        )->call('post', 'konten/profil-karang-taruna/info-utama', [
            csrf_token() => csrf_hash(),
            '_method' => 'PUT',
            'landing_title' => 'Test Landing Title',
            'landing_tagline' => 'Test Tagline',
            'cta_text' => 'Call to Action',
            'cta_url' => 'https://test.com',
            'vision' => 'Test vision',
            'mission' => "- Test mission 1 [Mission description 1.]\n
                          - Test mission 2 [Mission description 2.]",
        ]);
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * @testdox TC-03 Mengubah isi informasi utama dengan format url salah
     */
    public function testMainInfoInvalid()
    {
        $this->tc['expected'] = "Menampilkan pesan Kolom Url Call to Action harus berisi sebuah URL yang valid.";
        $this->tc['step'] = [
            'Masuk ke halaman Ubah Informasi Utama',
            "Ubah data pada input",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'landing_title: Test Landing Title',
            'landing_tagline: Test Tagline',
            'cta_text: Call to Action',
            'cta_url: not an url',
            'vision: Test vision',
            "mission: - Test mission 1 [Mission description 1.]\n- Test mission 2 [Mission description 2.]",
            "landing_image: test.jpg"
        ];

        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', 'konten/profil-karang-taruna/info-utama', 'Content\OrganizationProfile::mainInfo']
        ])->withSession(
            $this->sessionData
        )->call('post', 'konten/profil-karang-taruna/info-utama', [
            csrf_token() => csrf_hash(),
            '_method' => 'PUT',
            'landing_title' => 'Test Landing Title',
            'landing_tagline' => 'Test Tagline',
            'cta_text' => 'Call to Action',
            'cta_url' => 'not an url',
            'vision' => 'Test vision',
            'mission' => "- Test mission 1 [Mission description 1.]\n
                          - Test mission 2 [Mission description 2.]",
        ]);
        $validationError = service('validation')->getError('cta_url');
        $result->isTrue($validationError === "Kolom Url Call to Action harus berisi sebuah URL yang valid.", $validationError);
        $this->tc['actual'] = "Menampilkan pesan " . $validationError;
    }

    /**
     * @testdox TC-03 Mengubah isi informasi utama dengan format url salah
     */
    public function testOurActivities()
    {
        $this->tc['expected'] = "Menampilkan pesan Kolom Url Call to Action harus berisi sebuah URL yang valid.";
        $this->tc['step'] = [
            'Masuk ke halaman Ubah Informasi Utama',
            "Ubah data pada input",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'landing_title: Test Landing Title',
            'landing_tagline: Test Tagline',
            'cta_text: Call to Action',
            'cta_url: not an url',
            'vision: Test vision',
            "mission: - Test mission 1 [Mission description 1.]\n- Test mission 2 [Mission description 2.]",
            "landing_image: test.jpg"
        ];
        $result = $this->withSession($this->sessionData)->call('get', 'konten/profil-karang-taruna/kegiatan-kami');
        $result->assertOK();
        $result->assertSee('Ubah Kegiatan Kami', 'h1');
        $result->assertSeeElement('input[name=title_a]');
        $result->assertSeeElement('input[name=desc_a]');
        $result->assertSeeElement('input[name=image_a]');
        $result->assertSeeElement('input[name=title_b]');
        $result->assertSeeElement('input[name=desc_b]');
        $result->assertSeeElement('input[name=image_b]');
        $result->assertSeeElement('input[name=title_c]');
        $result->assertSeeElement('input[name=desc_c]');
        $result->assertSeeElement('input[name=image_c]');
    }

    public function testMembers()
    {
        $result = $this->withSession($this->sessionData)->call('get', 'konten/profil-karang-taruna/pengurus');
        $result->assertOK();
        $result->assertSee('Data Pengurus', 'h1');
        $result->assertSeeElement('table');
    }

    public function testMembersAjax()
    {
        $datatablesQuery = [
            'draw' => '1',
            'columns' => [
                [
                    'data' => 'member_name',
                    'name' => 'member_name',
                    'searchable' => 'true',
                    'orderable' => 'true',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
                [
                    'data' => 'member_position',
                    'name' => 'member_position',
                    'searchable' => 'true',
                    'orderable' => 'true',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
                [
                    'data' => 'member_type',
                    'name' => 'member_type',
                    'searchable' => 'true',
                    'orderable' => 'true',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
                [
                    'data' => 'member_active',
                    'name' => 'member_active',
                    'searchable' => 'true',
                    'orderable' => 'true',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
                [
                    'data' => 'unique_id',
                    'name' => 'unique_id',
                    'searchable' => 'true',
                    'orderable' => 'false',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
            ],
            'order' => [
                [
                    'column' => '0',
                    'dir' => 'asc',
                ],
            ],
            'start' => '0',
            'length' => '10',
            'search' => [
                'value' => '',
                'regex' => 'false',
            ],
            'orderable' => [
                'member_name',
                'member_position',
                'member_type',
                'member_active',
            ],
            'searchable' => [
                'member_name',
                'member_position',
                'member_type',
                'member_active',
            ],
            '_' => '1660301593638',
        ];
        $headers = [
            'X-Requested-With' => 'XMLHttpRequest'
        ];
        $result = $this->withSession($this->sessionData)->withBodyFormat('json')->withHeaders($headers)->call('get', 'konten/profil-karang-taruna/pengurus', $datatablesQuery);
        $result->assertOK();
        $result->assertTrue($result->getJSON() !== false);
    }

    public function testMemberCreate()
    {
        $result = $this->withSession($this->sessionData)->call('get', "konten/profil-karang-taruna/pengurus/tambah");
        $result->assertOK();
        $result->assertSee('Tambah Data Pengurus', 'h1');
        $result->assertSeeElement('input[name=member_name]');
        $result->assertSeeElement('input[name=member_position]');
        $result->assertSeeElement('select[name=member_type]');
        $result->assertSeeElement('input[name=member_active]');
    }
    public function testMemberUpdateFound()
    {
        $id = encode('1', 'members');
        $result = $this->withSession($this->sessionData)->call('get', "konten/profil-karang-taruna/pengurus/$id");
        $result->assertOK();
        $result->assertSee('Ubah Data Pengurus', 'h1');
        $result->assertSeeElement('input[name=member_name]');
        $result->assertSeeElement('input[name=member_position]');
        $result->assertSeeElement('select[name=member_type]');
        $result->assertSeeElement('input[name=member_active]');
    }
    public function testMemberUpdateNotFound()
    {
        $this->expectException(PageNotFoundException::class);
        $id = 'not-found';
        $result = $this->withSession($this->sessionData)->call('get', "konten/profil-karang-taruna/pengurus/$id");
        $result->assertOK();

        // $result->expectExceptionMessage('Page Not Found');
    }
}
