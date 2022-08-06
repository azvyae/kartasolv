<?php

namespace App\Controllers\Users;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TestHome extends CIUnitTestCase
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
        $roleName = checkAuth('roleName');
        $result = $this->call('get', 'dasbor');
        $result->assertOK();
        $result->assertSee("Dasbor $roleName", 'h1');
        $result->assertSee("Data PMKS", 'strong');
        $result->assertSee("Data PSKS", 'strong');
        $result->assertSee("Pengurus Aktif", 'strong');
    }
}
