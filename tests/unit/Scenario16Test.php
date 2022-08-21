<?php

use App\Models\MessagesModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @testdox #### TS-16 Cek fungsi melihat pesan aduan
 */
class Scenario16Test extends CIUnitTestCase
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
            'scenario' => 'TS-16',
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
     * @testdox TC-01 Melihat pesan aduan pada tanggal hari ini
     */
    public function testRetrieveMessages()
    {
        $date = date('Y-m-d');
        $count = $this->db->table('messages')->where('deleted_at', null)->like('created_at', $date)->countAllResults();
        $this->tc['expected'] = "Menampilkan $count data pesan";
        $this->tc['step'] = [
            'Masuk ke halaman Data Pesan',
            "Klik tombol dengan logo saring",
            "Isi kolom Timestamp",
            "Tutup panel saring",
        ];
        $this->tc['data'] = [
            "created_at: $date",
        ];
        $result = $this->withSession($this->sessionData)->call('get', 'data/pesan');
        $result->assertOK();
        $result->assertSee('Data Pesan', 'h1');
        $result->assertSeeElement('table');
        $query = [
            "draw" => "2",
            "columns" => [
                [
                    "data" => "message_sender",
                    "name" => "message_sender",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "message_type",
                    "name" => "message_type",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "message_whatsapp",
                    "name" => "message_whatsapp",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "message_status",
                    "name" => "message_status",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "created_at",
                    "name" => "created_at",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "unique_id",
                    "name" => "unique_id",
                    "searchable" => "true",
                    "orderable" => "false",
                    "search" => ["value" => "", "regex" => "false"],
                ],
            ],
            "order" => [["column" => "4", "dir" => "desc"]],
            "start" => "0",
            "length" => "10",
            "search" => ["value" => "", "regex" => "false"],
            "orderable" => [
                "message_sender",
                "message_type",
                "message_whatsapp",
                "message_status",
                "created_at",
            ],
            "searchable" => [
                "message_sender",
                "message_type",
                "message_whatsapp",
                "message_status",
                "created_at",
            ],
            "searchBuilder" => [
                "criteria" => [
                    [
                        "condition" => "=",
                        "data" => "Timestamp",
                        "origData" => "created_at",
                        "type" => "date",
                        "value" => [$date],
                        "value1" => $date,
                    ],
                ],
                "logic" => "AND",
            ],
            "_" => "1661057808685",
        ];
        $result = $this->withSession(
            $this->sessionData
        )->withBodyFormat(
            'json'
        )->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest'
        ])->call('get', 'data/pesan', $query);
        $result->assertOK();
        $jsonString = json_decode($result->getJSON());
        $data = json_decode($jsonString);
        $this->tc['actual'] = "Menampilkan " . $data->recordsFiltered . " data pesan";
    }

    /**
     * @testdox TC-02 Melihat pesan Aduan dalam rentang tanggal
     */
    public function testRetrieveMessagesRange()
    {
        $date0 = '2022-01-01';
        $date1 = date('Y-m-d');
        $count = $this->db->table('messages')->where(['created_at >=' => $date0, 'deleted_at' => null, 'created_at <=' => $date1])->countAllResults();
        $this->tc['expected'] = "Menampilkan $count data pesan";
        $this->tc['step'] = [
            'Masuk ke halaman Data Pesan',
            "Klik tombol dengan logo saring",
            "Isi kolom Timestamp (1) dengan opsi 'sejak'",
            "Klik tombol tambah kondisi DAN",
            "Isi kolom Timestamp (2) dengan opsi 'hingga'",
            "Tutup panel saring",
        ];
        $this->tc['data'] = [
            "created_at (1): $date0",
            "created_at (2): $date1",
        ];
        $query = [
            "draw" => "3",
            "columns" => [
                [
                    "data" => "message_sender",
                    "name" => "message_sender",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "message_type",
                    "name" => "message_type",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "message_whatsapp",
                    "name" => "message_whatsapp",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "message_status",
                    "name" => "message_status",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "created_at",
                    "name" => "created_at",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => ["value" => "", "regex" => "false"],
                ],
                [
                    "data" => "unique_id",
                    "name" => "unique_id",
                    "searchable" => "true",
                    "orderable" => "false",
                    "search" => ["value" => "", "regex" => "false"],
                ],
            ],
            "order" => [["column" => "4", "dir" => "desc"]],
            "start" => "0",
            "length" => "10",
            "search" => ["value" => "", "regex" => "false"],
            "orderable" => [
                "message_sender",
                "message_type",
                "message_whatsapp",
                "message_status",
                "created_at",
            ],
            "searchable" => [
                "message_sender",
                "message_type",
                "message_whatsapp",
                "message_status",
                "created_at",
            ],
            "searchBuilder" => [
                "criteria" => [
                    [
                        "condition" => ">",
                        "data" => "Timestamp",
                        "origData" => "created_at",
                        "type" => "date",
                        "value" => [$date0],
                        "value1" => $date0,
                    ],
                    [
                        "condition" => "<",
                        "data" => "Timestamp",
                        "origData" => "created_at",
                        "type" => "date",
                        "value" => [$date1],
                        "value1" => $date1,
                    ],
                ],
                "logic" => "AND",
            ],
            "_" => "1661058414348",
        ];
        $result = $this->withSession(
            $this->sessionData
        )->withBodyFormat(
            'json'
        )->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest'
        ])->call('get', 'data/pesan', $query);
        $result->assertOK();
        $jsonString = json_decode($result->getJSON());
        $data = json_decode($jsonString);
        $this->tc['actual'] = "Menampilkan " . $data->recordsFiltered . " data pesan";
    }

    /**
     * @testdox TC-03 Memperbarui status pesan dibaca/belum dibaca
     */
    public function testUpdateMessagetatus()
    {
        $builder = $this->db->table('messages');
        $countField = $builder->like("message_sender", 'test')->where('deleted_at', null)->countAllResults();
        $ids = $builder->where('deleted_at', null)->like("message_sender", 'test')->select('message_id')->get()->getResult();
        $ids = array_map(function ($e) {
            return encode($e->message_id, 'messages');
        }, $ids);
        $stringIds = json_encode($ids);
        $this->tc['expected'] = "Menampilkan pesan $countField Data Pesan Berhasil Diperbarui";
        $this->tc['step'] = [
            'Masuk ke halaman Data Pesan',
            "Pilih $countField data untuk diperbarui",
            "Tekan tombol ubah status"
        ];
        $this->tc['data'] = [
            "selections: " . str_replace('"', '', $stringIds)
        ];

        $result = $this->withRoutes([
            ['post', "data/pesan", "Data\Messages::index"],
        ])->withSession(
            $this->sessionData
        )->withBodyFormat(
            'json'
        )->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])->call('post', "data/pesan", [
            csrf_token() => csrf_hash(),
            '_method' => "PUT",
            'selections' => $ids,
        ]);
        $result->assertSessionHas('message', "$countField Data Pesan Berhasil Diperbarui");
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
    }

    /**
     * @testdox TC-04 Menghapus pesan
     */
    public function testDeleteMessages()
    {
        $builder = $this->db->table('messages');
        $countField = $builder->like("message_sender", 'test')->where('deleted_at', null)->countAllResults();
        $ids = $builder->where('deleted_at', null)->like("message_sender", 'test')->select('message_id')->get()->getResult();
        $ids = array_map(function ($e) {
            return encode($e->message_id, 'messages');
        }, $ids);
        $stringIds = json_encode($ids);
        $this->tc['expected'] = "Menampilkan pesan $countField data Pesan berhasil dihapus";
        $this->tc['step'] = [
            'Masuk ke halaman Data Pesan',
            "Pilih $countField data untuk dihapus",
            "Tekan tombol hapus"
        ];
        $this->tc['data'] = [
            "selections: " . str_replace('"', '', $stringIds)
        ];

        $result = $this->withRoutes([
            ['post', "data/pesan", "Data\Messages::index"],
        ])->withSession(
            $this->sessionData
        )->withBodyFormat(
            'json'
        )->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])->call('post', "data/pesan", [
            csrf_token() => csrf_hash(),
            '_method' => "DELETE",
            'selections' => $ids,
        ]);
        $result->assertSessionHas('message', "$countField data Pesan berhasil dihapus");
        $this->tc['actual'] = "Menampilkan pesan " . getFlash('message', true);
        $msm = new MessagesModel();
        $msm->purgeDeleted();
    }
}
