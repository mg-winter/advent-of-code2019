<?php
require_once 'calculate_energy.php';

$input = [['x'=>9, 'y'=>13, 'z'=>-8],
['x'=>-3, 'y'=>16, 'z'=>-17],
['x'=>-4, 'y'=>11, 'z'=>-10],
['x'=>0, 'y'=>-2, 'z'=>-2]];

echo 'Part 1';
echo PHP_EOL;
echo format_array(calculate_energy($input, 1000));
echo PHP_EOL;

echo 'Part 2';
$start_time = time();
echo PHP_EOL;
echo format_array(calculate_steps_until_repeat($input));
$end_time = time();

echo PHP_EOL . 'Done in ' . ($end_time - $start_time) . 's';
echo PHP_EOL;





?>