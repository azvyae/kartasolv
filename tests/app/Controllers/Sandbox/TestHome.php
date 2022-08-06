<?php

namespace App\Controllers\Sandbox;

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
        session_destroy();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testIndex()
    {
        $result = $this->call('get', 'sandbox');
        $result->assertOK();
        $result->assertStatus(200);
    }
}
