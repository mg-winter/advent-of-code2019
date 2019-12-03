<?php
require_once 'calculate_fuel.php';

echo 'Simple';
echo PHP_EOL;
echo calculate_from_file($argv[1]);
echo PHP_EOL;
echo 'Compounded';
echo PHP_EOL;
echo calculate_from_file_compounded($argv[1]);
echo PHP_EOL;

?>