<?php

namespace App\Controllers\Users;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TestProfile extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $session = session();
        $sessionData = [
            'user' => objectify([
                'userId' => 1,
                'roleId' => 1,
                'roleString' => 'admin',
                'roleName' => 'Administrator',
            ])
        ];

        $session->set($sessionData);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        session_destroy();
    }

    public function testIndex()
    {
        $result = $this->call('get', 'profil');
        $result->assertOK();
        $result->assertSee("Ubah Akun/Profil", 'h1');
        $result->assertSeeElement('input[name=user_name]');
        $result->assertSeeElement('input[name=user_image]');
        $result->assertSeeElement('input[name=user_email]');
        $result->assertSeeElement('input[name=user_old_password]');
        $result->assertSeeElement('input[name=user_new_password]');
        $result->assertSeeElement('input[name=user_verify_password]');
    }
}
