<?php
require_once 'get_passwords.php';
require_once '../util/test-util.php';

$array_to_num_tests = [
    ['input' => [[0]], 'result' => 0],
    ['input' => [[]], 'result' => 0],
    ['input' => [[1,2,3]], 'result' => 123],
    ['input' => [[5,0,1,1]], 'result' => 5011]
];

#$num_so_far, $digit_to_add, $remaining_digits, $has_repeat, $max_num

$get_passwords_rec_tests = [
    ['input' => [1, 1, 0, 12], 'result' => [11]],
    ['input' => [1, 2, 0,  12], 'result' => [12]],
    ['input' => [15, 2, 0,  17], 'result' => []],
    ['input' => [1, 1, 1,  120], 'result' => [111, 112, 113, 114, 115, 116, 117, 118, 119]],
];

$min_digit_arr_tests = [
    ['input' => [[1,5,1]], 'result' => [1,5,5]],
    ['input' => [[1,5,7]], 'result' => [1,5,7]],
    ['input' => [[8,5,7]], 'result' => [8,8,8]],
    ['input' => [[2,7,3,0,2,5]], 'result' => [2,7,7,7,7,7]]
];

$get_passwords_from_tests = [
    ['input' => [[1,2,3], 1, 150], 'result' => [133,134,135,136,137,138,139,144,145,146,147,148,149]],
    ['input' => [[4,4,4], 2, 500], 'result' => [445,446,447,448,449]],
];

$get_all_partial_passwords_tests = [
    ['input' => [[1,2,3], 200], 'result' => [124,125,126,127,128,129,133,134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,158,159,166,167,168,169,177,178,179,188,189,199]],
];

$get_first_digits_tests = [
    ['input' => [[1,2,3], [1,3,3]], 'result' => []],
    ['input' => [[1,2,3], [2,2,3]], 'result' => [2]],
    ['input' => [[5,6,7,8], [8,8,9,9]], 'result' => [6,7,8]],
];

$get_full_passwords_tests = [
    ['input' => [[1,2,3], 133], 'result' => []],
    ['input' => [[1,2,3], 223], 'result' => [222, 223]],
];

    output_suite('Get passwords from...', $get_passwords_from_tests, 'get_passwords_from');
    output_suite('Get all partials', $get_all_partial_passwords_tests, 'get_all_partial_passwords'); 
    output_suite('Get first digits', $get_first_digits_tests, 'get_first_digits');
    output_suite('Get full passwords', $get_full_passwords_tests, 'get_full_passwords');
    output_suite('Recursive function', $get_passwords_rec_tests, 'get_passwords_recursive');
    output_suite('Min digits', $min_digit_arr_tests, 'get_min_digit_arr');
    
?>