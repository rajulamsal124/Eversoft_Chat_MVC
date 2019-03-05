<?php
// array('email' => 'dhpradeep25@gmail.com') ? 1 : 0

class Validator {

    private $passed = false;
    private $errors = array();

    public function check($source, $items = array()) { 
        /*
        'name': {
            'name': 'Your Name',
            'required': true,
            'min': 2,
            'max': 10
        }
        */
        foreach($items as $item => $rules) {
            foreach($rules as $rule => $rule_value) {
                $value = $source[$item];
                $item = htmlentities($item, ENT_QUOTES, 'UTF-8');
                if($rule === 'required' && empty($value)) {
                    $this->addError("{$item} is required"); 
                } else if (!empty($value)) {
                    switch($rule) {
                        case 'min':
                            if(strlen($value) < $rule_value) {
                                $this->addError("{$item} must be a minimum of {$rule_value} characters.");
                            }
                            break;
                        case 'max':
                            if(strlen($value) > $rule_value) {
                                $this->addError("{$item} must be a maximum of {$rule_value} characters.");
                            }
                            break;
                        case 'type':
                            if($rule_value == 'email') {
                                if (!filter_var($source[$item], FILTER_VALIDATE_EMAIL)) {
                                    $this->addError("Invalid {$item} format"); 
                                  }
                            }
                            else if ($rule_value == 'number') {
                                // validate number
                                if (!is_numeric($source[$item])) {
                                    $this->addError("{$item} should be numeric value.");
                                }
                            }
                            else if($rule_value == 'PHNumber') {
                                // if(!preg_match('/^+[0-9]{5}+$/', $source[$item])) {
                                //     $this->addError("{$item} field is invalid.");
                                // }
                            }
                            else if($rule_value == 'date') {
                                // validate date
                            // $test_arr  = explode('/', $source[$item]);
                            // if (count($test_arr) == 3) {
                            //     if (checkdate($test_arr[0], $test_arr[1], $test_arr[2])) {
                            //         $this->addError("{$item} should be date function.");
                            //     }
                            // }else{
                            //     $this->addError("{$item} should be date function.");
                            // }
                        }
                    }
                }
            }
        }

        if(empty($this->errors)) {
            $this->passed = true;
        }
    }

    public function addError($error) {
        $this->errors[] = $error;
    }

    public function errors() {
        return $this->errors;
    }

    public function passed() {
        return $this->passed;
    }
}


?>