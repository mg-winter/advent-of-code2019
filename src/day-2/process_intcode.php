
<?php

function check_index($arr, $index, $index_desc) {
    $max_index = count($arr) - 1;
    if ($index < 0 || $index > $max_index) {
        throw new Exception('Index for ' . $index_desc . ' out of bounds: ' 
                            . $index . 'Must be between 0 and ' 
                            . $max_index);
    }
}
function calculate($arr, $index, $num_params, $calc_func) {


    $output_slot = $index + $num_params + 1;
    check_index($arr, $output_slot, 'slot for output index');

    $output_index = $arr[$output_slot];
    check_index($arr, $output_index, 'output index');

    $arg_slots = range($index + 1, $index + $num_params);

    $get_arg_indices_from_arr = function($slot_index) use ($arr) {

        check_index($arr, $arr[$slot_index], 'argument index');
        return $arr[$slot_index];

    };

    $get_args_from_indices = function($arg_index) use ($arr) {
        return $arr[$arg_index];
    };

    $arg_indices = array_map($get_arg_indices_from_arr, $arg_slots);
    $args = array_map($get_args_from_indices, $arg_indices);

    $arr[$output_index] = call_user_func_array($calc_func, $args);
    return ['result' => $arr, 'next_index' => $output_slot + 1, 'halt' => false, 
            desc => $calc_func . ' ' . implode(',', $args) 
            . ' @pos ' . implode(',', $arg_indices) 
            . ' into position ' . $output_index];
} 

function calculate_binary($arr, $index, $calc_func) {
    return calculate($arr, $index, 2, $calc_func);
}

function calculate_add($arr, $index) {
    return calculate_binary($arr, $index, 'add');
}

function calculate_mult($arr, $index) {
    return calculate_binary($arr, $index, 'mult');
}

function halt($arr, $index) {
    return ['result' => $arr, 'next_index' => -1, 'halt' => true, 'desc' => 'halt @ position ' . $index];
}

function add($a, $b) {
    return $a + $b;
}

function mult($a, $b) {
    return $a * $b;
}


function process_intcode_arr($arr, $is_debug) {
    $functions = ['1' => 'calculate_add', '2' => 'calculate_mult', '99' => 'halt' ];
    $res = ['result' => $arr, 'next_index' => 0, 'halt' => false];
    $arr_length = count($arr);

    while(!$res['halt'] && $res['next_index'] < $arr_length) {
       
        $cur_index = $res['next_index'];
        $cur_arr = $res['result'];
        $cur_code = $cur_arr[$cur_index];
        $cur_func = $functions[$cur_code];

        if (!isset($cur_func)) {
            throw new Exception('Unknown code: ' + $cur_code);
        }
 
        $res = call_user_func_array($cur_func, [$cur_arr, $cur_index]);
        if ($is_debug) { 
            echo $cur_index . ': '. $res['desc'] . PHP_EOL;
        }
    }

    return $res['result'];
}

function process_intcode_from_file($path, $is_debug) {
    $str = file_get_contents($path);
    return implode(',', process_intcode_arr(explode(',', $str), $is_debug));
}

function try_substitute_intcode_from_file($path, $pos1, $pos2, $is_debug) {

    $str = file_get_contents($path);
    $arr = explode(',', $str);
    $arr[1] = $pos1;
    $arr[2] = $pos2;
    
    return process_intcode_arr($arr, $is_debug);
}


/**If difference is even, returns exact midpoint. Otherwise,
 * returns the higher midpoint.
 */
function get_midpoint($min, $max) {
    $diff = $max - $min;

    return $min + (intdiv($diff, 2)  + ($diff % 2));
}

function seek_result_in_range($test_func, $min_desired, $max_desired, $min_val, $max_val, $is_debug) {
    $cur_min = $min_val;
    $cur_max = $max_val;

    $res_range = ['options' => ['min_range' => $min_desired, 'max_range' => $max_desired]];
    do {
        $cur_val = get_midpoint($cur_min, $cur_max);
        
        if ($is_debug) {
            echo 'Try ' . $cur_val . ' to get within range ' 
                    . $min_desired . ',' . $max_desired;
            echo PHP_EOL;
        }

        $res = call_user_func_array($test_func, [$cur_val]);
        if (filter_var($res, FILTER_VALIDATE_INT, $res_range)) {
            return ['has_solution' => true, 'input' => $cur_val, 'result' => $res];
        } else if ($res < $min_desired) {
            $cur_min = $cur_val + 1;
        } else {
            $cur_max = $cur_val - 1;
        }
    } while ($cur_min <= $cur_max);

    return ['has_solution' => false];
}

/**
 * 
 * 
 * It is important that position 1 has big impact (gets multiplied a lot), and position 2
 * just gets added to position 1 at the end.  If we can
 * use position 1 to get within the range of (desired value -99, desired value), we can then
 * use position 2 to adjust the difference
 * 
 */
function seek_intcode_result_in_file($path, $desired_result, $is_debug) {
    
    $MIN_CODE = 0;
    $MAX_CODE = 99;

    $tester = function($pos1) use ($path, $is_debug) {
        return try_substitute_intcode_from_file($path, $pos1, $MIN_CODE, $is_debug)[0];
    };

    $seek_result = seek_result_in_range($tester, 
                                            $desired_result - $MAX_CODE + $MIN_CODE, 
                                            $desired_result - $MIN_CODE, 
                                            $MIN_CODE, $MAX_CODE, $is_debug);
    
    return $seek_result['has_solution'] ? ['has_solution' => true, 
                        'pos1' => $seek_result['input'],
                        'pos2' => $desired_result - $seek_result['result']] 
                    : $seek_result;
}

?>