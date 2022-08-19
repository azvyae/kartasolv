<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

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
     * @testdox TC-01 Mengubah kata sandi
     */
    public function testChangePassword()
    {
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
     * @testdox TC-02 Mengubah kata sandi dengan kata sandi salah
     */
    public function testChangePasswordWithWrongPassword()
    {
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

    public function testForgetPasswordPage()
    {
        $result = $this->call('get', "lupa-kata-sandi");
        $result->assertOK();
        $result->assertSee('Lupa Kata Sandi', 'h1');
        $result->assertSeeElement('input[name=user_email]');
    }

    public function testForgetPasswordValidationFails()
    {
        $result = $this->call('post', "lupa-kata-sandi", [csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $validationError = service('validation')->getError('user_email');
        $result->assertTrue($validationError === 'Kolom Email harus diisi.', $validationError);
        $result->assertRedirectTo(base_url('lupa-kata-sandi'));
    }

    public function testForgetPasswordWithWrongEmail()
    {
        $result = $this->call('post', "lupa-kata-sandi", ['user_email' => 'lala@gmail.com', csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertSessionHas('message', 'Email yang kamu tulis tidak ditemukan!');
        $result->assertRedirectTo(base_url('lupa-kata-sandi'));
    }

    public function testForgetPasswordWithCorrectEmail()
    {
        $result = $this->call('post', "lupa-kata-sandi", ['user_email' => 'test@test.com', csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertSessionHas('message', 'Silakan cek emailmu untuk melanjutkan.');
        $result->assertRedirectTo(base_url('masuk'));
    }

    public function testForgetPasswordWithRecentlyRequestedAttempt()
    {
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
    }

    public function testResetPasswordPage()
    {
        $date = date('Y-m-d H:i:s', strtotime('+15 minutes', time()));
        $updateData = [
            'user_id' => 2,
            'user_reset_attempt' => $date
        ];
        $this->um->save($updateData);
        $uuid = encode(2, 'resetPassword');
        $attempt = encode(strtotime($date), 'resetPassword');
        $result = $this->call('get', "atur-ulang-kata-sandi?uuid=$uuid&attempt=$attempt");
        $result->assertOK();
        $result->assertSee('Atur Ulang Kata Sandi', 'h1');
        $result->assertSeeElement('input[name=user_new_password]');
        $result->assertSeeElement('input[name=password_verify]');
    }

    public function testResetPasswordUserNotFound()
    {
        $date = date('Y-m-d H:i:s', strtotime('+15 minutes', time()));
        $uuid = encode(999, 'resetPassword');
        $attempt = encode(strtotime($date), 'resetPassword');
        $result = $this->call('get', "atur-ulang-kata-sandi?uuid=$uuid&attempt=$attempt");
        $result->assertOK();
        $result->assertSessionHas('message', 'Pengguna tidak ditemukan.');
        $result->assertRedirectTo(base_url('lupa-kata-sandi'));
    }

    public function testExpiredResetPassword()
    {
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
    }

    public function testResetPasswordValidationFails()
    {
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
    }

    public function testResetPassword()
    {
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
    }
}
