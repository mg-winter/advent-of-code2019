<?php
require_once 'calculate_visible_asteroids.php';
require_once '../util/test-util.php';


$normalize_angle_tests = [['input' => [0], 'result' => 0],
  ['input' => [10], 'result' => 10],
  ['input' => [360], 'result' => 360],
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

$part2_tests_file = [
                  ['input' =>['./tests/test-file-5.txt', '8,3', 1], 'result' => '8,1'],
                  ['input' =>['./tests/test-file-5.txt', '8,3', 2], 'result' => '9,0'],
                  ['input' =>['./tests/test-file-5.txt', '8,3', 3], 'result' => '9,1'],
                  ['input' =>['./tests/test-file-5.txt', '8,3', 4], 'result' => '10,0'],
                  ['input' =>['./tests/test-file-5.txt', '8,3', 5], 'result' => '9,2'],
                  ['input' =>['./tests/test-file-5.txt', '8,3', 14], 'result' => '12,3'],
                  ['input' =>['./tests/test-file-5.txt', '8,3', 28], 'result' => '6,1'],
                  ['input' =>['./tests/test-file-4.txt', '11,13', 1], 'result' => '11,12'],
                  ['input' =>['./tests/test-file-4.txt', '11,13', 2], 'result' => '12,1'],
                  ['input' =>['./tests/test-file-4.txt', '11,13', 3], 'result' => '12,2'],
                  ['input' =>['./tests/test-file-4.txt', '11,13', 10], 'result' => '12,8'],
                  ['input' =>['./tests/test-file-4.txt', '11,13', 20], 'result' => '16,0'],
                  ['input' =>['./tests/test-file-4.txt', '11,13', 50], 'result' => '16,9'],
                  ['input' =>['./tests/test-file-4.txt', '11,13', 100], 'result' => '10,16'],
                  ['input' =>['./tests/test-file-4.txt', '11,13', 199], 'result' => '9,6'],
                  ['input' =>['./tests/test-file-4.txt', '11,13', 200], 'result' => '8,2'],
                  ['input' =>['./tests/test-file-4.txt', '11,13', 201], 'result' => '10,9'],
                  ['input' =>['./tests/test-file-4.txt', '11,13', 299], 'result' => '11,1'],
              ];
                    
output_suite('Part 1 tests', $part1_tests, 'calculate_visible_asteroids');
output_suite('Part 1 tests - file', $part1_tests_file, 'calculate_visible_asteroids_from_file');

output_suite('Part 2 tests', $part2_tests_file, 'get_nth_to_vaporise_from_file');



?>