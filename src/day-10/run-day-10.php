<?php
require_once 'calculate_visible_asteroids.php';

echo 'Part 1';
echo PHP_EOL;
$res = calculate_visible_asteroids_from_file('/Users/Ria/Code/advent-of-code2019/data/day-10.txt');
echo format_array($res);
echo PHP_EOL;

echo 'Part 2';
echo PHP_EOL;
echo format_array(get_nth_to_vaporise_from_file('/Users/Ria/Code/advent-of-code2019/data/day-10.txt', 
                                                                    implode(',', $res['coords']), 200));
echo PHP_EOL;




?>