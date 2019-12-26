<?php
require_once 'apply_fft.php';
$Base_Pattern = [0, 1, 0, -1];

echo 'Part 1';
echo PHP_EOL;
echo apply_phases_from_file('/Users/Ria/Code/advent-of-code2019/data/day-16.txt', 100, $Base_Pattern);
echo PHP_EOL;





?>