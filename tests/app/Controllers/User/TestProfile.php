<?php

namespace App\Controllers\Users;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TestProfile extends CIUnitTestCase
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

    public function testIndex()
    {
        $result = $this->withSession($this->sessionData)->call('get', 'profil');
        $result->assertOK();
        $result->assertSee("Ubah Akun/Profil", 'h1');
        $result->assertSeeElement('input[name=user_name]');
        $result->assertSeeElement('input[name=user_email]');
        $result->assertSeeElement('input[name=user_password]');
        $result->assertSeeElement('input[name=user_new_password]');
        $result->assertSeeElement('input[name=password_verify]');
    }
}
