<?php 
 
    function read_array($args_arr) {

 
        if (!isset($args_arr) || !isset($args_arr[1])) {
            return [];
        } else {
            return explode(',', $args_arr[1]);
        }
    }

    function read_int_array($args_arr) {
        return array_map('intval', read_array($args_arr));
    }

     //https://stackoverflow.com/a/173479
     function is_associative($arr) {
        return isset($arr) && array_keys($arr) === range(0, count($arr) - 1);
    }

    function format_array($val) {
        if (is_array($val)) {
            $inner = '';
            if (is_associative($arr)) {
                $keys = array_keys($val);
                $stringifier = function($key) use ($val) {
                    return $key . ' => '. format_array($val[$key]);
                };

                $strings = array_map($stringifier, $keys);
                $inner = implode(', ',  $strings);
            } else {
                $inner = implode(',', array_map('format_array', $val));
            }
            return '[' . $inner . ']';
            
        } else {
            return $val;
        }
    }



?>