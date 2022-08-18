<?php

namespace App\Libraries;

use Config\Database;
use Exception;

/**
 * DatabaseManager is a library made to simplify query builder especially to retrieve data and displays it to
 * Datatables with server side processing.
 * 
 * This library has five main methods from composing query, counting results, grouping query,
 * translate query, and prepare query to be executed.
 * 
 * @author Azvya Erstevan I.
 * @package KartasolvApp\Libraries
 */
class DatabaseManager extends Database
{
    /**
     * Prepare the db connection.
     * @var \CodeIgniter\Database\BaseConnection $db Database connection.
     */
    private $db;

    /**
     * Prepare builder variable.
     * @var mixed $builder Table to be connected.
     */
    private $builder;
    /**
     * Construct connection to the database.
     */
    public function __construct()
    {
        $this->db = $this->connect();
    }

    /**
     * Generate datatables query based on user input.
     * 
     * Before you could implement php data you have to provide Javascript and html function inside the
     * View file.
     * 
     * HTML Code Example:
     * ```html
     *  <table id="table"></table>
     * ```
     * 
     * And you have to provide Javascript function, you can either use separated files or implement this code inside
     * the html <script></script> Tag. Example:
     * 
     * ```javascript
     *  document.addEventListener('DOMContentLoaded', function() {
     *    // Create configuration
     *    config = {
     *        table_id: 'table',
     *        // Ajax server side config
     *        ajax: {
     *            url: baseUrl("konten/profil-karang-taruna/pengurus"),
     *            type: "GET",
     *            data: {
     *                orderable: ['selected_id', 'selected_name', 'selected_name2'],
     *                searchable: ['selected_id', 'selected_name', 'selected_name2']
     *            }
     *        },
     *        // Configure buttons
     *        defaultOrder: [2, 'asc'],
     *        buttons: {
     *            add: {
     *                url: baseUrl('ajax/url')
     *            },
     *            xlsx: true,
     *            delete: {
     *                url: baseUrl('ajax/url'),
     *                postData: postData()
     *            },
     *            manipulateSelected: {
     *                url: baseUrl('ajax/url'),
     *                text: '<i class="icon sample"></i>',
     *                title: 'Title Custom',
     *                postData: postData()
     *            },
     *            custom: {
     *                text: '<i class="icon sample"></i>',
     *                title: 'Title Custom',
     *                action: function() {
     *                    window.location.href = baseUrl('ajax/url/custom');
     *                }
     *            }
     *        },
     *        columns: [{
     *                title: "Nama",
     *                name: "selected_name",
     *                data: "selected_name",
     *                className: 'text-center text-lg-start',
     *                render: function(data, type, row) {
     *                    return `{row.selected_name}`;
     *                },
     *
     *            },
     *            {
     *                title: "Tipe",
     *                name: "selected_name2",
     *                data: "selected_name2",
     *                className: 'text-center',
     *                type: 'array',
     *                options: [{
     *                        value: '1',
     *                        text: 'Ketua'
     *                    },
     *                    {
     *                        value: '2',
     *                        text: 'Top Level'
     *                    },
     *                    {
     *                        value: '3',
     *                        text: 'Kegiatan Khusus'
     *                    },
     *                    {
     *                        value: '4',
     *                        text: 'Anggota'
     *                    },
     *
     *                ],
     *                render: function(data) {
     *                    switch (data) {
     *                        case '1':
     *                            type = 'Ketua'
     *                            className = 'bg-danger'
     *                            break;
     *                        case '2':
     *                            type = 'Top Level'
     *                            className = 'bg-warning text-dark'
     *                            break;
     *                        case '3':
     *                            type = 'Kabid'
     *                            className = 'bg-success'
     *                            break;
     *                        default:
     *                            type = 'Anggota'
     *                            className = 'bg-light text-dark'
     *                            break;
     *                    }
     *                    return `<span class="badge ${className}">${type}</span>`;
     *                }
     *            },
     *            {
     *                title: "Aksi",
     *                name: "unique_id",
     *                data: "unique_id",
     *                className: "text-center",
     *                render: function(data, type, row) {
     *                    return (
     *                        `<a href="sample/url/${unique_id}></a>`
     *                    );
     *                },
     *            },
     *        ],
     *    }
     *    createDatatable(config);
     *})
     * ```
     * 
     * @param mixed $condition Condition to be generated, for usage see Model Examples provided in this application.
     * ```php
     * public function getDatatable($condition)
     *   {
     *       $dbMan = new DatabaseManager;
     *       $query = [
     *           'result' => 'result',
     *           'table'  => 'table_name',
     *           'select' => ['selected_id', 'selected_name', 'selected_name2']
     *       ];
     *       $query += $dbMan->filterDatatables($condition);
     *       $query['orderBy'] .= ', selected_id ASC';
     *       $data = [
     *           'totalRows' => $dbMan->countAll($query),
     *           'result' => $dbMan->read($query),
     *           'searchable' => array_map(function ($e) {
     *               return $e . ":name";
     *           }, $condition['columnSearch'])
     *       ];
     *       return objectify($data);
     *  }
     * ```
     * @return mixed Formatted query.
     * @see https://datatables.net/ for instructions how to create server side processing Datatables.
     */
    public function filterDatatables($condition)
    {
        $d = null;
        if ($condition['order']) {
            $order = $condition['orderable'][$condition['order']['column']] . ' ' . $condition['order']['dir'];
        }
        $d['orderBy'] = $order ?? $condition['orderable'][0] . ' ASC';
        $d['group'] = [];
        if ($condition['filter'] ?? false) {
            foreach ($condition['filter']['criteria'] as $criteria) {
                if (!array_key_exists('data', $criteria) or !array_key_exists('condition', $criteria)) {
                    continue;
                }
                if (!in_array($criteria['origData'], $condition['columnSearch'])) {
                    continue;
                }
                $cdn = $criteria['condition'];
                $col = $criteria['origData'] ?? null;
                switch ($cdn) {
                    case 'null':
                        if ($criteria['type'] == 'date') {
                            if ($condition['filter']['logic'] == 'OR') {
                                array_push($d['group'], ['orWhere', [$col => null]]);
                            } else {
                                $subgroup = [];
                                array_push($subgroup, ['orWhere', [$col => null]]);
                                array_push($d['group'], ['subgroup', $subgroup]);
                            }
                        } else {
                            if ($condition['filter']['logic'] == 'OR') {
                                array_push($d['group'], ['orWhere', [$col => null]], ['orWhere', [$col => '']]);
                            } else {
                                $subgroup = [];
                                array_push($subgroup, ['orWhere', [$col => null]], ['orWhere', [$col => '']]);
                                array_push($d['group'], ['subgroup', $subgroup]);
                            }
                        }

                        break;
                    case 'contains':
                        if (!array_key_exists('value', $criteria)) continue 2;
                        if ($condition['filter']['logic'] == 'OR') {
                            array_push($d['group'], ['orLike', [$col => $criteria['value1']]]);
                        } else {
                            array_push($d['group'], ['like', [$col => $criteria['value1']]]);
                        }
                        break;
                    default:
                        if (!array_key_exists('value', $criteria)) continue 2;
                        if ($criteria['type'] == 'date') {
                            switch ($cdn) {
                                case '=':
                                    if ($condition['filter']['logic'] == 'OR') {
                                        array_push($d['group'], ['orLike', [$col => $criteria['value1']]]);
                                    } else {
                                        array_push($d['group'], ['like', [$col => $criteria['value1']]]);
                                    }
                                    break;
                                case '>':
                                    if ($condition['filter']['logic'] == 'OR') {
                                        array_push($d['group'], ['orWhere', [$col . " >= " => $criteria['value1']]]);
                                    } else {
                                        array_push($d['group'], ['where', [$col . " >= " => $criteria['value1']]]);
                                    }
                                    break;
                                case '<':
                                    if ($condition['filter']['logic'] == 'OR') {
                                        array_push($d['group'], ['orWhere', [$col . " <= " => $criteria['value1']]]);
                                    } else {
                                        array_push($d['group'], ['where', [$col . " <= " => $criteria['value1']]]);
                                    }
                                    break;
                            }
                        } else {
                            if ($condition['filter']['logic'] == 'OR') {
                                array_push($d['group'], ['orWhere', [$col . " {$cdn} " => $criteria['value1']]]);
                            } else {
                                array_push($d['group'], ['where', [$col . " {$cdn} " => $criteria['value1']]]);
                            }
                        }

                        break;
                }
            }
        }
        if ($condition['search']) {
            $subgroup = [];
            foreach ($condition['columnSearch'] as $column) {
                array_push($subgroup, ['orLike', [$column => $condition['search']]]);
            }
            array_push($d['group'], ['subgroup', $subgroup]);
        }
        $d['query'] = [
            'orderBy' => $d['orderBy'],

        ];
        if ($condition['limit'] >= 0) {
            $d['query'] += [
                'limit'  =>  $condition['limit'],
                'offset' => $condition['offset']
            ];
        }
        if (array_key_exists('group', $d)) {
            $d['query'] += ['group' => $d['group']];
        }
        return $d['query'];
    }

    /**
     * Count queried result.
     * @param mixed $data Query array.
     * @param bool $retrieve_all Retrieve everything includin deleted_at is not null data.
     * @return int Row count.
     */
    public function countAll($data, $retrieve_all = false)
    {
        if (!$retrieve_all) {
            if (array_key_exists('where', $data)) {
                if (!is_array($data['where'])) {
                    throw new Exception('Where clause must be an associative array!');
                }
            }
            if (array_key_exists('join', $data)) {
                $data['where'][$data['table'] . '.deleted_at'] = null;
                foreach ($data['join'] as $key) {
                    $data['where'][($key[3] ?? $key[0]) . '.deleted_at'] = null;
                }
            } else {
                $data['where']['deleted_at'] = null;
            }
        }
        $this->builder = $this->db->table($data['table']);
        if (array_key_exists('groupBy', $data)) {
            $this->builder->groupBy($data['groupBy']);
        }

        if (array_key_exists('group', $data)) {
            if ($data['group'] != null) {
                $this->makeGroup($data);
            }
        }
        if (array_key_exists('where', $data)) {
            if (!is_array($data['where'])) {
                throw new \Exception('Where clause must be an associative array!');
            }
            $this->builder->where($data['where']);
        }
        if (array_key_exists('like', $data)) {
            if (!is_array($data['like'])) {
                throw new \Exception('Like clause must be an associative array!');
            }
            $this->builder->like($data['like']);
        }

        if (array_key_exists('select', $data)) {
            $this->builder->select($data['select']);
        } else {
            $this->builder->select('*');
        }

        if (array_key_exists('join', $data)) {
            if (!is_array($data['join'] ?? [])) {
                throw new \Exception('Join parameter must be an associative array!');
            }

            foreach ($data['join'] as $key) {
                if ($key[3] ?? null) {
                    $key[0] .= ' AS ' . $key[3];
                }
                $this->builder->join($key[0], $key[1], $key[2] ?? null);
            }
        }
        $string = $this->builder->getCompiledSelect();
        $this->db->query($string)->getResult();
        return (int)$this->db->query('SELECT FOUND_ROWS() as "rows"')->getRow()->rows;
    }

    /**
     * Read database function with QueryBuilder.
     * 
     *
     * Simple read query:
     * ```php
     * $query = [
     *      'result' => 'result',
     *      'table'  => 'sample_table'
     * ];
     * $this->lib_db->read($query);
     * ```
     * Simple where query:
     * ```php
     * $query = [
     *      'result' => 'result',
     *      'table'  => 'sample_table',
     *      'where'  => ['id' => '1', 'email' => 'good@gmail.com']
     * ];
     * $this->lib_db->read($query);
     * ```
     * Limit and join read query:
     * ```php
     * $query = [
     *      'result' => 'result',
     *      'table'  => 'sample_table',
     *      'join'   => [
     *          ['sample_table_2','sample_table.id = sample_table_2.id_sample', 'right outer'],
     *          ['sample_table_3','sample_table_2.id_sample = sample_table_3.id_sample_2']
     *      ],
     *      'limit'  => 3
     * ];
     * $this->lib_db->read($query);
     * ```
     * @param mixed $data Array query data.
     * @param bool $retrieve_all Retrieve everything includin deleted_at is not null data.
     * @return mixed Queried result.
     */
    public function read($data, $retrieveAll = false)
    {
        $this->builder = $this->db->table($data['table']);
        if (!$retrieveAll) {
            if (array_key_exists('where', $data)) {
                if (!is_array($data['where'])) {
                    throw new Exception('Where clause must be an associative array!');
                }
            }
            if (array_key_exists('join', $data)) {
                $data['where'][$data['table'] . '.deleted_at'] = null;
                foreach ($data['join'] as $key) {
                    $data['where'][($key[3] ?? $key[0]) . '.deleted_at'] = null;
                }
            } else {
                $data['where']['deleted_at'] = null;
            }
        }

        if (array_key_exists('select', $data)) {
            $this->builder->select($data['select']);
        } else {
            $this->builder->select('*');
        }

        if (array_key_exists('where', $data)) {
            if (!is_array($data['where'])) {
                throw new \Exception('Where clause must be an associative array!');
            }
            $this->builder->where($data['where']);
        }

        if (array_key_exists('join', $data)) {
            if (!is_array($data['join'] ?? [])) {
                throw new \Exception('Join parameter must be an associative array!');
            }
            foreach ($data['join'] as $key) {
                if ($key[3] ?? null) {
                    $key[0] .= ' AS ' . $key[3];
                }
                $this->builder->join($key[0], $key[1], $key[2] ?? null);
            }
        }
        if (array_key_exists('like', $data)) {
            if (!is_array($data['like'])) {
                throw new \Exception('Like clause must be an associative array!');
            }
            $this->builder->like($data['like']);
        }

        if (array_key_exists('orLike', $data)) {
            if (!is_array($data['orLike'])) {
                throw new \Exception('Or like clause must be an associative array!');
            }
            $this->builder->like($data['orLike'][0]);
        }

        if (array_key_exists('orderBy', $data)) {
            $this->builder->orderBy($data['orderBy']);
        }

        if (array_key_exists('groupBy', $data)) {
            $this->builder->groupBy($data['groupBy']);
        }

        if (array_key_exists('group', $data)) {
            if ($data['group'] != null) {
                $this->makeGroup($data);
            }
        }

        if (array_key_exists('limit', $data)) {
            if (array_key_exists('offset', $data)) {
                $this->builder->limit($data['limit'], $data['offset']);
            } else {
                $this->builder->limit($data['limit']);
            }
        }

        if (array_key_exists('result', $data)) {
            $result = 'get' . ucfirst($data['result']);
            $data = $this->builder->get()->$result();
        } else {
            $data = $this->builder->get();
        }

        return filterOutput($data);
    }

    /**
     * Group Queries.
     * @param mixed $data Query to be grouped.
     * @return void Nothing to return, just sets the builder to be grouped.
     */
    public function makeGroup($data)
    {
        $this->builder->groupStart('', $data[1]['grouptype'] ?? $data['group']['grouptype'] ?? 'AND ');
        foreach ($data[1] ?? $data['group'] as $key) {
            if ($key[0] == 'where') {
                $this->builder->where($key[1]);
            } else if ($key[0] == 'like') {
                $this->builder->like($key[1], false, $key[2] ?? 'both');
            } else if ($key[0] == 'orLike') {
                $this->builder->orLike($key[1], false, $key[2] ?? 'both');
            } else if ($key[0] == 'orWhere') {
                $this->builder->orWhere($key[1]);
            } else if ($key[0] == 'subgroup') {
                $this->makeGroup($key);
            }
        }
        $this->builder->groupEnd();
    }
}
