<?php
require_once 'calculate_ore.php';
require_once '../util/test-util.php';

$calculate_ore_tests = [
    ['input' => ['./tests/test-1.txt', 'ORE', 'FUEL'], 'result' => 31],
   
       ['input' => ['./tests/test-2.txt', 'ORE', 'FUEL'], 'result' => 165],
        ['input' => ['./tests/test-3.txt', 'ORE', 'FUEL'], 'result' => 13312],
        ['input' => ['./tests/test-4.txt', 'ORE', 'FUEL'], 'result' => 180697],
       ['input' => ['./tests/test-5.txt', 'ORE', 'FUEL'], 'result' => 2210736]
];

output_suite('Part 1 tests', $calculate_ore_tests, 'calculate_ore_from_file');
?>