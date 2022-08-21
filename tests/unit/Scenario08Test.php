<?php


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\DOMParser;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox TS-08 Cek fungsi menambahkan data PMKS
 */
class Scenario08Test extends CIUnitTestCase
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
            'test_scenario' => 'Cek fungsi menambahkan data PMKS',

            'scenario' => 'TS-08',
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
     * @testdox TC-01 Menambahkan data PMKS validasi gagal
     */
    public function testAddPMKSDataInvalid()
    {
        $this->tc['case_code'] = 'TC-01';
        $this->tc['case'] = 'Menambahkan data PMKS validasi gagal';
        $this->tc['expected'] = "Menampilkan pesan Kamu hanya dapat memilih kategori PMKS yang ada";
        $this->tc['step'] = [
            'Masuk ke halaman Tambah Data PMKS',
            "Isi formulir data PMKS",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'community_name: Test Name',
            'community_address: Test Address',
            'pmks_type: 28',
            'community_identifier: 1234567890',
            'community_status: Disetujui',
            'pmpsks_img_loc: [pmks1.webp, pmks2.webp]',
        ];
        $result = $this->withSession($this->sessionData)->call('get', "data/pmks/tambah");
        $result->assertOK();
        $result->assertSee('Tambah Data PMKS', 'h1');
        $result->assertSeeElement('input[name=community_name]');
        $result->assertSeeElement('input[name=community_address]');
        $result->assertSeeElement('select[name=pmks_type]');
        $result->assertSeeElement('input[name=community_identifier]');
        $result->assertSeeElement('input[name=community_status]');
        $result->assertSeeElement('input[id=pmpsks_img_loc]');
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withSession(
            $this->sessionData
        )->call('post', 'data/pmks/tambah', [
            csrf_token() => csrf_hash(),
            'community_name' => 'Test Name',
            'community_address' => 'Test Address',
            'pmks_type' => '28',
            'community_identifier' => '1234567890',
            'community_status' => 'Disetujui',
        ]);
        $validationError = service('validation')->getError('pmks_type');
        $result->isTrue($validationError === "Kamu hanya dapat memilih kategori PMKS yang ada", $validationError);
        $this->tc['actual'] = "Menampilkan pesan " . $validationError;
    }

    /**
     * @testdox TC-02 Menambahkan data PMKS
     */
    public function testAddPMKSData()
    {
        $this->tc['case_code'] = 'TC-02';
        $this->tc['case'] = 'Menambahkan data PMKS';
        $identifier = uniqid("pmks-add-");
        $this->tc['expected'] = "Menampilkan pesan Data PMKS berhasil diperbarui.";
        $this->tc['step'] = [
            'Masuk ke halaman Tambah Data PMKS',
            "Isi formulir data PMKS",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'community_name: Test Name',
            'community_address: Test Address',
            'pmks_type: 1',
            "community_identifier: $identifier",
            'pmpsks_img_loc: [pmks1.webp, pmks2.webp]',
        ];
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withSession(
            $this->sessionData
        )->call('post', 'data/pmks/tambah', [
            csrf_token() => csrf_hash(),
            'community_name' => 'Test Name',
            'community_address' => 'Test Address',
            'pmks_type' => 1,
            'community_identifier' => $identifier,
        ]);
        $result->assertSessionHas('message', 'Data PMKS berhasil diperbarui.');
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);

        $ids = $this->db->table('communities')->like("community_name", 'test')->like('community_identifier', 'pmks')->select('community_id')->get()->getResult();
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
    public function testXLSXPMKSAdd()
    {
        $this->tc['case_code'] = 'TC-03';
        $this->tc['case'] = 'Menampilkan halaman tambah data dengan spreadsheet';
        $this->tc['expected'] = "Menampilkan kolom unggah file Spreadsheet";
        $this->tc['step'] = ["Mengakses halaman tambah data PMKS dengan Spreadsheet"];
        $result = $this->withSession(
            $this->sessionData
        )->call('get', "data/pmks/tambah-spreadsheet");
        $result->assertOK();
        $result->assertSeeElement('input[accept=application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]');
        $result->assertSeeElement('input[name=file_excel]');
        $result->assertSeeElement('form[action=' . base_url('data/pmks/tambah-spreadsheet') . ']');
        $domParser = new DOMParser;
        $domParser->withString(service('response')->getBody());
        $checks = [
            $domParser->seeElement('input[accept=application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]'),
            $domParser->seeElement('input[name=file_excel]'),
            $domParser->seeElement('form[action=' . base_url('data/pmks/tambah-spreadsheet') . ']')
        ];
        if (!in_array(false, $checks)) {
            $this->tc['actual'] = "Menampilkan kolom unggah file Spreadsheet";
        }
    }
}
