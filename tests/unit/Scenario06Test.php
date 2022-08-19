<?php


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;
use Config\Services;

class Scenario06Test extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    protected $db;
    protected function setUp(): void
    {
        parent::setUp();
        $this->db =  Database::connect();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Services::validation()->reset();
    }

    public function testHistoryPage()
    {
        $result = $this->call('get', "sejarah");
        $result->assertOK();
        $result->assertSeeElement('h1');
        $result->assertSeeElement('footer');
    }
}
