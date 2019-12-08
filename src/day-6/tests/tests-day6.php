<?php
require_once 'check_maps.php';
require_once '../util/test-util.php';

    $count_orbits_tests = [
        ['input' => [[
            'COM)B',
            'B)C',
            'C)D',
            'D)E',
            'E)F',
            'B)G',
            'G)H',
            'D)I',
            'E)J',
            'J)K',
            'K)L']], 'result' => 42],
            ['input' => [[
                'COM)B',
                'C)D',
                'B)C',
                'D)E',
                'E)F',
                'B)G',
                'G)H',
                'D)I',
                'E)J',
                'J)K',
                'K)L']], 'result' => 42],        
    ];



    $get_distance_tests = [
        ['input' => [[
            'COM)B',
           'COM)C',
            ], 'B', 'C'], 'result' => 0,       
        ],
        ['input' => [[
            'COM)B',
            'B)C',
            'C)D',
            'D)E',
            'E)F',
            'B)G',
            'G)H',
            'D)I',
            'E)J',
            'J)K',
            'K)L',
            'K)YOU',
            'I)SAN'], 'SAN', 'YOU'], 'result' => 4],       
     ];

   output_suite('Part 1 tests', $count_orbits_tests, 'count_orbits');
    output_suite('Part 2 tests', $get_distance_tests, 'get_distance');



?>