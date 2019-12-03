<?php
require_once 'process_intcode.php';

echo 'Part 1';
echo PHP_EOL;
echo process_intcode_from_file($argv[1]);
echo PHP_EOL;

echo 'Part 2';
echo PHP_EOL;
echo try_substitute_intcode_from_file($argv[1], $argv[2], $argv[3]);

?>