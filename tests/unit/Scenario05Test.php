<?php


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\DOMParser;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox #### TS-05 Cek fungsi melihat profil Karang Taruna
 */
class Scenario05Test extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
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
     * @testdox TC-01 Menampilkan halaman utama dengan eksternal Call to Action
     */
    public function testIndexPageExternalCallToAction()
    {
        $this->tc['expected'] = "Menampilkan halaman utama dengan tombol Call to Action eksternal";
        $this->tc['step'] = ["Mekakses halaman utama"];
        $this->db->table('info_landing')->where('id', 1)->update(
            [
                'cta_url' => 'https://www.google.com',
                'cta_text' => 'Buka Google'
            ]
        );
        $result = $this->call('get', "/");
        $result->assertOK();
        $result->assertSeeElement('a[target=blank]');
        $result->assertSeeElement('header');
        $result->assertSeeElement('main');
        $result->assertSee('Visi & Misi', 'h2');
        $result->assertSee('Kegiatan Kami', 'h2');
        $result->assertSee('Siapa Kami', 'h2');
        $result->assertSeeElement('footer');
        $domParser = new DOMParser;
        if ($domParser->withString(service('response')->getBody())->seeElement('a[target=blank]')) {
            $this->tc['actual'] = "Menampilkan halaman utama dengan tombol Call to Action eksternal";
        }
    }

    /**
     * @testdox TC-02 Menampilkan halaman utama dengan tautan lokal Call to Action
     */
    public function testIndexPageLocalCallToAction()
    {
        $this->tc['expected'] = "Menampilkan halaman utama dengan tombol Call to Action lokal";
        $this->tc['step'] = ["Mekakses halaman utama"];
        $this->db->table('info_landing')->where('id', 1)->update(
            [
                'cta_url' => 'https://kartasarijadi.test/hubungi-kami#kirim-pesan',
                'cta_text' => 'Kirim Pesan'
            ]
        );
        $result = $this->call('get', "/");
        $result->assertOK();
        $result->assertSeeElement('a[target=_self]');
        $domParser = new DOMParser;
        if ($domParser->withString(service('response')->getBody())->seeElement('a[target=_self]')) {
            $this->tc['actual'] = "Menampilkan halaman utama dengan tombol Call to Action lokal";
        }
    }

    /**
     * @testdox TC-03 Menampilkan halaman utama tanpa Call to Action
     */
    public function testIndexNoCallToAction()
    {
        $this->tc['expected'] = "Menampilkan halaman utama tanpa Call to Action";
        $this->tc['step'] = ["Mekakses halaman utama"];
        $this->db->table('info_landing')->where('id', 1)->update(
            [
                'cta_url' => '',
                'cta_text' => ''
            ]
        );
        $result = $this->call('get', "/");
        $result->assertOK();
        $result->assertDontSeeElement('a[target=_self]');
        $domParser = new DOMParser;
        if ($domParser->withString(service('response')->getBody())->dontSeeElement('a[target=_self]')) {
            $this->tc['actual'] = "Menampilkan halaman utama tanpa Call to Action";
        }
    }

    /**
     * @testdox TC-04 Menampilkan Sitemap
     */
    public function testAccessSitemap()
    {
        $this->tc['expected'] = "Menampilkan XML sitemap";
        $this->tc['step'] = ["Akses ke halaman sitemap"];
        $result = $this->call('get', 'sitemap');
        $result->assertOk();
        $result->assertHeader('Content-Type', 'application/xml; charset=ISO-8859-1');
        if (Services::response()->header('Content-Type')->getValue() === 'application/xml; charset=ISO-8859-1') {
            $this->tc['actual'] = "Menampilkan XML sitemap";
        }
    }
}
