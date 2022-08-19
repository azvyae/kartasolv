<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox #### TS-02 Cek fungsi mengatur ulang kata sandi
 */
class Scenario02Test extends CIUnitTestCase
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
            'testStep' => [],
            'testData' => [],
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
     * @testdox TC-01 Mengirim formulir lupa kata sandi tanpa data yang diinput
     */
    public function testForgetPasswordValidationFails()
    {
        $this->tc['expected'] = "Menampilkan pesan Kolom Email harus diisi.";
        $result = $this->call('get', "lupa-kata-sandi");
        $result->assertOK();
        $result->assertSee('Lupa Kata Sandi', 'h1');
        $result->assertSeeElement('input[name=user_email]');
        $this->tc['step'] = [
            'Masuk ke halaman lupa kata sandi',
            'Tekan tombol lupa kata sandi',
        ];
        $result = $this->call('post', "lupa-kata-sandi", [csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $validationError = service('validation')->getError('user_email');
        $result->assertTrue($validationError === 'Kolom Email harus diisi.', $validationError);
        $result->assertRedirectTo(base_url('lupa-kata-sandi'));
        $this->tc['actual'] = "Menampilkan pesan $validationError";
    }

    /**
     * @testdox TC-02 Mengirim formulir lupa kata sandi tanpa data yang diinput
     */
    public function testForgetPasswordWithWrongEmail()
    {
        $this->tc['expected'] = "Menampilkan pesan Email yang kamu tulis tidak ditemukan!";
        $this->tc['step'] = [
            'Masuk ke halaman lupa kata sandi',
            'Isi kolom email',
            'Tekan tombol lupa kata sandi',
        ];
        $this->tc['data'][] = 'user_email: notfound@gmail.com';
        $result = $this->call('post', "lupa-kata-sandi", ['user_email' => 'notfound@gmail.com', csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertSessionHas('message', 'Email yang kamu tulis tidak ditemukan!');
        $result->assertRedirectTo(base_url('lupa-kata-sandi'));
        $this->tc['actual'] = 'Menampilkan pesan ' . getFlash('message', true);
    }

    /**
     * @testdox TC-03 Mengisi formulir lupa kata sandi dengan benar
     */
    public function testForgetPasswordWithCorrectEmail()
    {
        $this->tc['expected'] = "Menampilkan pesan Silakan cek emailmu untuk melanjutkan.";
        $this->tc['step'] = [
            'Masuk ke halaman lupa kata sandi',
            'Isi kolom email',
            'Tekan tombol lupa kata sandi',
        ];
        $this->tc['data'][] = 'user_email: test@test.com';
        $result = $this->call('post', "lupa-kata-sandi", ['user_email' => 'test@test.com', csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertSessionHas('message', 'Silakan cek emailmu untuk melanjutkan.');
        $result->assertRedirectTo(base_url('masuk'));
        $this->tc['actual'] = 'Menampilkan pesan ' . getFlash('message', true);
    }

    /**
     * @testdox TC-04 Meminta perubahan kata sandi berulang kali
     */
    public function testForgetPasswordWithRecentlyRequestedAttempt()
    {
        $this->tc['expected'] = "Menampilkan pesan Kamu baru saja melakukan permintaan atur ulang kata sandi, tunggu 5 menit lagi.";
        $this->tc['step'] = [
            'Masuk ke halaman lupa kata sandi',
            'Isi kolom email',
            'Tekan tombol lupa kata sandi',
        ];
        $this->tc['data'][] = 'user_email: test@test.com';
        $date = date('Y-m-d H:i:s', strtotime('+15 minutes', time()));
        $updateData = [
            'user_id' => 2,
            'user_reset_attempt' => $date
        ];
        $this->um->save($updateData);
        $result = $this->call('post', "lupa-kata-sandi", ['user_email' => 'test@test.com', csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertSessionHas('message', 'Kamu baru saja melakukan permintaan atur ulang kata sandi, tunggu 5 menit lagi.');
        $result->assertRedirectTo(base_url('lupa-kata-sandi'));
        $this->tc['actual'] = 'Menampilkan pesan ' . getFlash('message', true);
    }

    /**
     * @testdox TC-05 Masuk ke halaman atur ulang kata sandi, pengguna tidak ditemukan
     */
    public function testResetPasswordUserNotFound()
    {
        $this->tc['expected'] = "Menampilkan pesan Pengguna tidak ditemukan.";
        $this->tc['step'] = [
            'Masuk ke halaman atur ulang kata sandi',
        ];
        $date = date('Y-m-d H:i:s', strtotime('+15 minutes', time()));
        $uuid = encode(999, 'resetPassword');
        $attempt = encode(strtotime($date), 'resetPassword');
        $result = $this->call('get', "atur-ulang-kata-sandi?uuid=$uuid&attempt=$attempt");
        $result->assertOK();
        $result->assertSessionHas('message', 'Pengguna tidak ditemukan.');
        $result->assertRedirectTo(base_url('lupa-kata-sandi'));
        $this->tc['actual'] = 'Menampilkan pesan ' . getFlash('message', true);
    }

    /**
     * @testdox TC-06 Masuk ke halaman atur ulang kata sandi, kadaluarsa
     */
    public function testExpiredResetPassword()
    {
        $this->tc['expected'] = "Menampilkan pesan Link tidak valid/kadaluarsa.";
        $this->tc['step'] = [
            'Masuk ke halaman atur ulang kata sandi',
        ];
        $date = date('Y-m-d H:i:s', strtotime('-15 minutes', time()));
        $updateData = [
            'user_id' => 2,
            'user_reset_attempt' => $date
        ];
        $this->um->save($updateData);
        $uuid = encode(2, 'resetPassword');
        $attempt = encode(strtotime($date), 'resetPassword');
        $result = $this->call('get', "atur-ulang-kata-sandi?uuid=$uuid&attempt=$attempt");
        $result->assertOK();
        $result->assertSessionHas('message', 'Link tidak valid/kadaluarsa.');
        $result->assertRedirectTo(base_url('lupa-kata-sandi'));
        $this->tc['actual'] = 'Menampilkan pesan ' . getFlash('message', true);
    }

    /**
     * @testdox TC-07 Gagal atur ulang kata sandi
     */
    public function testResetPasswordValidationFails()
    {
        $this->tc['expected'] = "Menampilkan pesan Kolom Kata Sandi Baru harus diisi.";
        $this->tc['step'] = [
            'Masuk ke halaman atur ulang kata sandi',
            'Mengisi formulir perubahan kata sandi',
            'Tekan tombol simpan',
        ];
        $this->tc['data'] = [
            'user_new_password: (kosong)',
            'password_verify: 123456'
        ];
        $date = date('Y-m-d H:i:s', strtotime('+15 minutes', time()));
        $updateData = [
            'user_id' => 2,
            'user_reset_attempt' => $date
        ];
        $this->um->save($updateData);
        $uuid = encode(2, 'resetPassword');
        $attempt = encode(strtotime($date), 'resetPassword');
        $url = "atur-ulang-kata-sandi?uuid=$uuid&attempt=$attempt";
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', 'atur-ulang-kata-sandi', 'Auth::resetPassword'],
        ])->call('post', $url, [csrf_token() => csrf_hash(), 'user_new_password' => '', 'password_verify' => '123456', '_method' => 'PUT', 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $validationError = service('validation')->getError('user_new_password');
        $result->assertTrue($validationError === 'Kolom Kata Sandi Baru harus diisi.', $validationError);
        $result->assertRedirectTo(base_url($url));
        $this->tc['actual'] = "Menampilkan pesan $validationError";
    }

    /**
     * @testdox TC-08 Atur ulang kata sandi
     */
    public function testResetPassword()
    {
        $this->tc['expected'] = "Menampilkan pesan Berhasil mengubah kata sandi.";
        $this->tc['step'] = [
            'Masuk ke halaman atur ulang kata sandi',
            'Mengisi formulir perubahan kata sandi',
            'Tekan tombol simpan',
        ];
        $this->tc['data'] = [
            'user_new_password: testpassword',
            'password_verify: testpassword'
        ];
        $date = date('Y-m-d H:i:s', strtotime('+15 minutes', time()));
        $updateData = [
            'user_id' => 2,
            'user_reset_attempt' => $date
        ];
        $this->um->save($updateData);
        $uuid = encode(2, 'resetPassword');
        $attempt = encode(strtotime($date), 'resetPassword');
        $url = "atur-ulang-kata-sandi?uuid=$uuid&attempt=$attempt";
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', 'atur-ulang-kata-sandi', 'Auth::resetPassword']
        ])->call('post', $url, [csrf_token() => csrf_hash(), 'user_new_password' => 'testpassword', 'password_verify' => 'testpassword', '_method' => 'PUT', 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertSessionHas('message', 'Berhasil mengubah kata sandi.');
        $result->assertRedirectTo(base_url('masuk'));
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message');
    }
}
