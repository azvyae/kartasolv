<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\DOMParser;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox TS-01 Cek fungsi Mengubah akun/profil
 */
class Scenario01Test extends CIUnitTestCase
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
            'scenario' => 'TS-01',
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
     * @testdox TC-01 Menampilkan halaman dasbor
     */
    public function testDashboardPage()
    {
        $this->tc['case_code'] = 'TC-01';
        $this->tc['case'] = 'Menampilkan halaman dasbor';
        $this->tc['expected'] = "Menampilkan halaman dasbor";
        $this->tc['step'][] = "Masuk ke halaman dasbor";
        $roleName = checkAuth('roleName');
        $result = $this->withSession($this->sessionData)->call('get', 'dasbor');
        $result->assertOK();
        $result->assertSee("Dasbor $roleName", 'h1');
        $result->assertSee("Data PMKS", 'h2');
        $result->assertSee("Data PSKS", 'h2');
        $result->assertSee("Pengurus Aktif", 'h2');
        $domParser = new DOMParser;
        $domParser->withString(service('response')->getBody());
        $checks = [
            $domParser->see("Dasbor $roleName", 'h1'),
            $domParser->see("Data PMKS", 'h2'),
            $domParser->see("Data PSKS", 'h2'),
            $domParser->see("Pengurus Aktif", 'h2'),
        ];
        if (!in_array(false, $checks)) {
            $this->tc['actual'] = "Menampilkan halaman dasbor";
        }
    }

    /**
     * @testdox TC-02 Mengubah data nama
     */
    public function testChangeBasicProfileData()
    {
        $this->tc['case_code'] = 'TC-02';
        $this->tc['case'] = 'Mengubah data nama';
        $this->tc['expected'] = "Menampilkan pesan Berhasil melakukan perubahan.";
        $this->tc['step'][] = "Masuk ke halaman profil";
        $result = $this->withSession($this->sessionData)->call('get', 'profil');
        $result->assertOK();
        $result->assertSee("Ubah Akun/Profil", 'h1');
        $result->assertSeeElement('input[name=user_name]');
        $result->assertSeeElement('input[name=user_email]');
        $result->assertSeeElement('input[name=user_password]');
        $result->assertSeeElement('input[name=user_new_password]');
        $result->assertSeeElement('input[name=password_verify]');
        $this->tc['step'][] = "Ubah data pada kolom nama";
        $this->tc['data'][] = "user_name: User Tester";
        $this->tc['data'][] = "user_temp_mail: test@test.com";
        $this->tc['step'][] = "Tekan tombol simpan";
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withSession(
            $this->sessionData
        )->withRoutes([
            ['post', 'profil', 'User\Profile::index'],
        ])->call('post', 'profil', [
            csrf_token() => csrf_hash(),
            'user_name' => 'User Tester',
            'user_email' => 'test@test.com',
            'user_temp_mail' => 'test@test.com',
            '_method' => 'PUT',
            'g-recaptcha-response' => 'random-token'
        ]);
        $result->assertOK();
        $result->assertSessionHas('message', 'Berhasil melakukan perubahan.');
        $this->tc['actual'] = 'Menampilkan pesan ' . getFlash('message', true);
    }

    /**
     * @testdox TC-03 Mengubah data email dengan validasi gagal
     */
    public function testValidationFails()
    {
        $this->tc['case_code'] = 'TC-03';
        $this->tc['case'] = 'Mengubah data email dengan validasi gagal';
        $this->tc['expected'] = "Menampilkan pesan Kolom Email harus berisi sebuah alamat surel yang valid.";
        $this->tc['step'][] = "Masuk ke halaman profil";
        $this->tc['step'][] = "Ubah data pada kolom email";
        $this->tc['data'][] = "user_name: User Tester";
        $this->tc['data'][] = "user_temp_mail: abcdefg";
        $this->tc['step'][] = "Tekan tombol simpan";
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withSession(
            $this->sessionData
        )->withRoutes([
            ['post', 'profil', 'User\Profile::index'],
        ])->call('post', 'profil', [
            csrf_token() => csrf_hash(),
            'user_name' => 'User Tester',
            'user_email' => 'test@test.com',
            'user_temp_mail' => 'abcdefg',
            '_method' => 'PUT',
            'g-recaptcha-response' => 'random-token'
        ]);
        $result->assertOK();
        $validationError = service('validation')->getError('user_temp_mail');
        $result->isTrue($validationError === "Kolom Email harus berisi sebuah alamat email yang valid.", $validationError);
        $this->tc['actual'] = "Menampilkan pesan $validationError";
    }

    /**
     * @testdox TC-04 Mengubah data email
     */
    public function testNewEmail()
    {
        $this->tc['case_code'] = 'TC-04';
        $this->tc['case'] = 'Mengubah data email';
        $this->tc['expected'] = "Menampilkan pesan Berhasil melakukan perubahan. Silakan cek emailmu untuk verifikasi perubahan email.";
        $this->tc['step'][] = "Masuk ke halaman profil";
        $this->tc['step'][] = "Ubah data pada kolom email";
        $this->tc['data'][] = "user_name: User Test";
        $this->tc['data'][] = "user_temp_mail: new@test.com";
        $this->tc['step'][] = "Tekan tombol simpan";
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withSession(
            $this->sessionData
        )->withRoutes([
            ['post', 'profil', 'User\Profile::index'],
        ])->call('post', 'profil', [
            csrf_token() => csrf_hash(),
            'user_name' => 'User Test',
            'user_email' => 'test@test.com',
            'user_temp_mail' => 'new@test.com',
            '_method' => 'PUT',
            'g-recaptcha-response' => 'random-token'
        ]);
        $result->assertOK();
        $result->assertRedirectTo(base_url('profil'));
        $result->assertSessionHas('message', 'Berhasil melakukan perubahan. Silakan cek emailmu untuk verifikasi perubahan email.');
        $this->tc['actual'] = 'Menampilkan pesan ' . getFlash('message', true);
    }

    /**
     * @testdox TC-05 Verifikasi email ketika sudah log in
     */
    public function testVerifyEmail()
    {
        $this->tc['case_code'] = 'TC-05';
        $this->tc['case'] = 'Verifikasi email ketika sudah log in';
        $this->tc['expected'] = "Diarahkan ke halaman " . base_url('profil') . " dengan menampilkan pesan Berhasil mengubah email.";
        $time = date('Y-m-d H:i:s', strtotime('+30 minutes', time()));
        $attempt = encode(strtotime($time), 'changeEmail');
        $uuid = encode(2, 'changeEmail');
        $this->db->table('users')->update(['user_change_mail' => $time], ['user_id' => 2]);
        $this->tc['step'][] = "Masuk ke halaman Verifikasi Perubahan Email";
        $this->tc['data'] = [
            "uuid: $uuid",
            "attempt: $attempt",
        ];
        $result = $this->withSession($this->sessionData)->call('get', 'verifikasi', ['uuid' => $uuid, 'attempt' => $attempt]);
        $result->assertRedirectTo(base_url('profil'));
        $result->assertSessionHas('message', 'Berhasil mengubah email.');
        $this->db->table('users')->update(['user_email' => 'test@test.com'], ['user_id' => 2]);
        $this->tc['actual'] = "Diarahkan ke halaman " . $result->getRedirectUrl() . " dengan menampilkan pesan " . getFlash('message', true);
    }

    /**
     * @testdox TC-06 Verifikasi email ketika belum log in dan membatalkannya
     */
    public function testCancelVerifyEmail()
    {
        $this->tc['case_code'] = 'TC-06';
        $this->tc['case'] = 'Verifikasi email ketika belum log in dan membatalkannya';
        $time = date('Y-m-d H:i:s', strtotime('+30 minutes', time()));
        $attempt = encode(strtotime($time), 'changeEmail');
        $uuid = encode(2, 'changeEmail');
        $this->tc['expected'] = "Diarahkan ke halaman " . base_url("verifikasi?uuid=$uuid&attempt=$attempt&cancel=1") . ' lalu menampilkan pesan Berhasil membatalkan perubahan email.';
        $this->tc['step'][] = "Masuk ke halaman Verifikasi Perubahan Email";
        $this->tc['step'][] = "Mengisi form data log in";
        $this->tc['step'][] = "Menekan tombol masuk";
        $this->db->table('users')->update(['user_change_mail' => $time], ['user_id' => 2]);
        $this->tc['data'] = [
            'user_email: test@test.com',
            'user_password: testpassword',
        ];
        session()->setTempdata('verifyEmail', objectify([
            'uuid' => $uuid,
            'attempt' => $attempt,
            'cancel' => 1
        ]));
        $result = $this->withSession()->call('post', 'masuk', [csrf_token() => csrf_hash(), 'user_email' => 'test@test.com', 'user_password' => 'testpassword', 'g-recaptcha-response' => 'random-token']);
        $result->assertRedirectTo(base_url("verifikasi?uuid=$uuid&attempt=$attempt&cancel=1"));
        $redirection = $result->getRedirectUrl();
        $result = $this->withSession($this->sessionData)->call('get', 'verifikasi', ['uuid' => $uuid, 'attempt' => $attempt, 'cancel' => 1]);
        $result->assertSessionHas('message', 'Berhasil membatalkan perubahan email.');
        $this->tc['actual'] = "Diarahkan ke halaman " . $redirection . " lalu menampilkan pesan " . getFlash('message', true);
    }

    /**
     * @testdox TC-07 Mencoba verifikasi email yang kadaluarsa
     */
    public function testExpiredVerifyEmail()
    {
        $this->tc['case_code'] = 'TC-07';
        $this->tc['case'] = 'Mencoba verifikasi email yang kadaluarsa';
        $this->tc['expected'] = "Diarahkan ke halaman " . base_url('profil') . " dengan menampilkan pesan Link tidak valid/kadaluarsa.";
        $time = '2022-01-18 22:22:48';
        $attempt = encode(strtotime($time), 'changeEmail');
        $uuid = encode(2, 'changeEmail');
        $this->db->table('users')->update(['user_change_mail' => $time], ['user_id' => 2]);
        $this->tc['step'][] = "Masuk ke halaman Verifikasi Perubahan Email";
        $this->tc['data'] = [
            "uuid: $uuid",
            "attempt: $attempt",
        ];
        $result = $this->withSession($this->sessionData)->call('get', 'verifikasi', ['uuid' => $uuid, 'attempt' => $attempt]);
        $result->assertRedirectTo(base_url('profil'));
        $message = getFlash('message', true);
        $result->assertSessionHas('message', 'Link tidak valid/kadaluarsa.');
        $this->tc['actual'] = "Diarahkan ke halaman " . $result->getRedirectUrl() . " dengan menampilkan pesan $message";
    }

    /**
     * @testdox TC-08 Verifikasi email pengguna yang tidak terdaftar
     */
    public function testUnknownVerifyEmail()
    {
        $this->tc['case_code'] = 'TC-08';
        $this->tc['case'] = 'Verifikasi email pengguna yang tidak terdaftar';
        $this->tc['expected'] = "Menampilkan pesan Pengguna tidak ditemukan.";
        $time = '2022-01-18 22:22:48';
        $attempt = encode(strtotime($time), 'changeEmail');
        $uuid = encode(999, 'changeEmail');
        $this->tc['step'][] = "Masuk ke halaman Verifikasi Perubahan Email";
        $this->tc['data'] = [
            "uuid: $uuid",
            "attempt: $attempt",
        ];
        $result = $this->withSession($this->sessionData)->call('get', 'verifikasi', ['uuid' => $uuid, 'attempt' => $attempt]);
        $result->assertSessionHas('message', 'Pengguna tidak ditemukan.');
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * @testdox TC-09 Mengubah kata sandi
     */
    public function testChangePassword()
    {
        $this->tc['case_code'] = 'TC-09';
        $this->tc['case'] = 'Mengubah kata sandi';
        $this->tc['expected'] = "Menampilkan pesan Berhasil melakukan perubahan. Kata sandi berhasil diubah.";
        $this->tc['step'] = [
            'Masuk ke halaman profil',
            'Ubah data pada kolom kata sandi',
            'Tekan tombol simpan',
        ];
        $this->tc['data'] = [
            "user_name: User Test",
            "user_temp_mail: new@test.com",
            "user_password: testpassword",
            "user_new_password: testpassword",
            "password_verify: testpassword",
        ];
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withSession(
            $this->sessionData
        )->withRoutes([
            ['post', 'profil', 'User\Profile::index'],
        ])->call('post', 'profil', [
            csrf_token() => csrf_hash(),
            'user_name' => 'User Test',
            'user_email' => 'test@test.com',
            'user_temp_mail' => 'test@test.com',
            'user_password' => 'testpassword',
            'user_new_password' => 'testpassword',
            'password_verify' => 'testpassword',
            '_method' => 'PUT',
            'g-recaptcha-response' => 'random-token'
        ]);
        $result->assertOK();
        $result->assertSessionHas('message', 'Berhasil melakukan perubahan. Kata sandi berhasil diubah.');
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * @testdox TC-10 Mengubah kata sandi dengan kata sandi salah
     */
    public function testChangePasswordWithWrongPassword()
    {
        $this->tc['case_code'] = 'TC-10';
        $this->tc['case'] = 'Mengubah kata sandi dengan kata sandi salah';
        $this->tc['expected'] = "Menampilkan pesan Kata sandi salah.";
        $this->tc['step'] = [
            'Masuk ke halaman profil',
            'Ubah data pada kolom kata sandi',
            'Tekan tombol simpan',
        ];
        $this->tc['data'] = [
            "user_name: User Test",
            "user_temp_mail: new@test.com",
            "user_password: wrongpassword",
            "user_new_password: testpassword",
            "password_verify: testpassword",
        ];
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withSession(
            $this->sessionData
        )->withRoutes([
            ['post', 'profil', 'User\Profile::index'],
        ])->call('post', 'profil', [
            csrf_token() => csrf_hash(),
            'user_name' => 'User Test',
            'user_email' => 'test@test.com',
            'user_temp_mail' => 'test@test.com',
            'user_password' => 'wrongpassword',
            'user_new_password' => 'testpassword',
            'password_verify' => 'testpassword',
            '_method' => 'PUT',
            'g-recaptcha-response' => 'random-token'
        ]);
        $result->assertOK();
        $result->assertSessionHas('message', 'Kata sandi salah.');
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }
}
