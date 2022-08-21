<?php


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox TS-13 Cek fungsi mengubah data PSKS
 */
class Scenario13Test extends CIUnitTestCase
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
            'scenario' => 'TS-13',
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
     * @testdox TC-01 Mencari data PSKS dengan id acak
     */
    public function testFindInvalidPSKSData()
    {
        $this->tc['case_code'] = 'TC-01';
        $this->tc['case'] = 'Mencari data PSKS dengan id acak';
        $this->tc['expected'] = "Menampilkan pesan Halaman Tidak Ditemukan";
        $this->tc['step'] = [
            "Isi panel alamat URL dengan data/psks/xxx dengan id acak",
            "Tekan Enter"
        ];
        $this->tc['data'] = [
            'community_id: notFoundId'
        ];
        try {
            $id = 'notFoundId';
            $this->withSession($this->sessionData)->call('get', "data/psks/$id");
        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            $message = $e->getMessage();
        }
        $this->tc['actual'] = "Menampilkan pesan $message";
    }

    /**
     * @testdox TC-02 Mengubah data PSKS
     */
    public function testUpdatePSKS()
    {
        $this->tc['case_code'] = 'TC-02';
        $this->tc['case'] = 'Mengubah data PSKS';
        $identifier = uniqid("psks-change-");
        $community_id = $this->db->table('communities')->select('community_id')->where('deleted_at', null)->like('community_identifier', 'psks')->like("community_name", "Test")->get(1)->getRow()->community_id;
        $encodedId = encode($community_id, 'psks');
        $this->tc['expected'] = "Menampilkan pesan Data PSKS berhasil diperbarui.";
        $this->tc['step'] = [
            'Masuk ke halaman Data PSKS',
            'Klik salah satu tombol Ubah pada tabel PSKS',
            "Isi formulir data PSKS",
            "Tekan tombol simpan"
        ];
        $this->tc['data'] = [
            'community_name: Test Name',
            'community_address: Test Address',
            'psks_type: 31',
            "community_identifier:  $identifier"
        ];
        $result = $this->withSession(
            $this->sessionData
        )->call('get', "data/psks/$encodedId");
        $result->assertOK();
        $result = $this->withHeaders([
            "Content-Type" => 'multipart/form-data'
        ])->withRoutes([
            ['post', "data/psks/(:alphanum)", "Data\Psks::crud/$1"],
        ])->withSession(
            $this->sessionData
        )->call('post', "data/psks/$encodedId", [
            csrf_token() => csrf_hash(),
            '_method' => "PUT",
            'community_name' => 'Test Name',
            'community_address' => 'Test Address',
            'psks_type' => 31,
            'community_identifier' =>  $identifier,
        ]);
        $result->assertSessionHas('message', 'Data PSKS berhasil diperbarui.');
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * @testdox TC-03 Memperbarui status PSKS
     */
    public function testUpdatePSKSStatus()
    {
        $this->tc['case_code'] = 'TC-03';
        $this->tc['case'] = 'Memperbarui status PSKS';
        $builder = $this->db->table('communities');
        $countField = $builder->like("community_name", 'test')->where('deleted_at', null)->like('community_identifier', 'psks')->countAllResults();
        $ids = $builder->where('deleted_at', null)->like("community_name", 'test')->like('community_identifier', 'psks')->select('community_id')->get()->getResult();
        $ids = array_map(function ($e) {
            return encode($e->community_id, 'psks');
        }, $ids);
        $stringIds = json_encode($ids);
        $this->tc['expected'] = "Menampilkan pesan $countField Data PSKS Berhasil Diperbarui";
        $this->tc['step'] = [
            'Masuk ke halaman Data PSKS',
            "Pilih $countField data untuk diperbarui",
            "Tekan tombol ubah status"
        ];
        $this->tc['data'] = [
            "selections: " . str_replace('"', '', $stringIds)
        ];

        $result = $this->withRoutes([
            ['post', "data/psks", "Data\Psks::index"],
        ])->withSession(
            $this->sessionData
        )->withBodyFormat(
            'json'
        )->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])->call('post', "data/psks", [
            csrf_token() => csrf_hash(),
            '_method' => "PUT",
            'selections' => $ids,
        ]);
        $result->assertSessionHas('message', "$countField Data PSKS Berhasil Diperbarui");
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }
}
