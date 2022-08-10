<?php

namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TestAuth extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    protected $sessionData;
    protected function setUp(): void
    {
        parent::setUp();
        $this->sessionData = [
            'user' => objectify([
                'userId' => 1,
                'roleId' => 1,
                'roleString' => 'admin',
                'roleName' => 'Administrator',
            ])
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testLogin()
    {
        $result = $this->call('get', "masuk");
        $result->assertOK();
        $result->assertSee('Masuk', 'h1');
        $result->assertSeeElement('input[name=user_email]');
        $result->assertSeeElement('input[name=user_password]');
    }
    public function testAfterLoggedIn()
    {
        $result = $this->withSession($this->sessionData)->call('get', "masuk");
        $result->assertOK();
        $result->assertRedirectTo(base_url('dasbor'));
    }

    public function testLogout()
    {

        $result = $this->withSession($this->sessionData)->call('delete', "keluar", [csrf_token() => csrf_hash()]);
        $result->assertOK();
        $result->assertRedirectTo(base_url('masuk'));
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
