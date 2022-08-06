<?php

namespace App\Libraries;

use Config\Database;

class DatabaseManager extends Database
{
    private $db, $builder;
    public function __construct()
    {
        $this->db = $this->connect();
    }
    // INSERT STATEMENT
    public function insert($table, $data, $batch = false, $xss_clean = true)
    {
        if ($xss_clean) {
            $data = $this->CI->security->xss_clean($data);
        }
        $created_at = date('Y-m-d H:i:s');
        $created_by = check_auth('user_id');
        if (!$batch) {
            $data['created_at'] = $created_at;
            $data['created_by'] = $created_by;
        } else {
            $data = array_map(function ($d) use ($created_at, $created_by) {
                return $d += [
                    'created_at' => $created_at,
                    'created_by' => $created_by
                ];
            }, $data);
        }
        $this->CI->db->trans_start();
        if (!$batch) {
            $this->CI->db->insert($table, $data);
            $id = $this->CI->db->insert_id();
        } else {
            $id = $this->CI->db->insert_batch($table, $data);
        }
        $affected_rows = $this->affected_rows();
        if ($affected_rows > 0) {
            $this->CI->db->trans_complete();
            if ($id == 0) {
                return $affected_rows;
            }
            return $id;
        } else {
            $this->CI->db->trans_complete();
            return false;
        }
    }

    // UPDATE STATEMENT
    public function update($table, $data, $where)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        if ($user_id = check_auth('user_id')) {
            $data['updated_by'] = $user_id;
        } else if (!array_key_exists('updated_by', $data)) {
            $data['updated_by'] = null;
        }
        $this->CI->db->update($table, $data, $where);
        return $this->affected_rows();
    }

    // DELETE STATEMENT
    public function delete($table, $where, $hard_delete = false)
    {
        if ($hard_delete) {
            if (is_array($where[0] ?? false)) {
                foreach ($where as $or_where) {
                    $this->CI->db->or_where($or_where);
                }
                $this->CI->db->delete($table);
            } else {

                $this->CI->db->delete($table, $where);
            }
        } else {
            $data['deleted_by'] = null;
            $data['deleted_at'] = date('Y-m-d H:i:s');
            if ($user_id = check_auth('user_id')) {
                $data['deleted_by'] = $user_id;
            }

            if (is_array($where[0] ?? false)) {
                $where = array_map(function ($e) {
                    return $e += ['deleted_at' => null];
                }, $where);
                foreach ($where as $or_where) {
                    $this->CI->db->or_group_start();
                    $this->CI->db->where($or_where);
                    $this->CI->db->group_end();
                }
                $this->CI->db->update($table, ['deleted_at' => $data['deleted_at'], 'deleted_by' => $data['deleted_by']]);
            } else {
                $where['deleted_at'] = null;
                $this->CI->db->update($table, $data, $where);
            }
        }
        return $this->affected_rows();
    }
    public function count_all($data)
    {

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
     * Read function
     * @param mixed $data
     * @param boolean $retrieve_all
     * @return mixed
     * ----------------------------------------------------------
     * Simple read query:
     * $query = [
     *      'result' => 'result',
     *      'table'  => 'sample_table'
     * ];
     * $this->lib_db->read($query);
     * ----------------------------------------------------------
     * Simple where query:
     * $query = [
     *      'result' => 'result',
     *      'table'  => 'sample_table',
     *      'where'  => ['id' => '1', 'email' => 'mantap']
     * ];
     * $this->lib_db->read($query);
     * ----------------------------------------------------------
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
     */
    public function read($data, $retrieve_all = false)
    {
        $this->builder = $this->db->table($data['table']);


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
     * AUTOCOMPLETE FUNCTION DO NOT CHANGE
     */
    public function insertID()
    {
        return $this->db->insertID();
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

    public function affectedRows()
    {
        $affectedRows = $this->builder->affectedRows();
        if ($affectedRows > 0) {
            return $affectedRows;
        }
        return 0;
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
