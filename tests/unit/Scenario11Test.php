<?php


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox TS-11 Cek fungsi melihat data PSKS
 */
class Scenario11Test extends CIUnitTestCase
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
            'scenario' => 'TS-11',
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
     * @testdox TC-01 Melihat data PSKS
     */
    public function testRetrievePSKSData()
    {
        $this->tc['case_code'] = 'TC-01';
        $this->tc['case'] = 'Melihat data PSKS';
        $this->tc['expected'] = "Menampilkan 3 data dengan nama: Abigail Hubbard, Abigail Perkins, Beck Ford";
        $this->tc['step'] = [
            'Masuk ke halaman Data PSKS',
            "Klik tombol dengan logo saring",
            "Isi kolom Nama (1)",
            "Klik tombol tambah kondisi ATAU",
            "Isi kolom Nama (2)",
            "Tutup panel saring",
        ];
        $this->tc['data'] = [
            'community_name (1): Abigail',
            'community_name (2): Beck',
            'condition: OR',
        ];
        $result = $this->withSession($this->sessionData)->call('get', 'data/psks');
        $result->assertOK();
        $result->assertSee('Data PSKS', 'h1');
        $result->assertSeeElement('table');
        $query = [
            "draw" => "18",
            "columns" => [
                "0" => [
                    "data" => "community_name",
                    "name" => "community_name",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                "1" => [
                    "data" => "community_address",
                    "name" => "community_address",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                "2" => [
                    "data" => "pmpsks_name",
                    "name" => "pmpsks_name",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                "3" => [
                    "data" => "community_status",
                    "name" => "community_status",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                "4" => [
                    "data" => "unique_id",
                    "name" => "unique_id",
                    "searchable" => "true",
                    "orderable" => "false",
                    "search" => ["value" => "", "regex" => "false"],
                ],
            ],
            "order" => [["column" => "0", "dir" => "asc"]],
            "start" => "0",
            "length" => "10",
            "search" => ["value" => "", "regex" => "false"],
            "orderable" => [
                "community_name",
                "community_address",
                "pmpsks_name",
                "community_status",
            ],
            "searchable" => [
                "community_identifier",
                "community_name",
                "community_address",
                "pmpsks_name",
                "community_status",
            ],
            "searchBuilder" => [
                "criteria" => [
                    "0" => [
                        "condition" => "contains",
                        "data" => "Nama",
                        "origData" => "community_name",
                        "type" => "html",
                        "value" => ["Abigail"],
                        "value1" => "Abigail",
                    ],
                    "1" => [
                        "condition" => "contains",
                        "data" => "Nama",
                        "origData" => "community_name",
                        "type" => "html",
                        "value" => ["Beck"],
                        "value1" => "Beck",
                    ],
                ],
                "logic" => "OR",
            ],
            "_" => "1661002418706",
        ];
        $result = $this->withSession(
            $this->sessionData
        )->withBodyFormat(
            'json'
        )->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest'
        ])->call('get', 'data/psks', $query);
        $result->assertOK();
        $jsonString = json_decode($result->getJSON());
        $data = json_decode($jsonString);
        $this->tc['actual'] = "Menampilkan " . $data->recordsFiltered . " data dengan nama: ";
        if (strpos($jsonString, "Abigail Hubbard") !== false) {
            $this->tc['actual'] .= "Abigail Hubbard,";
        }
        if (strpos($jsonString, "Abigail Perkins") !== false) {
            $this->tc['actual'] .= " Abigail Perkins,";
        }
        if (strpos($jsonString, "Beck Ford") !== false) {
            $this->tc['actual'] .= " Beck Ford";
        }
    }

    /**
     * @testdox TC-02 Menampilkan gambar PSKS yang diprivat
     */
    public function testGetPSKSImage()
    {
        $this->tc['case_code'] = 'TC-02';
        $this->tc['case'] = 'Menampilkan gambar PSKS yang diprivat';
        $id = $this->db->table('communities')
            ->where('pmpsks_type', 27)
            ->notLike('community_name', "Test")
            ->orderBy('pmpsks_type', 'asc')
            ->orderBy('community_id', 'asc')
            ->get(1)
            ->getRow()
            ->community_id;
        $encodedId = encode($id, 'psks');
        $this->tc['expected'] = "Menampilkan gambar data PSKS tertentu";
        $this->tc['step'] = [
            'Masuk ke halaman Data PSKS',
            "Klik tombol Lihat Gambar",
        ];
        $this->tc['data'] = [
            "uuid: $encodedId",
        ];
        $imgBuilder = $this->db->table('pmpsks_img');
        if (!$imgBuilder->getWhere(['community_id' => $id])->getRow()) {
            $imgBuilder->insert([
                'community_id' => $id,
                'pmpsks_img_loc' => 'https://kartasarijadi.test/gambar-privat?q=uploads%2Fdefault.webp',
            ]);
        }
        $result = $this->withSession(
            $this->sessionData
        )->withBodyFormat(
            'json'
        )->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest'
        ])->call('get', 'data/psks/gambar', ['uuid' => $encodedId]);
        $result->assertOK();
        $jsonString = json_decode($result->getJSON());
        $data = json_decode($jsonString);
        if ($data[0] === 'https://kartasarijadi.test/gambar-privat?q=uploads%2Fdefault.webp') {
            $this->tc['actual'] = "Menampilkan gambar data PSKS tertentu";
        }
    }
}
