<?php
require_once 'calculate_ore.php';

echo 'Part 1';
echo PHP_EOL;
echo calculate_ore_from_file('/Users/Ria/Code/advent-of-code2019/data/day-14.txt', 'ORE', 'FUEL');
echo PHP_EOL;

echo 'Part 2';
echo PHP_EOL;
echo calculate_fuel_from_file('/Users/Ria/Code/advent-of-code2019/data/day-14.txt', 'ORE', 'FUEL', 1000000000000);
echo PHP_EOL;





?>