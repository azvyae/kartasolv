<?php

namespace App\Controllers\Users;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TestHome extends CIUnitTestCase
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
        $roleName = checkAuth('roleName');
        $result = $this->withSession($this->sessionData)->call('get', 'dasbor');
        $result->assertOK();
        $result->assertSee("Dasbor $roleName", 'h1');
        $result->assertSee("Data PMKS", 'h2');
        $result->assertSee("Data PSKS", 'h2');
        $result->assertSee("Pengurus Aktif", 'h2');
    }
}
