<?php

namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TestAuth extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    protected $builder, $db, $uuid, $attempt, $sessionData;
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
        $result = $this->withSession($this->sessionData)->call('get', "keluar");
        $result->assertOK();
        $result->assertRedirectTo(base_url('masuk'));
    }

    public function testForgetPassword()
    {
        $result = $this->call('get', "lupa-kata-sandi");
        $result->assertOK();
        $result->assertSee('Lupa Kata Sandi', 'h1');
        $result->assertSeeElement('input[name=user_email]');
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('users');
        $date = strtotime('+30 minutes', date('Y-m-d H:i:s'));
        $updateData = [
            'user_reset_password' => $date
        ];
        $this->uuid = encode(1, 'userId');
        $this->uuid = encode($date, 'ResetPassword');
        $this->builder->where('user_id', 1);
        $this->builder->update($updateData);
    }

    public function testResetPassword()
    {
        $result = $this->call('get', "atur-ulang-kata-sandi", ['uuid' => $this->uuid, 'attempt' => $this->attempt]);
        $result->assertOK();
        $result->assertSee('Atur Ulang Kata Sandi', 'h1');
        $result->assertSeeElement('input[name=user_password]');
        $result->assertSeeElement('input[name=verify_user_password]');
        $updateData = [
            'user_reset_password' => null
        ];
        $this->builder->where('user_id', 1);
        $this->builder->update($updateData);
    }
}
