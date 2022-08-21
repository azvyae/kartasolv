<?php


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox TS-07 Cek fungsi melihat data PMKS
 */
class Scenario07Test extends CIUnitTestCase
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
            'test_scenario' => 'Cek fungsi melihat data PMKS',

            'scenario' => 'TS-07',
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
     * @testdox TC-01 Melihat data PMKS
     */
    public function testRetrievePMKSData()
    {
        $this->tc['case_code'] = 'TC-01';
        $this->tc['case'] = 'Melihat data PMKS';
        $this->tc['expected'] = "Menampilkan 41 data dengan kategori: Anak yang memerlukan perlindungan khusus dan Korban kekerasan";
        $this->tc['step'] = [
            'Masuk ke halaman Data PMKS',
            "Klik tombol dengan logo saring",
            "Isi kolom Tipe (1)",
            "Klik tombol tambah kondisi ATAU",
            "Isi kolom Tipe (2)",
            "Tutup panel saring",
        ];
        $this->tc['data'] = [
            'pmpsks_name (1): Anak yang memerlukan perlindungan khusus',
            'pmpsks_name (2): Korban kekerasan',
            'condition: OR',
        ];
        $result = $this->withSession($this->sessionData)->call('get', 'data/pmks');
        $result->assertOK();
        $result->assertSee('Data PMKS', 'h1');
        $result->assertSeeElement('table');
        $query = [
            "draw" => "7",
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
                        "condition" => "=",
                        "data" => "Tipe",
                        "origData" => "pmpsks_name",
                        "type" => "array",
                        "value" => ["Anak yang memerlukan perlindungan khusus"],
                        "value1" => "Anak yang memerlukan perlindungan khusus",
                    ],
                    "1" => [
                        "condition" => "=",
                        "data" => "Tipe",
                        "origData" => "pmpsks_name",
                        "type" => "array",
                        "value" => ["Korban kekerasan"],
                        "value1" => "Korban kekerasan",
                    ],
                ],
                "logic" => "OR",
            ],
            "_" => "1661001204263",
        ];
        $result = $this->withSession(
            $this->sessionData
        )->withBodyFormat(
            'json'
        )->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest'
        ])->call('get', 'data/pmks', $query);
        $result->assertOK();
        $jsonString = json_decode($result->getJSON());
        $data = json_decode($jsonString);
        $this->tc['actual'] = "Menampilkan " . $data->recordsFiltered . " data dengan kategori: ";
        if (strpos($jsonString, "Anak yang memerlukan perlindungan khusus") !== false) {
            $this->tc['actual'] .= "Anak yang memerlukan perlindungan khusus";
        }
        if (strpos($jsonString, "Korban kekerasan") !== false) {
            $this->tc['actual'] .= " dan Korban kekerasan";
        }
    }

    /**
     * @testdox TC-02 Menampilkan gambar PMKS yang diprivat
     */
    public function testGetPMKSImage()
    {
        $this->tc['case_code'] = 'TC-02';
        $this->tc['case'] = 'Menampilkan gambar PMKS yang diprivat';
        $id = $this->db->table('communities')
            ->where('pmpsks_type', 1)
            ->notLike('community_name', "Test")
            ->orderBy('pmpsks_type', 'asc')
            ->orderBy('community_id', 'asc')
            ->get(1)
            ->getRow()
            ->community_id;
        $encodedId = encode($id, 'pmks');
        $this->tc['expected'] = "Menampilkan gambar data PMKS tertentu";
        $this->tc['step'] = [
            'Masuk ke halaman Data PMKS',
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
        ])->call('get', 'data/pmks/gambar', ['uuid' => $encodedId]);
        $result->assertOK();
        $jsonString = json_decode($result->getJSON());
        $data = json_decode($jsonString);
        if ($data[0] === 'https://kartasarijadi.test/gambar-privat?q=uploads%2Fdefault.webp') {
            $this->tc['actual'] = "Menampilkan gambar data PMKS tertentu";
        }
    }
}
