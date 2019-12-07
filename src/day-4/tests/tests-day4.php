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
    ['input' => [1, 2, 0,  12], 'result' => []],
    ['input' => [0, 1, 2,  300], 'result' => [111, 112, 113, 114, 115, 116, 117, 118, 119, 122, 133, 144, 155, 166, 177, 188, 199]],
    ['input' => [1, 1, 1,  120], 'result' => [111, 112, 113, 114, 115, 116, 117, 118, 119]],
    ['input' => [1, 5, 2,  1584], 'result' => [
        1555, 1556, 1557, 1558, 1559,
        1566, 1577   
    ]]
];

$min_digit_arr_tests = [
    ['input' => [[1,5,1]], 'result' => [1,5,5]],
    ['input' => [[1,5,7]], 'result' => [1,6,6]],
    ['input' => [[8,5,7]], 'result' => [8,8,8]],
    ['input' => [[1,2,3,4]], 'result' => [1,2,4,4]],
    ['input' => [[2,7,3,0,2,5]], 'result' => [2,7,7,7,7,7]]
];

$get_passwords_from_tests = [
    ['input' => [[1,2,3], 1, 200], 'result' => [133, 144, 155, 166, 177, 188, 199]],
    ['input' => [[4,4,4], 2, 500], 'result' => [445,446,447,448,449]],
    ['input' => [[5,5,6,6], 2, 6000], 'result' => [5577, 5578, 5579, 5588, 5589, 5599]],
];

$get_all_partial_passwords_tests = [
    ['input' => [[1,2,3], 200], 'result' => [133, 144, 155, 166, 177, 188, 199]],
    ['input' => [[4,4,4], 500], 'result' => [445,446,447,448,449, 455, 466, 477, 488, 499]],
    ['input' => [[5,5,6,6], 6000], 'result' => [5567, 5568, 5569, 
                                                    5577, 5578, 5579, 5588, 5589, 5599,
                                                    5666, 5667, 5668, 5669,
                                                    5677, 5688, 5699,
                                                    5777, 5778, 5779,
                                                    5788, 5799,
                                                    5888, 5889,
                                                    5899,
                                                    5999]],
];

$get_first_digits_tests = [
    ['input' => [[1,2,3], [1,3,3]], 'result' => []],
    ['input' => [[1,2,3], [2,2,3]], 'result' => [2]],
    ['input' => [[5,6,7,8], [8,8,9,9]], 'result' => [6,7,8]],
];

$get_full_passwords_tests = [
    ['input' => [[1,2,3], 133], 'result' => []],
    ['input' => [[1,2,3], 223], 'result' => [222, 223]],
    ['input' => [[5,6,7,8], 8950], 'result' => [6666, 6667, 6668, 6669,
                                                6677, 6678, 6679,
                                                6688, 6689,
                                                6699,
                                                6777, 6778, 6779,
                                                6788, 6799,
                                                6888, 6889, 6899,
                                           
                                                6999,
                                                7777, 7778, 7779,
                                                7788, 7789, 7799,
                                                7888, 7889,
                                                7899,
                                                7999,
                                                8888, 8889, 8899]],
];





    // $part1_tests = [
    //     ['input' => [[['R8','U5','L5','D3'], ['U7','R6','D4','L4']],true], 'result' => 6],
    //     ['input' => [[['R75','D30','R83','U83','L12','D49','R71','U7','L72'],
    //             ['U62','R66','U55','R34','D71','R55','D58','R83']],true], 'result' => 159],
    //     ['input' => [[['R98','U47','R26','D63','R33','U87','L62','D20','R33','U53','R51'],
    //     ['U98','R91','D20','R16','D67','R40','U7','R15','U6','R7']],true], 'result' => 135]
    // ];

    // $part2_tests = [
    //     ['input' => [[['R8','U5','L5','D3'], ['U7','R6','D4','L4']],true], 'result' => 30],
    //     ['input' => [[['R75','D30','R83','U83','L12','D49','R71','U7','L72'],
    //             ['U62','R66','U55','R34','D71','R55','D58','R83']],true], 'result' => 610],
    //     ['input' => [[['R98','U47','R26','D63','R33','U87','L62','D20','R33','U53','R51'],
    //     ['U98','R91','D20','R16','D67','R40','U7','R15','U6','R7']],true], 'result' => 410]
    // ];


    output_suite('Get passwords from...', $get_passwords_from_tests, 'get_passwords_from');
    output_suite('Get all partials', $get_all_partial_passwords_tests, 'get_all_partial_passwords'); 
    output_suite('Get first digits', $get_first_digits_tests, 'get_first_digits');
    output_suite('Get full passwords', $get_full_passwords_tests, 'get_full_passwords');
    //output_suite('Recursive function', $get_passwords_rec_tests, 'get_passwords_recursive');
    //output_suite('Min digits', $min_digit_arr_tests, 'get_min_digit_arr');
    //output_suite('Part 1', $part1_tests, 'find_intersections_arr');
    //output_suite('Part 2', $part2_tests, 'find_intersections_by_steps_arr');
?>