<?php
require_once 'calculate_visible_asteroids.php';
require_once '../util/test-util.php';


$divisor_tests = [['input' =>[0], 'result' =>[0 => [0], 1 => [1]]],
                  ['input' =>[1], 'result' =>[0 => [0], 1 => [1]]],
                  ['input' =>[2], 'result' =>[0 => [0], 1 => [1], 2 => [2]]],
                  ['input' =>[3], 'result' =>[0 => [0], 1 => [1], 2 => [2], 3 => [3]]],
                  ['input' =>[10], 'result' =>[0 => [0], 1 => [1], 2 => [2], 4 => [2,2],
                                              3 => [3], 6 => [2,3], 9 => [3,3],
                                              8 => [2,2,2],
                                               5 =>[5], 10 => [2,5],
                                              7 => [7], 
                                               
                                          ]
                  ]

];

$part1_tests = [
                ['input' =>[[
                ['#','#'],
            

                        ]], 'result' =>['coords' => [0,0], 'num_visible' => 1]
                ],

                ['input' =>[[
                ['.','#','.','.','#'],
                ['.','.','.','.','.'],
                ['#','#','#','#','#'],
                ['.','.','.','.','#'],
                ['.','.','.','#','#'],

                        ]], 'result' =>['coords' => [3,4], 'num_visible' => 8]
                        ]
                    ];

$part1_tests_file = [
                ['input' =>['./tests/test-file-1.txt'], 'result' =>['coords' => [5,8], 'num_visible' => 33]],
                ['input' =>['./tests/test-file-2.txt'], 'result' =>['coords' => [1,2], 'num_visible' => 35]],
                ['input' =>['./tests/test-file-3.txt'], 'result' =>['coords' => [6,3], 'num_visible' => 41]],
                ['input' =>['./tests/test-file-4.txt'], 'result' =>['coords' => [11,13], 'num_visible' => 210]]
              ];
                    
output_suite('Divisor tests', $divisor_tests, 'get_divisors_up_to');
output_suite('Part 1 tests', $part1_tests, 'calculate_visible_asteroids');
output_suite('Part 1 tests - file', $part1_tests_file, 'calculate_visible_asteroids_from_file');
//output_suite('Part 2 tests', $part2_tests, 'decode_image');



?>