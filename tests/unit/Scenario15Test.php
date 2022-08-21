<?php


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox #### TS-15 Cek fungsi mengirimkan pesan aduan
 */
class Scenario15Test extends CIUnitTestCase
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
            'scenario' => 'TS-15',
            'case_code' => '',
            'case' => '',
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
     * TC-01 Mengirimkan Pesan Aduan dengan no Whatsapp diawali dengan 08..
     */
    public function testContactUsPage()
    {
        $this->tc['expected'] = "Menampilkan pesan Berhasil mengirimkan pesan.";
        $this->tc['step'] = [
            'Masuk ke halaman Hubungi Kami',
            'Isi formulir kirim pesan aduan',
            'Tekan tombol kirim',
        ];
        $this->tc['data'] = [
            'message_sender: Test Sender',
            'message_whatsapp: 0812345678',
            'message_type: Kritik & Saran',
            'message_text: Test Message',
        ];
        $result = $this->call('get', "hubungi-kami");
        $result->assertOK();
        $result->assertSee('Hubungi Kami', 'h1');
        $result->assertSeeElement('input[name=message_sender]');
        $result->assertSeeElement('input[name=message_whatsapp]');
        $result->assertSeeElement('select[name=message_type]');
        $result->assertSeeElement('textarea[name=message_text]');
        $result->assertSeeElement('footer');
        $result = $this->call('post', 'hubungi-kami', [csrf_token() => csrf_hash(), 'message_sender' => 'Test Sender', 'message_whatsapp' => '0812345678', 'message_text' => 'Test Message', 'message_type' => 'Kritik & Saran', 'g-recaptcha-response' => 'random-token']);
        $result->assertOk();
        $result->assertSessionHas('message', 'Berhasil mengirimkan pesan.');
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * TC-02 Mengirimkan Pesan Aduan dengan no Whatsapp diawali dengan 8..
     */
    public function testSendMessageWhatsappLeadingEight()
    {
        $this->tc['expected'] = "Menampilkan pesan Berhasil mengirimkan pesan.";
        $this->tc['step'] = [
            'Masuk ke halaman Hubungi Kami',
            'Isi formulir kirim pesan aduan',
            'Tekan tombol kirim',
        ];
        $this->tc['data'] = [
            'message_sender: Test Sender',
            'message_whatsapp: 812345678',
            'message_type: Kritik & Saran',
            'message_text: Test Message',
        ];
        $result = $this->call('post', 'hubungi-kami', [csrf_token() => csrf_hash(), 'message_sender' => 'Test Sender', 'message_whatsapp' => '812345678', 'message_text' => 'Test Message', 'message_type' => 'Kritik & Saran', 'g-recaptcha-response' => 'random-token']);
        $result->assertOk();
        $result->assertSessionHas('message', 'Berhasil mengirimkan pesan.');
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * TC-03 Mengirimkan Pesan Aduan dengan no Whatsapp diawali dengan +62
     */
    public function testSendMessageWhatsappPlusNumber()
    {
        $this->tc['expected'] = "Menampilkan pesan Berhasil mengirimkan pesan.";
        $this->tc['step'] = [
            'Masuk ke halaman Hubungi Kami',
            'Isi formulir kirim pesan aduan',
            'Tekan tombol kirim',
        ];
        $this->tc['data'] = [
            'message_sender: Test Sender',
            'message_whatsapp: +62812345678',
            'message_type: Kritik & Saran',
            'message_text: Test Message',
        ];
        $result = $this->call('post', 'hubungi-kami', [csrf_token() => csrf_hash(), 'message_sender' => 'Test Sender', 'message_whatsapp' => '+62812345678', 'message_text' => 'Test Message', 'message_type' => 'Kritik & Saran', 'g-recaptcha-response' => 'random-token']);
        $result->assertOk();
        $result->assertSessionHas('message', 'Berhasil mengirimkan pesan.');
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * TC-04 Mengirimkan Pesan Aduan dengan format no Whatsapp salah
     */
    public function testSendMessageWrongWhatsappFormat()
    {
        $this->tc['expected'] = "Menampilkan pesan Nomor Whatsapp Salah!";
        $this->tc['step'] = [
            'Masuk ke halaman Hubungi Kami',
            'Isi formulir kirim pesan aduan',
            'Tekan tombol kirim',
        ];
        $this->tc['data'] = [
            'message_sender: Test Sender',
            'message_whatsapp: +62812345678',
            'message_type: Kritik & Saran',
            'message_text: Test Message',
        ];
        $result = $this->call('post', 'hubungi-kami', [csrf_token() => csrf_hash(), 'message_sender' => 'Test Sender', 'message_whatsapp' => '2082935678', 'message_text' => 'Test Message', 'message_type' => 'Kritik & Saran', 'g-recaptcha-response' => 'random-token']);
        $result->assertOk();
        $result->assertRedirectTo(base_url('hubungi-kami'));
        $validationError = service('validation')->getError('message_whatsapp');
        $result->isTrue($validationError === "Nomor Whatsapp Salah!", $validationError);
        $this->tc['actual'] = "Menampilkan pesan $validationError";
    }
}
