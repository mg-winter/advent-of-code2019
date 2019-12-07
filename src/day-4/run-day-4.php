<?php
require_once 'get_passwords.php';


echo 'Part 1';

echo PHP_EOL;
$res =  get_passwords(273025, 767253);
//$res =  get_passwords(273025, 320000);
echo format_array($res) . PHP_EOL;
echo count($res);
echo PHP_EOL;

// echo 'Part 2';
// echo PHP_EOL;
// echo find_intersections_by_steps_from_file($argv[1], $is_debug_argv);
// echo PHP_EOL;

?>