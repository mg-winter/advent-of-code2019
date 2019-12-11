<?php
require_once 'process_intcode-v3.php';

echo 'Part 1';
echo PHP_EOL;
$input = new Cached_Input([1]);
$output = new Cached_Output();
echo process_intcode_from_file('/Users/Ria/Code/advent-of-code2019/data/day-9.txt', 
        $input, $output,
        true);
echo PHP_EOL;
echo 'Output: ' . PHP_EOL;
echo $output;
echo PHP_EOL;

echo 'Part 2';
$input = new Cached_Input([2]);
$output = new Cached_Output();
echo process_intcode_from_file('/Users/Ria/Code/advent-of-code2019/data/day-9.txt', 
        $input, $output,
        true);
echo PHP_EOL;
echo 'Output: ' . PHP_EOL;
echo $output;
echo PHP_EOL;




?>