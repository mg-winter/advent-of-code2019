<?php
require_once 'process_intcode.php';
require_once '../util/test-util.php';

    $part1_tests = [
        ['input' => [[]], 'result' => []],
        ['input' => [[99]], 'result' => [99]],
        ['input' => [[1,9,10,3,2,3,11,0,99,30,40,50]], 
            'result' => [3500,9,10,70,2,3,11,0,99,30,40,50]],
        ['input' => [[1,0,0,0,99]], 'result' => [2,0,0,0,99]],
        ['input' => [[2,3,0,3,99]], 'result' => [2,3,0,6,99]],
        ['input' => [[2,4,4,5,99,0]], 'result' => [2,4,4,5,99,9801]],
        ['input' => [[1,1,1,4,99,5,6,0,99]], 'result' => [30,1,1,4,2,5,6,0,99]],
    ];

   


    output_suite('Part 1', $part1_tests, 'process_intcode_arr');

?>