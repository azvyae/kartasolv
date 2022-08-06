<?php

declare(strict_types=1);

namespace App\Controllers\Content;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TestOrganizationProfile extends CIUnitTestCase
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
        if (checkAuth('all')) {
            session_destroy();
        }
    }

    public function testIndex()
    {
        $result = $this->call('get', 'konten/profil-karang-taruna');
        $result->assertOK();
        $result->assertSee('Pengaturan Profil Karta', 'h1');
        $result->assertSee('Ubah Informasi Utama');
        $result->assertSee('Ubah Kegiatan Kami');
        $result->assertSee('Data Pengurus');
    }
    public function testMainInfo()
    {
        $result = $this->call('get', 'konten/profil-karang-taruna/info-utama');
        $result->assertOK();
        $result->assertSee('Ubah Informasi Utama', 'h1');
        $result->assertSeeElement('input[name=landing_title]');
        $result->assertSeeElement('input[name=landing_tagline]');
        $result->assertSeeElement('input[name=cta_text]');
        $result->assertSeeElement('input[name=cta_url]');
        $result->assertSeeElement('input[name=vision]');
        $result->assertSeeElement('input[name=landing_image]');
        $result->assertSeeElement('textarea[name=mission]');
    }
    public function testOurActivities()
    {
        $result = $this->call('get', 'konten/profil-karang-taruna/kegiatan-kami');
        $result->assertOK();
        $result->assertSee('Ubah Kegiatan Kami', 'h1');
        $result->assertSeeElement('input[name=title_a]');
        $result->assertSeeElement('input[name=desc_a]');
        $result->assertSeeElement('input[name=image_a]');
        $result->assertSeeElement('input[name=title_b]');
        $result->assertSeeElement('input[name=desc_b]');
        $result->assertSeeElement('input[name=image_b]');
        $result->assertSeeElement('input[name=title_c]');
        $result->assertSeeElement('input[name=desc_c]');
        $result->assertSeeElement('input[name=image_c]');
    }

    public function testMembers()
    {
        $result = $this->call('get', 'konten/profil-karang-taruna/pengurus');
        $result->assertOK();
        $result->assertSee('Data Pengurus', 'h1');
        $result->assertSeeElement('table');
    }
    public function testMemberCreate()
    {
        $result = $this->call('get', "konten/profil-karang-taruna/tambah-pengurus");
        $result->assertOK();
        $result->assertSee('Tambah Data Pengurus', 'h1');
        $result->assertSeeElement('input[name=member_name]');
        $result->assertSeeElement('input[name=member_position]');
        $result->assertSeeElement('select[name=member_type]');
        $result->assertSeeElement('input[name=member_active]');
    }
    public function testMemberUpdateFound()
    {
        $id = encode('1', 'members');
        $result = $this->call('get', "konten/profil-karang-taruna/pengurus/$id");
        $result->assertOK();
        $result->assertSee('Ubah Data Pengurus', 'h1');
        $result->assertSeeElement('input[name=member_name]');
        $result->assertSeeElement('input[name=member_position]');
        $result->assertSeeElement('select[name=member_type]');
        $result->assertSeeElement('input[name=member_active]');
    }
    public function testMemberUpdateNotFound()
    {
        $this->expectException(PageNotFoundException::class);
        $id = 'not-found';
        $result = $this->call('get', "konten/profil-karang-taruna/pengurus/$id");
        $result->assertOK();

        // $result->expectExceptionMessage('Page Not Found');
    }
}
