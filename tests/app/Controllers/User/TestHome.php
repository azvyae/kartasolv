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
    }

    public function testRestrictAccessIfNotLoggedIn()
    {
        $result = $this->call('get', "dasbor");
        $result->assertOK();
        $result->assertSessionHas('message', 'Kamu tidak dapat mengakses halaman tersebut!');
        $result->assertRedirectTo(base_url('masuk'));
    }

    public function testPrivateImageNotFound()
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
    }

    public function testLoadPrivateImages()
    {
        $result = $this->withSession($this->sessionData)->call('get', "gambar-privat?", ['q' => 'uploads/default.webp']);
        $result->assertOK();
        $result->assertHeader('Content-Type', 'image/webp; charset=UTF-8');
    }

    public function testRestrictAccessingPrivateDirectory()
    {
        $result = $this->withSession($this->sessionData)->call('get', "gambar-privat?", ['q' => '../../uploads/default.webp']);
        $result->assertOK();
        $result->assertRedirectTo(base_url('gambar-privat?q=%2fuploads%2fdefault.webp'));
    }
}
