<?php

use App\Models\CommunitiesModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox TS-14 Cek fungsi hapus data PSKS
 */
class Scenario14Test extends CIUnitTestCase
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
            'test_scenario' => 'Cek fungsi hapus data PSKS',

            'scenario' => 'TS-14',
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
     * @testdox TC-01 Menghapus data PSKS
     */
    public function testDeletePSKS()
    {
        $this->tc['case_code'] = 'TC-01';
        $this->tc['case'] = 'Menghapus data PSKS';
        $builder = $this->db->table('communities');
        $countField = $builder->where('deleted_at', null)->like('community_identifier', 'psks')->like("community_name", 'test')->countAllResults();
        $ids = $builder->where('deleted_at', null)->like("community_name", 'test')->like('community_identifier', 'psks')->select('community_id')->get()->getResult();
        $ids = array_map(function ($e) {
            return encode($e->community_id, 'psks');
        }, $ids);
        $stringIds = json_encode($ids);
        $this->tc['expected'] = "Menampilkan pesan $countField data PSKS berhasil dihapus";
        $this->tc['step'] = [
            'Masuk ke halaman Data PSKS',
            "Pilih $countField data untuk dihapus",
            "Tekan tombol hapus"
        ];
        $this->tc['data'] = [
            "selections: " . str_replace('"', '', $stringIds)
        ];

        $result = $this->withRoutes([
            ['post', "data/psks", "Data\Pmpsks::index"],
        ])->withSession(
            $this->sessionData
        )->withBodyFormat(
            'json'
        )->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])->call('post', "data/psks", [
            csrf_token() => csrf_hash(),
            '_method' => "DELETE",
            'selections' => $ids,
        ]);
        $result->assertSessionHas('message', "$countField data PSKS berhasil dihapus");
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
        $cm = new CommunitiesModel();
        $cm->purgeDeleted();
    }
}
