<?php
require_once 'get_passwords.php';


echo 'Part 1';

echo PHP_EOL;
$res =  get_passwords(273025, 767253);
echo format_array($res) . PHP_EOL;
echo count($res);
echo PHP_EOL;

echo 'Part 2';
echo PHP_EOL;
$res =  get_passwords_b(273025, 767253);
echo format_array($res) . PHP_EOL;
echo count($res);
echo PHP_EOL;

?>