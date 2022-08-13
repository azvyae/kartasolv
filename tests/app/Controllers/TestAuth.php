<?php

namespace App\Controllers;

use App\Models\UsersModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

class TestAuth extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    protected $sessionData, $um;
    protected function setUp(): void
    {
        parent::setUp();
        $this->um = new UsersModel();
        $this->sessionData = [
            'user' => objectify([
                'userId' => 2,
                'roleId' => 1,
                'roleString' => 'admin',
                'roleName' => 'Administrator',
            ])
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
    }

    public function testLoginPage()
    {
        $result = $this->call('get', "masuk");
        $result->assertOK();
        $result->assertSee('Masuk', 'h1');
        $result->assertSeeElement('input[name=user_email]');
        $result->assertSeeElement('input[name=user_password]');
    }

    public function testLoginWithMdfiveHash()
    {
        $data = [
            'user_id' => 2,
            'user_password' => '200ceb26807d6bf99fd6f4f0d1ca54d4'
        ];
        $this->um->save($data);
        $result = $this->call('post', 'masuk', [csrf_token() => csrf_hash(), 'user_email' => 'test@gmail.com', 'user_password' => 'administrator', 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertRedirectTo(base_url('dasbor'));
    }

    public function testLoginWithDefaultHash()
    {
        $result = $this->call('post', 'masuk', [csrf_token() => csrf_hash(), 'user_email' => 'test@gmail.com', 'user_password' => 'administrator', 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertRedirectTo(base_url('dasbor'));
    }

    public function testLoginWithWrongPassword()
    {
        $result = $this->call('post', 'masuk', [csrf_token() => csrf_hash(), 'user_email' => 'test@gmail.com', 'user_password' => 'administratore', 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertSessionHas('message', 'Email atau Kata Sandi Salah!');
        $result->assertRedirectTo(base_url('masuk'));
    }

    public function testLoginValidationFails()
    {
        $result = $this->call('post', 'masuk', [csrf_token() => csrf_hash(), 'user_email' => 'test satu dua', 'user_password' => 'lalawora', 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $validationError = service('validation')->getError('user_email');
        $result->assertTrue($validationError === 'Kolom Email harus berisi sebuah alamat surel yang valid.', $validationError);
        $result->assertRedirectTo(base_url('masuk'));
    }

    public function testAccessLoginPageAfterSessionIsSet()
    {
        $result = $this->withSession($this->sessionData)->call('get', "masuk");
        $result->assertOK();
        $result->assertRedirectTo(base_url('dasbor'));
    }

    public function testLogout()
    {
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', 'keluar', 'Auth::index'],
        ])->withSession($this->sessionData)->call('post', "keluar", ['_method' => "DELETE", csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertRedirectTo(base_url('masuk'));
    }

    public function testLogoutValidationFails()
    {
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', 'keluar', 'Auth::index'],
        ])->withSession($this->sessionData)->call('post', "keluar", ['_method' => "DELETE", csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'fail-token']);
        $result->assertOK();
        $result->assertRedirectTo(base_url());
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
        $result = $this->call('post', "lupa-kata-sandi", ['user_email' => 'test@gmail.com', csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'random-token']);
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
        $result = $this->call('post', "lupa-kata-sandi", ['user_email' => 'test@gmail.com', csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'random-token']);
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
        ])->call('post', $url, [csrf_token() => csrf_hash(), 'user_new_password' => '123456', 'password_verify' => '123456', '_method' => 'PUT', 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertSessionHas('message', 'Berhasil mengubah kata sandi.');
        $result->assertRedirectTo(base_url('masuk'));
    }
}
