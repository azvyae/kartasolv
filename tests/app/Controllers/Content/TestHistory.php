<?php

namespace App\Controllers\Content;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TestHistory extends CIUnitTestCase
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
        $result = $this->call('get', 'konten/sejarah');
        $result->assertOK();
        $result->assertSee('Pengaturan Sejarah', 'h1');
        $result->assertSeeElement('input[name=title_a]');
        $result->assertSeeElement('input[name=subtitle_a]');
        $result->assertSeeElement('input[name=title_b]');
        $result->assertSeeElement('input[name=subtitle_b]');
        $result->assertSeeElement('input[name=title_c]');
        $result->assertSeeElement('input[name=subtitle_c]');
        $result->assertSeeElement('input[name=title_d]');
        $result->assertSeeElement('input[name=subtitle_d]');
        $result->assertSeeElement('input[name=image_a]');
        $result->assertSeeElement('input[name=image_b]');
    }
}
