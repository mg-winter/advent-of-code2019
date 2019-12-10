<?php
require_once 'verify_image.php';

echo 'Part 1';
echo PHP_EOL;
echo format_array(verify_image_from_file('/Users/Ria/Code/advent-of-code2019/data/day-8.txt', 6, 25));
echo PHP_EOL;

echo 'Part 2';
echo PHP_EOL;
echo format_array(decode_image_from_file('/Users/Ria/Code/advent-of-code2019/data/day-8.txt', 6, 25));
echo PHP_EOL;




?>