<?php


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @testdox #### TS-00 Cek fungsi log in
 */
class Scenario00Test extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    protected $sessionData, $um, $tc;
    protected function setUp(): void
    {
        parent::setUp();
        $this->um = new \App\Models\UsersModel();
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
        $this->tc['step'][] = "Masuk ke halaman 'Masuk'";
        $result = $this->call('get', "masuk");
        $result->assertOK();
        $result->assertSee('Masuk', 'h1');
        $result->assertSeeElement('input[name=user_email]');
        $result->assertSeeElement('input[name=user_password]');
    }

    /**
     * @uses \Config\Services
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        if (!isset($_SESSION))
            session_start();
        if (session_status() === PHP_SESSION_ACTIVE)
            session_destroy();
        \Config\Services::validation()->reset();
        parseTest($this->tc);
        $this->assertTrue($this->tc['expected'] === $this->tc['actual'], "expected: " . $this->tc['expected'] . "\n" . 'actual: ' . $this->tc['actual']);
    }

    /**
     * @testdox TC-01 Log in dengan enkripsi kata sandi MD5
     */
    public function testLoginMD5()
    {
        $this->tc['expected'] = "Diarahkan ke halaman " . base_url('dasbor');
        $data = [
            'user_id' => 2,
            'user_password' => 'e16b2ab8d12314bf4efbd6203906ea6c'
        ];
        $this->um->save($data);
        $this->tc['step'][] =  "Menyimpan kata sandi enkripsi MD5 pada basis data";
        $this->tc['step'][] =  "Masukkan data email dan kata sandi";
        $this->tc['data'][] = "user_email: test@test.com";
        $this->tc['data'][] = "user_password: testpassword";
        $this->tc['step'][] =  "Menekan tombol 'Masuk'";
        $result = $this->call('post', 'masuk', [csrf_token() => csrf_hash(), 'user_email' => 'test@test.com', 'user_password' => 'testpassword', 'g-recaptcha-response' => 'random-token']);
        $result->assertRedirectTo(base_url('dasbor'));
        $this->tc['actual'] = "Diarahkan ke halaman " . $result->getRedirectUrl();
    }

    /**
     * @testdox TC-02 Log in dengan enkripsi kata sandi KartaSarijadi
     */
    public function testLoginKartaSarijadiEncrypt()
    {
        $this->tc['expected'] = "Diarahkan ke halaman " . base_url('dasbor');
        $this->tc['step'][] =  "Masukkan data email dan kata sandi";
        $this->tc['data'][] = "user_email: test@test.com";
        $this->tc['data'][] = "user_password: testpassword";
        $this->tc['step'][] =  "Menekan tombol 'Masuk'";
        $result = $this->call('post', 'masuk', [csrf_token() => csrf_hash(), 'user_email' => 'test@test.com', 'user_password' => 'testpassword', 'g-recaptcha-response' => 'random-token']);
        $result->assertRedirectTo(base_url('dasbor'));
        $this->tc['actual'] = "Diarahkan ke halaman " . $result->getRedirectUrl();
    }

    /**
     * @testdox TC-03 Log in dengan kata sandi yang salah
     */
    public function testFalseCredentials()
    {
        $this->tc['expected'] = "Diarahkan ke halaman " . base_url('masuk') . ' dan menampilkan pesan Email atau Kata Sandi Salah!';
        $this->tc['step'][] =  "Masukkan data email dan kata sandi";
        $this->tc['data'][] = "user_email: test@test.com";
        $this->tc['data'][] = "user_password: wrongpassword";
        $this->tc['step'][] =  "Menekan tombol 'Masuk'";
        $result = $this->call('post', 'masuk', [csrf_token() => csrf_hash(), 'user_email' => 'test@test.com', 'user_password' => 'wrongpassword', 'g-recaptcha-response' => 'random-token']);
        $message = getFlash('message', true);
        $result->assertRedirectTo(base_url('masuk'));
        $result->assertSessionHas('message', 'Email atau Kata Sandi Salah!');
        $flash = getFlash('message');
        $result->assertEquals("<div class='alert alert-danger alert-dismissible fade show' role='alert'>$message<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button></div>", $flash, $flash);
        $this->tc['actual'] = "Diarahkan ke halaman " . $result->getRedirectUrl() . " dan menampilkan pesan $message";
    }

    /**
     * @testdox TC-04 Log in dengan gagal validasi email
     */
    public function testFalseInput()
    {
        $this->tc['expected'] = "Diarahkan ke halaman " . base_url('masuk') . ' dan menampilkan pesan Kolom Email harus berisi sebuah alamat surel yang valid.';
        $this->tc['step'][] =  "Masukkan data email dan kata sandi";
        $this->tc['data'][] = "user_email: a b c d e";
        $this->tc['data'][] = "user_password: wrongpassword";
        $this->tc['step'][] =  "Menekan tombol 'Masuk'";
        $result = $this->call('post', 'masuk', [csrf_token() => csrf_hash(), 'user_email' => 'a b c d e', 'user_password' => 'wrongpassword', 'g-recaptcha-response' => 'random-token']);
        $validationError = service('validation')->getError('user_email');
        $result->assertRedirectTo(base_url('masuk'));
        $result->isTrue($validationError === "Kolom Email harus berisi sebuah alamat surel yang valid.", $validationError);
        $this->tc['actual'] = "Diarahkan ke halaman " . $result->getRedirectUrl() . ' dan menampilkan pesan ' . $validationError;
    }

    /**
     * @testdox TC-05 Masuk ke halaman log in ketika sudah ada session.
     */
    public function testAccessLoginPageAfterSessionIsSet()
    {
        $this->tc['expected'] = "Diarahkan ke halaman " . base_url('dasbor');
        $this->tc['step'][0] =  "Masuk ke halaman dasbor";
        $this->tc['step'][] =  "Mencoba masuk ke halaman login";
        $result = $this->withSession($this->sessionData)->call('get', "masuk");
        $result->assertOK();
        $result->assertRedirectTo(base_url('dasbor'));
        $this->tc['actual'] = "Diarahkan ke halaman " . $result->getRedirectUrl();
    }

    /**
     * @testdox TC-06 Melakukan Logout
     */
    public function testLogout()
    {
        $this->tc['expected'] = "Diarahkan ke halaman " . base_url('masuk');
        $this->tc['step'][0] =  "Masuk ke halaman dasbor";
        $this->tc['step'][] =  "Menekan tombol keluar";
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', 'keluar', 'Auth::index'],
        ])->withSession($this->sessionData)->call('post', "keluar", ['_method' => "DELETE", csrf_token() => csrf_hash(), 'g-recaptcha-response' => 'random-token']);
        $result->assertOK();
        $result->assertRedirectTo(base_url('masuk'));
        $this->tc['actual'] = "Diarahkan ke halaman " . $result->getRedirectUrl();
    }
}
