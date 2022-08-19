<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class Scenario04Test extends CIUnitTestCase
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
        $result->assertSee('Ubah Info Sejarah', 'h1');
        $result->assertSeeElement('input[name=title_a]');
        $result->assertSeeElement('input[name=desc_a]');
        $result->assertSeeElement('input[name=title_b]');
        $result->assertSeeElement('input[name=desc_b]');
        $result->assertSeeElement('input[name=title_c]');
        $result->assertSeeElement('input[name=desc_c]');
        $result->assertSeeElement('input[name=title_d]');
        $result->assertSeeElement('input[name=desc_d]');
        $result->assertSeeElement('input[name=image_a]');
        $result->assertSeeElement('input[name=image_b]');
    }
}
