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
        $result = $this->withSession($this->sessionData)->call('get', 'konten/profil-karang-taruna');
        $result->assertOK();
        $result->assertSee('Pengaturan Profil Karta', 'h1');
        $result->assertSee('Ubah Informasi Utama');
        $result->assertSee('Ubah Kegiatan Kami');
        $result->assertSee('Data Pengurus');
    }
    public function testMainInfo()
    {
        $result = $this->withSession($this->sessionData)->call('get', 'konten/profil-karang-taruna/info-utama');
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
        $result = $this->withSession($this->sessionData)->call('get', 'konten/profil-karang-taruna/kegiatan-kami');
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
        $result = $this->withSession($this->sessionData)->call('get', 'konten/profil-karang-taruna/pengurus');
        $result->assertOK();
        $result->assertSee('Data Pengurus', 'h1');
        $result->assertSeeElement('table');
    }

    public function testMembersAjax()
    {
        $datatablesQuery = [
            'draw' => '1',
            'columns' => [
                [
                    'data' => 'member_name',
                    'name' => 'member_name',
                    'searchable' => 'true',
                    'orderable' => 'true',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
                [
                    'data' => 'member_position',
                    'name' => 'member_position',
                    'searchable' => 'true',
                    'orderable' => 'true',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
                [
                    'data' => 'member_type',
                    'name' => 'member_type',
                    'searchable' => 'true',
                    'orderable' => 'true',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
                [
                    'data' => 'member_active',
                    'name' => 'member_active',
                    'searchable' => 'true',
                    'orderable' => 'true',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
                [
                    'data' => 'unique_id',
                    'name' => 'unique_id',
                    'searchable' => 'true',
                    'orderable' => 'false',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
            ],
            'order' => [
                [
                    'column' => '0',
                    'dir' => 'asc',
                ],
            ],
            'start' => '0',
            'length' => '10',
            'search' => [
                'value' => '',
                'regex' => 'false',
            ],
            'orderable' => [
                'member_name',
                'member_position',
                'member_type',
                'member_active',
            ],
            'searchable' => [
                'member_name',
                'member_position',
                'member_type',
                'member_active',
            ],
            '_' => '1660301593638',
        ];
        $headers = [
            'X-Requested-With' => 'XMLHttpRequest'
        ];
        $result = $this->withSession($this->sessionData)->withBodyFormat('json')->withHeaders($headers)->call('get', 'konten/profil-karang-taruna/pengurus', $datatablesQuery);
        $result->assertOK();
        $result->assertTrue($result->getJSON() !== false);
    }

    public function testMemberCreate()
    {
        $result = $this->withSession($this->sessionData)->call('get', "konten/profil-karang-taruna/pengurus/tambah");
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
        $result = $this->withSession($this->sessionData)->call('get', "konten/profil-karang-taruna/pengurus/$id");
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
        $result = $this->withSession($this->sessionData)->call('get', "konten/profil-karang-taruna/pengurus/$id");
        $result->assertOK();

        // $result->expectExceptionMessage('Page Not Found');
    }
}
