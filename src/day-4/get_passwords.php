
<?php
require_once '../util/util.php';


function array_to_num($array) {
    $first_index = count($array) - 1;
    $mult = 1;
    $res = 0;
    for ($i = $first_index; $i >= 0; $i--) {
        $digit = isset($array[i]) ? $array[$i] : 0;
        $res += $array[$i] * $mult;
        $mult *= 10;
    }
    return $res;
}
function int_pow($base, $power) {
    $res = 1;
    for ($i = 0; $i < $power; $i++) {
        $res *= $base;
    }
    return $res;
}

function get_passwords_recursive($num_so_far, $digit_to_add, $remaining_digits, $max_num) {
 
    echo $num_so_far . '<-' .  $digit_to_add . ' ('. $remaining_digits . ' )'. PHP_EOL;
    $num = $num_so_far * 10 + $digit_to_add;   
    $has_repeat = preg_match('/(\d)\\1/', $num);
    $comparison_num = $num * int_pow(10, $remaining_digits);                                                               
    if ($comparison_num  > $max_num) {
        return [];
    } else if ($remaining_digits <= 0) {
        echo $num . PHP_EOL;
        return $has_repeat ? [$num] : [];
    } else {
        $next_digits = ($has_repeat || $remaining_digits >= 1)  ? range($digit_to_add, 9) 
                                                        : [$digit_to_add];
        
        $recurse = function($digit) use ($num, $digit_to_add, $remaining_digits, $max_num) {                                                                                                       
            return get_passwords_recursive($num, $digit, $remaining_digits - 1,  $max_num);                                                            
        };                                     
        return call_user_func_array('array_merge', array_map($recurse, $next_digits));
    }
}

function get_min_digit_arr($digits) {
    $prev_digit = $digits[0];
    $has_repeat = false;
    $num_digits = count($digits);
    $repeat_index = -1;

    for ($i = 1; $i < $num_digits; $i++) {
        if ($digits[$i] < $prev_digit) {
            for ($j = $i; $j < $num_digits; $j++) {
                $digits[$j] = $prev_digit;
            }
            $repeat_index = $i;
            break;
        } else {
            if ($repeat_index === -1 &&  $prev_digit === $digits[$i]) {
                $repeat_index = $i;
            }
            $prev_digit = $digits[$i];
        }  
        
    }

    if ($repeat_index < 1) {
        for ($i = $num_digits - 2; $i > 0; $i++) {
            if ($digits[$i] < 9) {
                $digits[$i] = $digits[$i] + 1;
                $repeat_index = $i+1;
                for ($j = $repeat_index; $j < $num_digits; $j++) {
                    $digits[$j] = $digits[$i];
                }
                break;
            }
        }
    }
    return $digits;
}


function get_passwords_from($digits_array, $set_length, $max_num) {
    $next_numbers = range($digits_array[$set_length]+1, 9);
    echo format_array($next_numbers) . PHP_EOL;
    $remaining_digits = count($digits_array) - $set_length - 1;
    $num_so_far = array_to_num(array_slice($digits_array, 0, $set_length));
    echo $num_so_far . ' - ' . $remaining_digits . PHP_EOL;
    $pw_results = array_map(function($digit) use ($num_so_far, $remaining_digits, $max_num) {
        echo $digit . PHP_EOL;
        return get_passwords_recursive($num_so_far, $digit, $remaining_digits, $max_num);
    }, $next_numbers);

    return call_user_func_array('array_merge', $pw_results);
}

/** only works for arrays with length > 2 */
function get_all_partial_passwords($digits_array, $max_num) {
    $swappable_range = range(count($digits_array) - 1, 1);
    $pw_results = array_map(function($pos) use ($digits_array, $max_num) {
        return get_passwords_from($digits_array, $pos, $max_num);
    }, $swappable_range);
    return call_user_func_array('array_merge', $pw_results);
}

function get_first_digits($min_digits, $max_digits) {
    if ($min_digits[0] >= $max_digits[0]) {
        return [];
    } else {
        return range($min_digits[0] + 1, $max_digits[0]);
    }
}

function get_full_passwords($min_digits, $max_num) {
    $max_digits = str_split($max_num);
    $digits = get_first_digits($min_digits, $max_digits);
    $digits_to_generate = count($min_digits) - 1;

    $pw_results = array_map(function($digit) use ($max_num, $digits_to_generate) {
        return get_passwords_recursive(0, $digit, $digits_to_generate, $max_num);
    }, $digits);

    return call_user_func_array('array_merge', $pw_results);
}


function get_passwords($min, $max) {
    $digits = str_split($min);
    $min_digits = get_min_digit_arr($digits);
    $num_digits = count($min_digits);
    $min_valid_num = array_to_num($min_digits);
   
    $sub_indices = range(count($min_digits)-1, 1);
   
    $partial_sets = get_all_partial_passwords($min_digits, $max);

    $full_sets = get_full_passwords($min_digits, $max);

    //echo count(call_user_func_array('array_merge', $partial_sets)) . ' + '. count(call_user_func_array('array_merge', $full_sets)) .  ' + 1' . PHP_EOL;
    return array_merge([$min_valid_num], $partial_sets, $full_sets);
}


?>