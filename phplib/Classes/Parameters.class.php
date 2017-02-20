<?php

/**
 * Description of parameters
 *
 * @author gelpi
 */
class Parameters {
    
    function __construct($params) {
        foreach (array_keys($params) as $k) {
            $this->$k = $params[$k];
        }
        return $this;
    }
    
    function expand($field, $compField) {
        $d = [];       
        foreach (explode(",", str_replace(' ', '', $this->$field)) as $k) {
                $d[$k] = 'on';
        }
        $this->$compField=$d;
        return $this;
    }
}
