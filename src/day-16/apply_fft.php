
<?php
require_once '../util/util.php';



function get_position($input, $position,  $base_pattern) {
    $pattern = [];


    $i = 0;
    $base_pattern_length = count($base_pattern);
    $num_repeats = $position + 1;
    while (count($pattern) < count($input)) {
        $cur_repeats = $i <= 0 ? $num_repeats - 1 : $num_repeats;
         for ($j = 0; $j < $cur_repeats; $j++) {
            
            $index_to_add = $i % $base_pattern_length;
            array_push($pattern, $base_pattern[$index_to_add]);
        }
       
        $i++;
    }
 
    $len = count($input);
    $pattern_len = count($pattern);
    
    $res = 0;
    for ($i = 0; $i < $len; $i++) {
       
        $pattern_index =  $i;
        $res += $pattern[$pattern_index] * $input[$i];
    }
    return abs($res % 10);
}


function apply_phase($input, $base_pattern) {
    $digits = array_map(function($pos) use ($input, $base_pattern) {
        return get_position($input, $pos, $base_pattern);
    }, range(0, count($input) - 1));
    return $digits;
}

function apply_phases($input, $num_phases, $base_pattern) {
    $res = $input;
    for ($i = 0; $i < $num_phases; $i++) {
        $res = apply_phase($res, $base_pattern);
    }

    return $res;
}

function apply_phases_from_file($path, $num_phases, $base_pattern) {
   
    $str = file_get_contents($path);
    $input = str_split($str);
    return implode('',  array_splice(apply_phases($input, $num_phases, $base_pattern), 0, 8));
}



?>