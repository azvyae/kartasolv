<?php

declare(strict_types=1);

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\DOMParser;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox #### TS-03 Cek fungsi mengubah isi profil Karang Taruna
 */
class Scenario03Test extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    protected $sessionData, $mm, $tc;
    protected function setUp(): void
    {
        parent::setUp();
        $this->mm = new \App\Models\MembersModel();
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
        $domParser = new DOMParser;
        $domParser->withString(service('response')->getBody());
        $checks = [
            $domParser->see('Pengaturan Profil Karta', 'h1'),
            $domParser->see('Ubah Informasi Utama'),
            $domParser->see('Ubah Kegiatan Kami'),
            $domParser->see('Data Pengurus'),
        ];
        if (!in_array(false, $checks)) {
            $this->tc['actual'] = "Menampilkan halaman Pengaturan Profil Karta";
        }
    }

    /**
     * @testdox TC-02 Mengubah isi informasi utama
     */
    public function testMainInfo()
    {
        $this->tc['expected'] = "Menampilkan pesan Info utama berhasil diperbarui.";
        $this->tc['step'] = [
            'Masuk ke halaman Ubah Informasi Utama',
            "Ubah data pada kolom",
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
            "Ubah data pada kolom",
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
     * @testdox TC-04 Mengubah isi Kegiatan Kami
     */
    public function testOurActivities()
    {
        $this->tc['expected'] = "Menampilkan pesan Kegiatan berhasil diperbarui.";
        $this->tc['step'] = [
            'Masuk ke halaman Ubah Kegiatan Kami',
            "Ubah data pada kolom",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'title_a: Test Title A',
            'desc_a: Test Desc A',
            'image_a: test_image_a.jpg',
            'title_a: Test Title B',
            'desc_b: Test Desc B',
            'image_b: test_image_b.jpg',
            'title_b: Test Title C',
            'desc_c: Test Desc C',
            'image_c: test_image_c.jpg',
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
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', 'konten/profil-karang-taruna/kegiatan-kami', 'Content\OrganizationProfile::ourActivities']
        ])->withSession(
            $this->sessionData
        )->call('post', 'konten/profil-karang-taruna/kegiatan-kami', [
            csrf_token() => csrf_hash(),
            '_method' => 'PUT',
            'title_a' => 'Test Title A',
            'desc_a' => 'Test Desc A',
            'title_b' => 'Test Title B',
            'desc_b' => 'Test Desc B',
            'title_c' => 'Test Title C',
            'desc_c' => 'Test Desc C',
        ]);
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * @testdox TC-05 Mengubah isi Kegiatan Kami dengan format judul melebihi 64 karakter
     */
    public function testOurActivitiesInvalid()
    {
        $this->tc['expected'] = "Menampilkan pesan Kolom Nama Kegiatan 1 tidak bisa melebihi 64 panjang karakter.";
        $this->tc['step'] = [
            'Masuk ke halaman Ubah Kegiatan Kami',
            "Ubah data pada kolom",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'title_a: Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
            'desc_a: Test Desc A',
            'image_a: test_image_a.jpg',
            'title_a: Test Title B',
            'desc_b: Test Desc B',
            'image_b: test_image_b.jpg',
            'title_b: Test Title C',
            'desc_c: Test Desc C',
            'image_c: test_image_c.jpg',
        ];
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', 'konten/profil-karang-taruna/kegiatan-kami', 'Content\OrganizationProfile::ourActivities']
        ])->withSession(
            $this->sessionData
        )->call('post', 'konten/profil-karang-taruna/kegiatan-kami', [
            csrf_token() => csrf_hash(),
            '_method' => 'PUT',
            'title_a' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. ',
            'desc_a' => 'Test Desc A',
            'title_b' => 'Test Title B',
            'desc_b' => 'Test Desc B',
            'title_c' => 'Test Title C',
            'desc_c' => 'Test Desc C',
        ]);
        $validationError = service('validation')->getError('title_a');
        $result->isTrue($validationError === "Kolom Nama Kegiatan 1 tidak bisa melebihi 64 panjang karakter.", $validationError);
        $this->tc['actual'] = "Menampilkan pesan " . $validationError;
    }

    /**
     * @testdox TC-06 Menampilkan Data Pengurus
     */
    public function testMembers()
    {
        $this->tc['expected'] = "Menampilkan 1 baris data dengan nama M Taufan Z";
        $this->tc['step'] = [
            'Masuk ke halaman Data Pengurus',
            "Klik tombol dengan logo saring",
            "Isi kolom Tipe Pengurus",
            "Klik tombol tambah kondisi DAN",
            "Isi kolom Nama Pengurus",
            "Tutup panel saring",
            "Isi kolom pencarian di bagian kanan"
        ];
        $result = $this->withSession($this->sessionData)->call('get', 'konten/profil-karang-taruna/pengurus');
        $result->assertOK();
        $result->assertSee('Data Pengurus', 'h1');
        $result->assertSeeElement('table');
        $this->tc['data'] = [
            'search: taufan',
            'member_type: 3',
            'member_name: (contains) a',
            'condition: AND',
        ];
        $datatablesQuery = [
            "draw" => "8",
            "columns" => [
                [
                    "data" => "member_name",
                    "name" => "member_name",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "member_position",
                    "name" => "member_position",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "member_type",
                    "name" => "member_type",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "member_active",
                    "name" => "member_active",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "unique_id",
                    "name" => "unique_id",
                    "searchable" => "true",
                    "orderable" => "false",
                    "search" => ["value" => "", "regex" => "false"],
                ],
            ],
            "order" => [["column" => "2", "dir" => "asc"]],
            "start" => "0",
            "length" => "25",
            "search" => ["value" => "taufan", "regex" => "false"],
            "orderable" => [
                "member_name",
                "member_position",
                "member_type",
                "member_active",
            ],
            "searchable" => [
                "member_name",
                "member_position",
                "member_type",
                "member_active",
            ],
            "searchBuilder" => [
                "criteria" => [
                    [
                        "condition" => "=",
                        "data" => "Tipe",
                        "origData" => "member_type",
                        "type" => "array",
                        "value" => ["3"],
                        "value1" => "3",
                    ],
                    [
                        "condition" => "contains",
                        "data" => "Nama",
                        "origData" => "member_name",
                        "type" => "html",
                        "value" => ["a"],
                        "value1" => "a",
                    ],
                ],
                "logic" => "AND",
            ],
            "_" => "1660977583213",
        ];
        $result = $this->withSession(
            $this->sessionData
        )->withBodyFormat(
            'json'
        )->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest'
        ])->call('get', 'konten/profil-karang-taruna/pengurus', $datatablesQuery);
        $result->assertOK();
        $data = json_decode(json_decode($result->getJSON()));
        $this->tc['actual'] = "Menampilkan " . $data->recordsFiltered . " baris data dengan nama " . $data->data[0]->member_name;
    }

    /**
     * @testdox TC-07 Menambahkan data pengurus validasi gagal
     */
    public function testMemberCreateInvalid()
    {
        $this->tc['expected'] = "Menampilkan pesan Kamu Hanya Dapat Memilih Opsi Aktif/Nonaktif.";
        $this->tc['step'] = [
            'Masuk ke halaman Tambah Data Pengurus',
            "Isi formulir data pengurus",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'member_name: Test Name',
            'member_position: Test Position',
            'member_type: 2',
            'member_active: Aktif',
            'member_image: member_image.jpg',
        ];
        $result = $this->withSession($this->sessionData)->call('get', "konten/profil-karang-taruna/pengurus/tambah");
        $result->assertOK();
        $result->assertSee('Tambah Data Pengurus', 'h1');
        $result->assertSeeElement('input[name=member_name]');
        $result->assertSeeElement('input[name=member_position]');
        $result->assertSeeElement('select[name=member_type]');
        $result->assertSeeElement('input[name=member_active]');
        $result->assertSeeElement('input[name=member_image]');
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withSession(
            $this->sessionData
        )->call('post', 'konten/profil-karang-taruna/pengurus/tambah', [
            csrf_token() => csrf_hash(),
            'member_name' => 'Test Name',
            'member_position' => 'Test Position',
            'member_type' => 2,
            'member_active' => 'Wrong Active',
        ]);
        $validationError = service('validation')->getError('member_active');
        $result->isTrue($validationError === "Kamu Hanya Dapat Memilih Opsi Aktif/Nonaktif.", $validationError);
        $this->tc['actual'] = "Menampilkan pesan " . $validationError;
    }

    /**
     * @testdox TC-08 Menambahkan data Pengurus
     */
    public function testMemberCreate()
    {
        $this->tc['expected'] = "Menampilkan pesan Data Pengurus berhasil diperbarui.";
        $this->tc['step'] = [
            'Masuk ke halaman Tambah Data Pengurus',
            "Isi formulir data pengurus",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'member_name: Test Name',
            'member_position: Test Position',
            'member_type: 2',
            'member_active: Aktif',
            'member_image: member_image.jpg',
        ];
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withSession(
            $this->sessionData
        )->call('post', 'konten/profil-karang-taruna/pengurus/tambah', [
            csrf_token() => csrf_hash(),
            'member_name' => 'Test Name',
            'member_position' => 'Test Position',
            'member_type' => 2,
            'member_active' => 'Aktif',
        ]);
        $result->assertSessionHas('message', "Data Pengurus berhasil diperbarui.");
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * @testdox TC-09 Mencari data pengurus dengan ID acak
     */
    public function testMemberUpdateNotFound()
    {
        $this->tc['expected'] = "Menampilkan pesan Halaman Tidak Ditemukan";
        $this->tc['step'] = [
            "Masuk ke halaman Ubah Data Pengurus"
        ];
        $this->tc['data'] = [
            'member_id: notFoundId'
        ];
        try {
            $id = 'notFoundId';
            $this->withSession($this->sessionData)->call('get', "konten/profil-karang-taruna/pengurus/$id");
        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            $message = $e->getMessage();
        }
        $this->tc['actual'] = "Menampilkan pesan $message";
    }

    /**
     * @testdox TC-10 Mengubah data pengurus, menonaktifkan pengurus
     */
    public function testMemberUpdateFound()
    {
        $this->tc['expected'] = "Menampilkan pesan Data Pengurus berhasil diperbarui.";
        $this->tc['step'] = [
            'Masuk ke halaman Ubah Data Pengurus',
            "Ubah formulir data pengurus",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'member_name: Test Name',
            'member_position: Test Position',
            'member_type: 3',
        ];
        $member_id = $this->db->table('members')->select('member_id')->like("member_name", "Test")->get(1)->getRow()->member_id;
        $id = encode($member_id, 'members');
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', "konten/profil-karang-taruna/pengurus/(:alphanum)", "Content\OrganizationProfile::memberCrud/$1"],
        ])->withSession(
            $this->sessionData
        )->call('post', "konten/profil-karang-taruna/pengurus/$id", [
            csrf_token() => csrf_hash(),
            '_method' => 'PUT',
            'member_name' => 'Test Name',
            'member_position' => 'Test Position',
            'member_type' => 3,
        ]);
        $result->assertSessionHas('message', "Data Pengurus berhasil diperbarui.");
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * @testdox TC-11 Menghapus data pengurus
     */
    public function testDeleteMemberData()
    {
        $builder = $this->db->table('members');
        $countField = $builder->like("member_name", 'test')->countAllResults();
        $ids = $builder->like("member_name", 'test')->select('member_id')->get()->getResult();
        $ids = array_map(function ($e) {
            return encode($e->member_id, 'members');
        }, $ids);
        $stringIds = json_encode($ids);
        $this->tc['expected'] = "Menampilkan pesan $countField data pengurus berhasil dihapus";
        $this->tc['step'] = [
            'Masuk ke halaman Data Pengurus',
            "Pilih $countField data untuk dihapus",
            "Tekan tombol hapus"
        ];
        $this->tc['data'] = [
            "selections: $stringIds"
        ];

        $result = $this->withRoutes([
            ['post', "konten/profil-karang-taruna/pengurus", "Content\OrganizationProfile::members"],
        ])->withSession(
            $this->sessionData
        )->withBodyFormat(
            'json'
        )->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])->call('post', "konten/profil-karang-taruna/pengurus", [
            csrf_token() => csrf_hash(),
            '_method' => "DELETE",
            'selections' => $ids,
        ]);
        $this->mm->purgeDeleted();
        $result->assertSessionHas('message', "$countField data pengurus berhasil dihapus");
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }
}
