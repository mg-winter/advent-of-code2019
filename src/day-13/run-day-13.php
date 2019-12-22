<?php
require_once 'play_game.php';

echo 'Part 1';
echo PHP_EOL . get_tile_count_from_file('/Users/Ria/Code/advent-of-code2019/data/day-13.txt', true) . PHP_EOL;

echo 'Part b' . PHP_EOL;
echo PHP_EOL . play_game_from_file('/Users/Ria/Code/advent-of-code2019/data/day-13-b.txt', false) . PHP_EOL;

?>