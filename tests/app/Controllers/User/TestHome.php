<?php

namespace App\Controllers\Users;

use CodeIgniter\Exceptions\PageNotFoundException;
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

    public function testNotFoundPage()
    {
        $sessionData = [
            'user' => objectify([
                'userId' => 2,
                'roleId' => 1,
                'roleString' => 'admin',
                'roleName' => 'Administrator',
            ])
        ];
        $this->expectException(PageNotFoundException::class);
        $result = $this->withSession($sessionData)->call('get', 'gambar-privat?q=notfound.xml');
        $result->assertStatus(404);
        if (!isset($_SESSION))
            session_start();
        if (session_status() === PHP_SESSION_ACTIVE)
            session_destroy();
    }

    public function testRestrictAccessIfNotLoggedIn()
    {
        $result = $this->call('get', "dasbor");
        $result->assertOK();
        $result->assertSessionHas('message', 'Kamu tidak dapat mengakses halaman tersebut!');
        $result->assertRedirectTo(base_url('masuk'));
    }
}
