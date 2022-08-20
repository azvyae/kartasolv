<?php


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\DOMParser;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox #### TS-12 Cek fungsi menambahkan data PSKS
 */
class Scenario12Test extends CIUnitTestCase
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
     * @testdox TC-01 Menambahkan data PSKS validasi gagal
     */
    public function testAddPSKSDataInvalid()
    {
        $this->tc['expected'] = "Menampilkan pesan Kamu hanya dapat memilih kategori PSKS yang ada";
        $this->tc['step'] = [
            'Masuk ke halaman Tambah Data PSKS',
            "Isi formulir data PSKS",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'community_name: Test Name',
            'community_address: Test Address',
            'psks_type: 1',
            'community_identifier: 1234567890',
            'community_status: Disetujui',
            'pmpsks_img_loc: [psks1.webp, psks2.webp]',
        ];
        $result = $this->withSession($this->sessionData)->call('get', "data/psks/tambah");
        $result->assertOK();
        $result->assertSee('Tambah Data PSKS', 'h1');
        $result->assertSeeElement('input[name=community_name]');
        $result->assertSeeElement('input[name=community_address]');
        $result->assertSeeElement('select[name=psks_type]');
        $result->assertSeeElement('input[name=community_identifier]');
        $result->assertSeeElement('input[name=community_status]');
        $result->assertSeeElement('input[id=pmpsks_img_loc]');
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withSession(
            $this->sessionData
        )->call('post', 'data/psks/tambah', [
            csrf_token() => csrf_hash(),
            'community_name' => 'Test Name',
            'community_address' => 'Test Address',
            'psks_type' => 1,
            'community_identifier' => '1234567890',
            'community_status' => 'Disetujui',
        ]);
        $validationError = service('validation')->getError('psks_type');
        $result->isTrue($validationError === "Kamu hanya dapat memilih kategori PSKS yang ada", $validationError);
        $this->tc['actual'] = "Menampilkan pesan " . $validationError;
    }

    /**
     * @testdox TC-02 Menambahkan data PSKS
     */
    public function testAddPSKSData()
    {
        $identifier = uniqid("psks-add-");
        $this->tc['expected'] = "Menampilkan pesan Data PSKS berhasil diperbarui.";
        $this->tc['step'] = [
            'Masuk ke halaman Tambah Data PSKS',
            "Isi formulir data PSKS",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'community_name: Test Name',
            'community_address: Test Address',
            'psks_type: 27',
            "community_identifier:  $identifier",
            'pmpsks_img_loc: [psks1.webp, psks2.webp]',
        ];
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withSession(
            $this->sessionData
        )->call('post', 'data/psks/tambah', [
            csrf_token() => csrf_hash(),
            'community_name' => 'Test Name',
            'community_address' => 'Test Address',
            'psks_type' => 27,
            'community_identifier' => $identifier,
        ]);
        $result->assertSessionHas('message', 'Data PSKS berhasil diperbarui.');
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
        $ids = $this->db->table('communities')->like("community_name", 'test')->like('community_identifier', 'psks')->select('community_id')->get()->getResult();
        $data = [];
        foreach ($ids as $value) {
            $data[] = [
                'community_id' => $value->community_id,
                'pmpsks_img_loc' => uniqid('pmks-img-') . ".webp"
            ];
        }
        $this->db->table('pmpsks_img')->insertBatch($data);
    }

    /**
     * @testdox TC-03 Menampilkan halaman tambah data dengan spreadsheet
     */
    public function testXLSXPSKSAdd()
    {
        $this->tc['expected'] = "Menampilkan kolom unggah file Spreadsheet";
        $this->tc['step'] = ["Mengakses halaman tambah data PSKS dengan Spreadsheet"];
        $result = $this->withSession(
            $this->sessionData
        )->call('get', "data/psks/tambah-spreadsheet");
        $result->assertOK();
        $result->assertSeeElement('input[accept=application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]');
        $result->assertSeeElement('input[name=file_excel]');
        $result->assertSeeElement('form[action=' . base_url('data/psks/tambah-spreadsheet') . ']');
        $domParser = new DOMParser;
        $domParser->withString(service('response')->getBody());
        $checks = [
            $domParser->seeElement('input[accept=application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]'),
            $domParser->seeElement('input[name=file_excel]'),
            $domParser->seeElement('form[action=' . base_url('data/psks/tambah-spreadsheet') . ']')
        ];
        if (!in_array(false, $checks)) {
            $this->tc['actual'] = "Menampilkan kolom unggah file Spreadsheet";
        }
    }
}
