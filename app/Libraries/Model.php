<?php

namespace App\Libraries;

use CodeIgniter\Model as OriginalModel;

/**
 * Extends from original Codeigniter Model.
 * 
 * This class has certain method to override its original method and added some functionalities.
 * 
 * @package KartasolvApp\Libraries
 */
class Model extends OriginalModel
{
    /**
     * Returns the model's defined validation rules so that they
     * can be used elsewhere, if needed.
     *
     * Added ['add'] option to combine model validationRules with other RuleGroup rules.
     * 
     * @param array $options Options
     */
    public function getValidationRules(array $options = []): array
    {
        $rules = $this->validationRules;

        // ValidationRules can be either a string, which is the group name,
        // or an array of rules.
        if (is_string($rules)) {
            $rules = $this->validation->loadRuleGroup($rules);
        }

        if (isset($options['except'])) {
            $rules = array_diff_key($rules, array_flip($options['except']));
        } elseif (isset($options['only'])) {
            $rules = array_intersect_key($rules, array_flip($options['only']));
        }
        if (isset($options['add'])) {
            if (count($options) > 1) {
                foreach ($options['add'] as $opt) {
                    $rules += $this->validation->getRuleGroup($opt);
                }
            } else {
                $rules += $this->validation->getRuleGroup($options['add'][0]);
            }
        }
        return $rules;
    }

    /**
     * Fetches the row of database
     *
     * @param array|int|string|null $id One primary key or an array of primary keys
     *
     * @return array|object|null The resulting row of data, or null.
     */
    public function find($id = null, $strict = false)
    {
        if ($strict && !$id) {
            return null;
        }
        $singleton = is_numeric($id) || is_string($id);

        if ($this->tempAllowCallbacks) {
            // Call the before event and check for a return
            $eventData = $this->trigger('beforeFind', [
                'id'        => $id,
                'method'    => 'find',
                'singleton' => $singleton,
            ]);

            if (!empty($eventData['returnData'])) {
                return filterOutput($eventData['data']);
            }
        }

        $eventData = [
            'id'        => $id,
            'data'      => $this->doFind($singleton, $id),
            'method'    => 'find',
            'singleton' => $singleton,
        ];

        if ($this->tempAllowCallbacks) {
            $eventData = $this->trigger('afterFind', $eventData);
        }

        $this->tempReturnType     = $this->returnType;
        $this->tempUseSoftDeletes = $this->useSoftDeletes;
        $this->tempAllowCallbacks = $this->allowCallbacks;

        return filterOutput($eventData['data']);
    }

    /**
     * Assign modified_by field in the database.
     * @param array $data
     * @return array
     */
    protected function setModifiedBy(array $data)
    {
        $data['data']['modified_by'] = checkAuth('userId');
        return $data;
    }
    /**
     * Assign created_by field in the database.
     * @param array $data
     * @return array
     */
    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = checkAuth('userId');
        return $data;
    }

    /**
     * Assign deleted_by field in the database.
     * @param array $data
     * @return void
     */
    protected function setDeletedBy(array $data)
    {
        if ($this->useSoftDeletes) {
            $this->builder();
            foreach ($data['id'] as $id) {
                $this->builder->orWhere($this->primaryKey, $id);
            }
            $this->builder->update(['deleted_by' => checkAuth('userId')]);
        }
    }
}
