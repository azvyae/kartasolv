<?php


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;
use Config\Services;

class Scenario05Test extends CIUnitTestCase
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

    public function testIndexPageExternalCallToAction()
    {
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
    }

    public function testIndexPageLocalCallToAction()
    {
        $this->db->table('info_landing')->where('id', 1)->update(
            [
                'cta_url' => 'https://kartasarijadi.test/hubungi-kami#kirim-pesan',
                'cta_text' => 'Kirim Pesan'
            ]
        );
        $result = $this->call('get', "/");
        $result->assertOK();
        $result->assertSeeElement('a[target=_self]');
    }

    public function testIndexNoCallToAction()
    {
        $this->db->table('info_landing')->where('id', 1)->update(
            [
                'cta_url' => '',
                'cta_text' => ''
            ]
        );
        $result = $this->call('get', "/");
        $result->assertOK();
        $result->assertDontSeeElement('a[target=_self]');
    }

    public function testContactUsPage()
    {
        $result = $this->call('get', "hubungi-kami");
        $result->assertOK();
        $result->assertSee('Hubungi Kami', 'h1');
        $result->assertSeeElement('input[name=message_sender]');
        $result->assertSeeElement('input[name=message_whatsapp]');
        $result->assertSeeElement('select[name=message_type]');
        $result->assertSeeElement('textarea[name=message_text]');
        $result->assertSeeElement('footer');
    }

    public function testSendMessageWhatsappLeadingZero()
    {
        $result = $this->call('post', 'hubungi-kami', [csrf_token() => csrf_hash(), 'message_sender' => 'Test Sender', 'message_whatsapp' => '0812345678', 'message_text' => 'Test Message', 'message_type' => 'Kritik & Saran', 'g-recaptcha-response' => 'random-token']);
        $result->assertOk();
        $result->assertSessionHas('message', 'Berhasil mengirimkan pesan.');
    }

    public function testSendMessageWhatsappLeadingEight()
    {
        $result = $this->call('post', 'hubungi-kami', [csrf_token() => csrf_hash(), 'message_sender' => 'Test Sender', 'message_whatsapp' => '812345678', 'message_text' => 'Test Message', 'message_type' => 'Kritik & Saran', 'g-recaptcha-response' => 'random-token']);
        $result->assertOk();
        $result->assertSessionHas('message', 'Berhasil mengirimkan pesan.');
    }

    public function testSendMessageWhatsappPlusNumber()
    {
        $result = $this->call('post', 'hubungi-kami', [csrf_token() => csrf_hash(), 'message_sender' => 'Test Sender', 'message_whatsapp' => '+62812345678', 'message_text' => 'Test Message', 'message_type' => 'Kritik & Saran', 'g-recaptcha-response' => 'random-token']);
        $result->assertOk();
        $result->assertSessionHas('message', 'Berhasil mengirimkan pesan.');
    }

    public function testSendMessageWrongWhatsappFormat()
    {
        $result = $this->call('post', 'hubungi-kami', [csrf_token() => csrf_hash(), 'message_sender' => 'Test Sender', 'message_whatsapp' => '182935678', 'message_text' => 'Test Message', 'message_type' => 'Kritik & Saran', 'g-recaptcha-response' => 'random-token']);
        $result->assertOk();
        $result->assertRedirectTo(base_url('hubungi-kami'));
    }

    public function testAccessSitemap()
    {
        $result = $this->call('get', 'sitemap');
        $result->assertOk();
        $result->assertHeader('Content-Type', 'application/xml; charset=ISO-8859-1');
    }
}
