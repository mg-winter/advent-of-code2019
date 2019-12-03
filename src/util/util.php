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

?>