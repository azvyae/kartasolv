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
        Services::validation()->reset();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (!isset($_SESSION))
            session_start();
        if (session_status() === PHP_SESSION_ACTIVE)
            session_destroy();
            
    }

    /**
     * Try to accessing index page
     */
    public function testLogin()
    {
        $result = $this->call('get', "masuk");
        $result->assertOK();
        $result->assertSee('Masuk', 'h1');
        $result->assertSeeElement('input[name=user_email]');
        $result->assertSeeElement('input[name=user_password]');
        $result->isRedirect();
    }

    /**
     * Try to login with correct credentials
     */
    public function testCorrectLoginMdfive()
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

    /**
     * Try to login with correct credentials
     */
    public function testCorrectLoginHash()
    {
        $result = $this->call('post', 'masuk', [csrf_token() => csrf_hash(), 'user_email' => 'test@gmail.com', 'user_password' => 'administrator', 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertRedirectTo(base_url('dasbor'));
    }

    /**
     * Try to login with bad credentials
     */
    public function testWrongLogin()
    {
        $result = $this->call('post', 'masuk', [csrf_token() => csrf_hash(), 'user_email' => 'test@gmail.com', 'user_password' => 'administratore', 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertRedirectTo(base_url('masuk'));
    }

    /**
     * Try to login with wrong email format
     */
    public function testLoginValidationFails()
    {
        $result = $this->call('post', 'masuk', [csrf_token() => csrf_hash(), 'user_email' => 'test satu dua', 'user_password' => 'lalawora', 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertRedirectTo(base_url('masuk'));
    }

    /**
     * Try to accessing login page after session is set
     */
    public function testAfterLogin()
    {
        $result = $this->withSession($this->sessionData)->call('get', "masuk");
        $result->assertOK();
        $result->assertRedirectTo(base_url('dasbor'));
    }

    /**
     * Try to logout
     */
    public function testLogout()
    {
        $result = $this->withHeaders([
            "Content-Type" => 'application/x-www-form-urlencoded'
        ])->withRoutes([
            ['post', 'keluar', 'Auth::index'],
        ])->withSession($this->sessionData)->call('post', "keluar", ['_method' => "DELETE", csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertRedirectTo(base_url('masuk'));
    }

    /**
     * Try to logout failing
     */
    public function testLogoutFails()
    {
        $result = $this->withHeaders([
            "Content-Type" => 'application/x-www-form-urlencoded'
        ])->withRoutes([
            ['post', 'keluar', 'Auth::index'],
        ])->withSession($this->sessionData)->call('post', "keluar", ['_method' => "DELETE", csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'fail-token']);
        $result->assertOK();
        $result->assertRedirectTo(base_url());
    }

    public function testForgetPassword()
    {
        $result = $this->call('get', "lupa-kata-sandi");
        $result->assertOK();
        $result->assertSee('Lupa Kata Sandi', 'h1');
        $result->assertSeeElement('input[name=user_email]');
    }

    public function testResetPassword()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $date = date('Y-m-d H:i:s', strtotime('+15 minutes', time()));
        $updateData = [
            'user_reset_attempt' => $date
        ];
        $builder->update($updateData, ['user_id' => 1]);
        $uuid = encode(1, 'resetPassword');
        $attempt = encode(strtotime($date), 'resetPassword');
        $result = $this->call('get', "atur-ulang-kata-sandi?uuid=$uuid&attempt=$attempt");
        $result->assertOK();
        $result->assertSee('Atur Ulang Kata Sandi', 'h1');
        $result->assertSeeElement('input[name=user_new_password]');
        $result->assertSeeElement('input[name=password_verify]');
        $updateData = [
            'user_reset_attempt' => null
        ];
        // $builder->update($updateData, ['user_id' => 1]);
    }
}
