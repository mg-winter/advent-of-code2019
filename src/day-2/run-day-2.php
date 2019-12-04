<?php
require_once 'process_intcode.php';

$is_debug_argv = in_array('-d', $argv);
echo 'Part 1';
echo PHP_EOL;
echo process_intcode_from_file($argv[1], $is_debug_argv);
echo PHP_EOL;

echo 'Part 2';
echo PHP_EOL;

$seek_res =  seek_intcode_result_in_file($argv[1], 19690720, $is_debug_argv);
echo $seek_res['pos1'] . ',' . $seek_res['pos2'];
echo PHP_EOL;



?>