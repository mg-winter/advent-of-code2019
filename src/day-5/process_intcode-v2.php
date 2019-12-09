
<?php
require_once '../util/util.php';

abstract class Input {
    abstract function read();
}

abstract class Output {
    abstract function write($value);
}

class Cached_Input extends Input {
    private $Storage;

    public function __construct($values) {
        $this->Storage = $values;
    }

    public function read() {
        return array_pop($this->Storage);
    }
}

class Cached_Output extends Output {
    private $Storage = [];

    public function write($value) {
        echo 'Value is ' . $value;
        return array_push($this->Storage, $value) - 1;
    }

    public function get_line($index) {
        return $this->Storage[$index];
    } 

    public function get_last() {
        return $this->Storage[count($this->Storage) - 1];
    }

    public function __toString() {
        return implode(PHP_EOL, array_map(function($line) {
            return $line . '';
        },$this->Storage));
    }
}

function check_index($arr, $index, $index_desc) {
    $max_index = count($arr) - 1;
    if ($index < 0 || $index > $max_index) {
        throw new Exception('Index "'.$index.'" for ' . $index_desc . ' out of bounds: ' 
                            . $index . '. Must be between 0 and ' 
                            . $max_index);
    }
}

function get_value($arr, $base_index, $offset, $modes) {
    
    $index = $base_index + $offset;
    $mode = isset($modes[$offset-1]) ? $modes[$offset-1] : 0;

    check_index($arr, $index, 'parameter address (mode "' . $mode . '")');
    $val = $arr[$index];

    if (isset($mode) && $mode == 1) {
        return $val;
    } else {
        check_index($arr, $val, 'parameter address (referenced from "' . $index . '")');
        return $arr[$val];
    }
}

function get_values($arr, $base_index, $num_params, $modes) {
    return array_map(function($offset) use ($arr, $base_index, $modes) {
        return get_value($arr, $base_index, $offset, $modes);
    }, range(1, $num_params));
}
function calculate($arr, $index, $modes, $num_params, $calc_func, $io) {


    
    $output_slot = $index + $num_params + 1;
    check_index($arr, $output_slot, 'slot for output index');

    $output_index = $arr[$output_slot];
    check_index($arr, $output_index, 'output index');

    $args = get_values($arr, $index, $num_params, $modes);

    $arr[$output_index] = call_user_func_array($calc_func, $args);
    return ['result' => $arr, 'next_index' => $output_slot + 1, 'halt' => false, 
            'desc' => $calc_func . ' ' . implode(',', $args) 
            . ' into position ' . $output_index];
} 

function calculate_binary($arr, $index, $modes, $calc_func, $io) {
    return calculate($arr, $index, $modes, 2, $calc_func, $io);
}

function calculate_add($arr, $index, $modes, $io) {
    return calculate_binary($arr, $index, $modes, 'add', $io);
}

function calculate_mult($arr, $index, $modes, $io) {
    return calculate_binary($arr, $index, $modes, 'mult', $io);
}

function calculate_less_than($arr, $index, $modes, $io) {
    return calculate_binary($arr, $index, $modes, 'less_than', $io);
}

function calculate_equals($arr, $index, $modes, $io) {
    return calculate_binary($arr, $index, $modes, 'equals', $io);
}

function save_input($arr, $index, $modes, $io) {
    $input = $io['input']->read();
    check_index($arr, $index+1, 'output slot');
    $output_index = $arr[$index + 1];
    $arr[$output_index] = $input;

    return ['result' => $arr, 'next_index' => $index + 2, 'halt' => false, 
    'desc' => 'saved ' . $input . ' into position ' . $output_index];
}

function jump_if_true($arr, $index, $modes, $io) {
    return jump_if($arr, $index, 2, $modes, 3, 
                        0 != get_value($arr, $index, 1, $modes));
}

function jump_if_false($arr, $index, $modes, $io) {
    return jump_if($arr, $index, 2, $modes, 3, 
                        0 == get_value($arr, $index, 1, $modes));
}

function jump_if($arr, $index, $jump_val_offset, $modes, $slots_to_skip, $is_jump) {

    $next_index = $is_jump ? get_value($arr, $index, $jump_val_offset, $modes) 
                : $index + $slots_to_skip;
    return ['result' => $arr, 'next_index' => $next_index, 'halt' => false, 
    'desc' => $is_jump ? 'jump to ' . $next_index : 'continue to ' . $next_index];
}


function output($arr, $index, $modes, $io) {
    $output = $io['output'];
    $val_index = $index + 1;

    $val_to_output = get_value($arr, $index, 1, $modes);
    
    $output_res = $output->write($val_to_output);

    return ['result' => $arr, 'next_index' => $index + 2, 'halt' => false, 
    'desc' => 'output ' . $val_to_output . ', got back ' . $output_res];
}


function halt($arr, $index, $modes, $io) {
    return ['result' => $arr, 'next_index' => -1, 'halt' => true, 'desc' => 'halt @ position ' . $index];
}

function add($a, $b) {
    return $a + $b;
}

function mult($a, $b) {
    return $a * $b;
}

function less_than($a, $b) {
    return $a < $b;
}

function equals($a, $b) {
    return $a == $b;
}


function process_intcode_arr($arr, $input, $output, $is_debug) {
    $functions = ['01' => 'calculate_add', 
                    '02' => 'calculate_mult', 
                    '03' => 'save_input',
                    '04' => 'output',
                    '05' => 'jump_if_true',
                    '06' => 'jump_if_false',
                    '07' => 'calculate_less_than',
                    '08' => 'calculate_equals',
                    '99' => 'halt' ];
    $res = ['result' => $arr, 'next_index' => 0, 'halt' => false];
    $arr_length = count($arr);

    $io = ['input' => $input, 'output' => $output];

    while(!$res['halt'] && $res['next_index'] < $arr_length) {
       
        $cur_index = $res['next_index'];
        $cur_arr = $res['result'];

        $cur_code = $cur_arr[$cur_index];

        $opcode_pos = strlen($cur_code) - 2;
        $opcode =  $opcode_pos > 0 ? substr($cur_code, $opcode_pos) 
                    : str_pad($cur_code, 2, "0", STR_PAD_LEFT);

        $cur_func = $functions[$opcode];
        if (!isset($cur_func)) {
            throw new Exception('Unknown code: ' + $cur_code);
        }

        $modes = $opcode_pos > 0 ? array_reverse(str_split(substr($cur_code, 0, $opcode_pos))) 
                            : [];
        if ($is_debug) {
            echo PHP_EOL . $cur_index . ' (' . $cur_func . ')';
        }
        $res = call_user_func_array($cur_func, [$cur_arr, $cur_index, $modes, $io]);
        if ($is_debug) { 
            echo ': '. $res['desc'] . PHP_EOL;
        }
    }

    return $res['result'];
}

function process_intcode_from_file($path, $input, $output, $is_debug) {
    $str = file_get_contents($path);
    return implode(',', process_intcode_arr(explode(',', $str), $input, $output, $is_debug));
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