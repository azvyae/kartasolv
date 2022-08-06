<?php

namespace App;

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
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testIndex()
    {
        $result = $this->call('get', ('konten/profil-karang-taruna'));
        $result->assertOK();
        $result->assertSee('Pengaturan Profil Karang Taruna');
    }
    public function testMainInfo()
    {
        $result = $this->call('get', ('konten/profil-karang-taruna/info-utama'));
        $result->assertOK();
        $result->assertSee('Ubah Informasi Utama');
    }
    public function testOurActivities()
    {
        $result = $this->call('get', ('konten/profil-karang-taruna/kegiatan-kami'));
        $result->assertOK();
        $result->assertSee('Ubah Kegiatan Kami');
    }

    public function testMembers()
    {
        $result = $this->call('get', ('konten/profil-karang-taruna/pengurus'));
        $result->assertOK();
        $result->assertSee('Data Pengurus');
    }
    public function testMemberCreate()
    {
        $result = $this->call('get', ("konten/profil-karang-taruna/tambah-pengurus"));
        $result->assertOK();
        $result->assertSee('Tambah Data Pengurus');
    }
    public function testMemberUpdateFound()
    {
        $id = encode('1', 'members');
        $result = $this->call('get', ("konten/profil-karang-taruna/pengurus/$id"));
        $result->assertOK();
        $result->assertSee('Ubah Data Pengurus');
    }
    public function testMemberUpdateNotFound()
    {
        $id = 'not-found';
        $result = $this->call('get', ("konten/profil-karang-taruna/pengurus/$id"));
        $result->assertOK();
        $result->assertSessionHas('message', 'Data Pengurus Tidak Ditemukan!');
        $result->assertRedirectTo(base_url('konten/profil-karang-taruna/pengurus'));
    }
}
