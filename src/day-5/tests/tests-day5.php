<?php
require_once 'process_intcode-v2.php';
require_once '../util/test-util.php';


function process_intcode_mock($program, $input_arr)  {
    $output = new Cached_Output();
    $new_state = process_intcode_arr($program, new Cached_Input($input_arr), $output, true);

    return ['output' => $output . '', 'state' => $new_state, ];
}

    $part1_tests = [
        ['input' => [[],[]], 'result' => ['output'=>'','state'=>[]]],
        ['input' => [[99],[]], 'result' => ['output'=>'','state'=>[99]]],
        ['input' => [[1,9,10,3,2,3,11,0,99,30,40,50],[]], 
            'result' => ['output'=>'','state'=>[3500,9,10,70,2,3,11,0,99,30,40,50]]],
        ['input' => [[1,0,0,0,99],[]], 'result' => ['output'=>'','state'=>[2,0,0,0,99]]],
        ['input' => [[2,3,0,3,99],[]], 'result' => ['output'=>'','state'=>[2,3,0,6,99]]],
        ['input' => [[2,4,4,5,99,0],[]], 'result' => ['output'=>'','state'=>[2,4,4,5,99,9801]]],
        ['input' => [[1,1,1,4,99,5,6,0,99],[]], 'result' => ['output'=>'','state'=>[30,1,1,4,2,5,6,0,99]]],
        ['input' => [[3,1,4,1,99],[7]], 'result' => ['output'=>'7','state'=>[3,7,4,1,99]]],
        ['input' => [[1002,4,3,4,33],[]], 'result' => ['output'=>'','state'=>[1002,4,3,4,99]]],
    ];


   

    output_suite('Part 1', $part1_tests, 'process_intcode_mock');



?>