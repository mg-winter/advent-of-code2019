<?php
require_once 'test_amplifiers.php';
require_once '../util/test-util.php';

$seq_tests = [
    ['input' => [[]], 'result' => [[]]],
    ['input' => [[5]], 'result' => [[5]]],
    ['input' => [[1,2]], 'result' => [[1,2], [2,1]]],
    ['input' => [[1,2,3]], 'result' => [[1,2,3], [1,3,2],[2,1,3],[2,3,1],[3,1,2],[3,2,1]]],
];

$test_amplifier_tests = [
    ['input'=>[[2,3,4,5,7,8],[3,0,3,1,1,0,1,0,4,0,99],false], 'result' => 29],
    ['input'=>[[4,3,2,1,0],[3,15,3,16,1002,16,10,16,1,16,15,15,4,15,99,0,0],true], 'result' => 43210],
    ['input'=>[[0,1,2,3,4],[3,23,3,24,1002,24,10,24,1002,23,-1,23,
                 101,5,23,23,1,24,23,23,4,23,99,0,0],true], 'result' => 54321],
    ['input'=>[[1,0,4,3,2],[3,31,3,32,1002,32,10,32,1001,31,-2,31,1007,31,0,33,
              1002,33,7,33,1,33,31,31,1,32,31,31,4,31,99,0,0,0],true], 'result' => 65210]
];
$part1_tests = [
    ['input' => [[3,15,3,16,1002,16,10,16,1,16,15,15,4,15,99,0,0],false], 
                            'result' => ['seq'=>[4,3,2,1,0],'max'=>43210]],
    ['input' => [[3,23,3,24,1002,24,10,24,1002,23,-1,23,
                 101,5,23,23,1,24,23,23,4,23,99,0,0],true], 
                             'result' => ['seq'=>[0,1,2,3,4],'max'=>54321]],
     ['input' => [[3,31,3,32,1002,32,10,32,1001,31,-2,31,1007,31,0,33,
              1002,33,7,33,1,33,31,31,1,32,31,31,4,31,99,0,0,0],true], 
                             'result' => ['seq'=>[1,0,4,3,2],'max'=>65210]],
   
];

//output_suite('Sequence generator', $seq_tests, 'get_sequences');
output_suite('Sequence test', $test_amplifier_tests, 'test_phase_sequence');
//output_suite('Part 1 tests', $part1_tests, 'test_amplifiers');
//output_suite('Part 2 tests', $get_distance_tests, 'get_distance');



?>