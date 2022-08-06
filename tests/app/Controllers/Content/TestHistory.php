<?php

namespace App\Controllers\Content;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class TestHistory extends CIUnitTestCase
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
        $result = $this->withSession($this->sessionData)->call('get', 'konten/sejarah');
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
