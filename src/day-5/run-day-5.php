<?php
require_once 'process_intcode-v2.php';

echo 'Part 1';
echo PHP_EOL;
$input = new Cached_Input([1]);
$output = new Cached_Output();
echo process_intcode_from_file('/Users/Ria/Code/advent-of-code2019/data/day-5.txt', 
        $input, $output,
        true);
echo PHP_EOL;
echo $output;
echo PHP_EOL;

// echo 'Part 2';
// echo PHP_EOL;

// $seek_res =  seek_intcode_result_in_file($argv[1], 19690720, $is_debug_argv);
// echo $seek_res['pos1'] . ',' . $seek_res['pos2'];
// echo PHP_EOL;



?>