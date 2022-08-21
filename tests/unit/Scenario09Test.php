<?php


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox TS-09 Cek fungsi mengubah data PMKS
 */
class Scenario09Test extends CIUnitTestCase
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
            'test_scenario' => 'Cek fungsi mengubah data PMKS',

            'scenario' => 'TS-09',
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
     * @testdox TC-01 Mencari data PMKS dengan id acak
     */
    public function testFindInvalidPMKSData()
    {
        $this->tc['case_code'] = 'TC-01';
        $this->tc['case'] = 'Mencari data PMKS dengan id acak';
        $this->tc['expected'] = "Menampilkan pesan Halaman Tidak Ditemukan";
        $this->tc['step'] = [
            "Isi panel alamat URL dengan data/pmks/xxx dengan id acak",
            "Tekan Enter"
        ];
        $this->tc['data'] = [
            'community_id: notFoundId'
        ];
        try {
            $id = 'notFoundId';
            $this->withSession($this->sessionData)->call('get', "data/pmks/$id");
        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            $message = $e->getMessage();
        }
        $this->tc['actual'] = "Menampilkan pesan $message";
    }

    /**
     * @testdox TC-02 Mengubah data PMKS
     */
    public function testUpdatePMKS()
    {
        $this->tc['case_code'] = 'TC-02';
        $this->tc['case'] = 'Mengubah data PMKS';
        $identifier = uniqid("pmks-change-");
        $community_id = $this->db->table('communities')->select('community_id')->where('deleted_at', null)->like('community_identifier', 'pmks')->like("community_name", "Test")->get(1)->getRow()->community_id;
        $encodedId = encode($community_id, 'pmks');
        $this->tc['expected'] = "Menampilkan pesan Data PMKS berhasil diperbarui.";
        $this->tc['step'] = [
            'Masuk ke halaman Data PMKS',
            'Klik salah satu tombol Ubah pada tabel PMKS',
            "Isi formulir data PMKS",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'community_name: Test Name',
            'community_address: Test Address',
            'pmks_type: 5',
            "community_identifier:  $identifier"
        ];
        $result = $this->withSession(
            $this->sessionData
        )->call('get', "data/pmks/$encodedId");
        $result->assertOK();
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', "data/pmks/(:alphanum)", "Data\Pmks::crud/$1"],
        ])->withSession(
            $this->sessionData
        )->call('post', "data/pmks/$encodedId", [
            csrf_token() => csrf_hash(),
            '_method' => "PUT",
            'community_name' => 'Test Name',
            'community_address' => 'Test Address',
            'pmks_type' => 5,
            'community_identifier' =>  $identifier,
        ]);
        $result->assertSessionHas('message', 'Data PMKS berhasil diperbarui.');
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * @testdox TC-03 Memperbarui status PMKS
     */
    public function testUpdatePMKSStatus()
    {
        $this->tc['case_code'] = 'TC-03';
        $this->tc['case'] = 'Memperbarui status PMKS';
        $builder = $this->db->table('communities');
        $countField = $builder->like("community_name", 'test')->where('deleted_at', null)->like('community_identifier', 'pmks')->countAllResults();
        $ids = $builder->where('deleted_at', null)->like("community_name", 'test')->like('community_identifier', 'pmks')->select('community_id')->get()->getResult();
        $ids = array_map(function ($e) {
            return encode($e->community_id, 'pmks');
        }, $ids);
        $stringIds = json_encode($ids);
        $this->tc['expected'] = "Menampilkan pesan $countField Data PMKS Berhasil Diperbarui";
        $this->tc['step'] = [
            'Masuk ke halaman Data PMKS',
            "Pilih $countField data untuk diperbarui",
            "Tekan tombol ubah status"
        ];
        $this->tc['data'] = [
            "selections: " . str_replace('"', '', $stringIds)
        ];

        $result = $this->withRoutes([
            ['post', "data/pmks", "Data\Pmks::index"],
        ])->withSession(
            $this->sessionData
        )->withBodyFormat(
            'json'
        )->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])->call('post', "data/pmks", [
            csrf_token() => csrf_hash(),
            '_method' => "PUT",
            'selections' => $ids,
        ]);
        $result->assertSessionHas('message', "$countField Data PMKS Berhasil Diperbarui");
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }
}
