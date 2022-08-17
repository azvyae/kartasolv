<?php

namespace App\Libraries;

use Config\Database;
use Exception;

/**
 * @package Kartasolv\Libraries
 */
class DatabaseManager extends Database
{
    private $db, $builder;
    public function __construct()
    {
        $this->db = $this->connect();
    }
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
                $cdn = $this->translate($criteria['condition']);
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
     * Simple read query:<br/>
     * <pre>
     * $query = [
     *      'result' => 'result',
     *      'table'  => 'sample_table'
     * ];
     * </pre>
     * $this->lib_db->read($query);
     *
     * Simple where query:
     * $query = [
     *      'result' => 'result',
     *      'table'  => 'sample_table',
     *      'where'  => ['id' => '1', 'email' => 'good@gmail.com']
     * ];
     * $this->lib_db->read($query);
     *
     * Limit and join read query:
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
     * 
     * @param mixed $data
     * @param bool $retrieveAll
     * @return mixed
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

    public function translate($condition)
    {
        $conditions = [
            'null' => 'null',
            'contains' => 'contains',
            'between' => 'between',
            '=' => '=',
            '<' => '<',
            '<=' => '<=',
            '>=' => '>=',
            '>' => '>',
        ];
        if (array_key_exists($condition, $conditions)) {
            return $conditions[$condition];
        }
        return false;
    }
}
