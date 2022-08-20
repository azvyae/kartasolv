<?php


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\DOMParser;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox #### TS-06 Cek fungsi melihat sejarah Karang Taruna
 */
class Scenario06Test extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    protected $sessionData, $tc;
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
        $this->tc = [
            'step' => [],
            'data' => [],
            'expected' => '',
            'actual' => ''
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        if (!isset($_SESSION))
            session_start();
        if (session_status() === PHP_SESSION_ACTIVE)
            session_destroy();
        Services::validation()->reset();
        parseTest($this->tc);
        $this->assertTrue($this->tc['expected'] === $this->tc['actual'], "expected: " . $this->tc['expected'] . "\n" . 'actual: ' . $this->tc['actual']);
    }

    /**
     * TC-01 Mengakses halaman Sejarah Karang Taruna
     */
    public function testHistoryPage()
    {
        $this->tc['expected'] = "Menampilkan halaman sejarah Karang Taruna";
        $this->tc['step'][] = "Masuk ke halaman sejarah Karang Taruna";
        $result = $this->call('get', "sejarah");
        $result->assertOK();
        $result->assertSeeElement('h1');
        $result->assertSeeElement('footer');
        $domParser = new DOMParser;
        $domParser->withString(service('response')->getBody());
        $checks = [
            $domParser->seeElement('h1'),
            $domParser->seeElement('footer'),
        ];
        if (!in_array(false, $checks)) {
            $this->tc['actual'] = "Menampilkan halaman sejarah Karang Taruna";
        }
    }
}
