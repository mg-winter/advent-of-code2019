<?php
require_once 'find_intersections.php';

$is_debug_argv = in_array('-d', $argv);
echo 'Part 1';
echo PHP_EOL;
echo find_intersections_from_file($argv[1], $is_debug_argv);
echo PHP_EOL;

?>