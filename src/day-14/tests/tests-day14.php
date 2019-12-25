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

$calculate_max_fuel_tests = [
 
      ['input' => ['./tests/test-1.txt', 'ORE', 'FUEL', 31], 'result' => 1],
      ['input' => ['./tests/test-1.txt', 'ORE', 'FUEL', 29], 'result' => 0],
      ['input' => ['./tests/test-3.txt', 'ORE', 'FUEL', 1000000000000], 'result' => 82892753],
        ['input' => ['./tests/test-4.txt', 'ORE', 'FUEL', 1000000000000], 'result' => 5586022],
         ['input' => ['./tests/test-5.txt', 'ORE', 'FUEL', 1000000000000], 'result' => 460664]
];

output_suite('Part 1 tests', $calculate_ore_tests, 'calculate_ore_from_file');
output_suite('Part 2 tests', $calculate_max_fuel_tests, 'calculate_fuel_from_file');
?>