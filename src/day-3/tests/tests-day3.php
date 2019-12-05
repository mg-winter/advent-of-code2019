<?php
require_once 'find_intersections.php';
require_once '../util/test-util.php';

    $part1_tests = [
        ['input' => [[['R8','U5','L5','D3'], ['U7','R6','D4','L4']],true], 'result' => 6],
        ['input' => [[['R75','D30','R83','U83','L12','D49','R71','U7','L72'],
                ['U62','R66','U55','R34','D71','R55','D58','R83']],true], 'result' => 159],
        ['input' => [[['R98','U47','R26','D63','R33','U87','L62','D20','R33','U53','R51'],
        ['U98','R91','D20','R16','D67','R40','U7','R15','U6','R7']],true], 'result' => 135]
    ];


    output_suite('Part 1', $part1_tests, 'find_intersections_arr');
?>