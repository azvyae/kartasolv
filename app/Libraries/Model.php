<?php

namespace App\Libraries;

use CodeIgniter\Model as OriginalModel;


class Model extends OriginalModel
{
    /**
     * Returns the model's defined validation rules so that they
     * can be used elsewhere, if needed.
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
                $rules += $this->validation->loadRuleGroup($options['add'][0]);
            }
        }
        return $rules;
    }
}
