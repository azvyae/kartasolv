<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox #### TS-04 Cek fungsi mengubah isi informasi sejarah Karang Taruna
 */
class Scenario04Test extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    protected $sessionData, $tc;
    protected function setUp(): void
    {
        parent::setUp();
        $this->sessionData = [
            'user' => objectify([
                'userId' => 2,
                'roleId' => 1,
                'roleString' => 'admin',
                'roleName' => 'Administrator',
            ])
        ];
        $this->tc = [
            'scenario' => 'TS-04',
            'case_code' => '',
            'case' => '',
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
     * @testdox TC-01 Mengubah isi sejarah Karang Taruna
     */
    public function testHistory()
    {
        $this->tc['expected'] = "Menampilkan pesan Info Sejarah berhasil diperbarui.";
        $this->tc['step'] = [
            'Masuk ke halaman Ubah Info Sejarah',
            "Ubah data pada kolom",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'title_a: Test Title A',
            'desc_a: Test Desc A',
            'title_b: Test Title B',
            'desc_b: Test Desc B',
            'title_c: Test Title C',
            'desc_c: Test Desc C',
            'title_d: Test Title D',
            'desc_d: Test Desc D',
            'image_a: test_image_a.jpg',
            'image_b: test_image_b.jpg',
        ];
        $result = $this->withSession($this->sessionData)->call('get', 'konten/sejarah');
        $result->assertOK();
        $result->assertSee('Ubah Info Sejarah', 'h1');
        $result->assertSeeElement('input[name=title_a]');
        $result->assertSeeElement('input[name=desc_a]');
        $result->assertSeeElement('input[name=title_b]');
        $result->assertSeeElement('input[name=desc_b]');
        $result->assertSeeElement('input[name=title_c]');
        $result->assertSeeElement('input[name=desc_c]');
        $result->assertSeeElement('input[name=title_d]');
        $result->assertSeeElement('input[name=desc_d]');
        $result->assertSeeElement('input[name=image_a]');
        $result->assertSeeElement('input[name=image_b]');

        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', 'konten/sejarah', 'Content\History::index']
        ])->withSession(
            $this->sessionData
        )->call('post', 'konten/sejarah', [
            csrf_token() => csrf_hash(),
            '_method' => 'PUT',
            'title_a' => 'Test Title A',
            'desc_a' => 'Test Desc A',
            'title_b' => 'Test Title B',
            'desc_b' => 'Test Desc B',
            'title_c' => 'Test Title C',
            'desc_c' => 'Test Desc C',
            'title_d' => 'Test Title D',
            'desc_d' => 'Test Desc D',
        ]);
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * @testdox TC-02 Mengubah isi sejarah Karang Taruna gagal validasi
     */
    public function testHistoryInvalid()
    {
        $this->tc['expected'] = "Menampilkan pesan Kolom Judul 1 tidak bisa melebihi 64 panjang karakter.";
        $this->tc['step'] = [
            'Masuk ke halaman Ubah Info Sejarah',
            "Ubah data pada kolom",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'title_a: Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
            'desc_a: Test Desc A',
            'title_b: Test Title B',
            'desc_b: Test Desc B',
            'title_c: Test Title C',
            'desc_c: Test Desc C',
            'title_d: Test Title D',
            'desc_d: Test Desc D',
            'image_a: test_image_a.jpg',
            'image_b: test_image_b.jpg',
        ];
        $result = $this->withSession($this->sessionData)->call('get', 'konten/sejarah');
        $result->assertOK();
        $result->assertSee('Ubah Info Sejarah', 'h1');
        $result->assertSeeElement('input[name=title_a]');
        $result->assertSeeElement('input[name=desc_a]');
        $result->assertSeeElement('input[name=title_b]');
        $result->assertSeeElement('input[name=desc_b]');
        $result->assertSeeElement('input[name=title_c]');
        $result->assertSeeElement('input[name=desc_c]');
        $result->assertSeeElement('input[name=title_d]');
        $result->assertSeeElement('input[name=desc_d]');
        $result->assertSeeElement('input[name=image_a]');
        $result->assertSeeElement('input[name=image_b]');

        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', 'konten/sejarah', 'Content\History::index']
        ])->withSession(
            $this->sessionData
        )->call('post', 'konten/sejarah', [
            csrf_token() => csrf_hash(),
            '_method' => 'PUT',
            'title_a' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
            'desc_a' => 'Test Desc A',
            'title_b' => 'Test Title B',
            'desc_b' => 'Test Desc B',
            'title_c' => 'Test Title C',
            'desc_c' => 'Test Desc C',
            'title_d' => 'Test Title D',
            'desc_d' => 'Test Desc D',
        ]);
        $validationError = service('validation')->getError('title_a');
        $result->isTrue($validationError === "Kolom Url Call to Action harus berisi sebuah URL yang valid.", $validationError);
        $this->tc['actual'] = "Menampilkan pesan " . $validationError;
    }
}
