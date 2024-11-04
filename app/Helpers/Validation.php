<?php

class Validation{
    public function validate($data, $rules){
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            if(isset($data[$field])){
                foreach ($fieldRules as $rule) {
                    switch ($rule) {
                        case 'required':
                            if (empty($data[$field])) {
                                array_push($errors, ucfirst($field) . ' is required.');
                            }
                            break;
                        case 'numeric':
                            if (!is_numeric($data[$field])) {
                                array_push($errors, ucfirst($field) . ' must be a number.');
                            }
                            break;
                        case strpos($rule, 'min:') === 0 && is_numeric($data[$field]) && 
                            $minValue = (int)substr($rule, 4);
                            if ($data[$field] < $minValue) {
                                array_push($errors, ucfirst($field) . ' must be at least ' . $minValue . '.');
                            }
                            break;
                        case (strpos($rule, 'max:') === 0 && is_numeric($data[$field])):
                            $maxValue = (int)substr($rule, 4);
                            if ($data[$field] > $maxValue) {
                                array_push($errors, ucfirst($field) . ' must not exceed ' . $maxValue . '.');
                            }
                            break;
                        case (strpos($rule, 'equal:') === 0):
                            $validValues = explode('|', substr($rule, 6));
                            if (!in_array($data[$field], $validValues)) {
                                array_push($errors, ucfirst($field) . ' must be one of: ' . implode(', ', $validValues) . '.');
                            }
                            break;
    
                        default:
                            break;
                    }
                }
            }else{
                if (in_array('required', $fieldRules)) {
                    array_push($errors, ucfirst($field) . ' is required.');
                }
            }
        }

        return empty($errors) ? '' : implode(' ', $errors);
    }
}