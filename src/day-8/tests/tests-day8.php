<?php
require_once 'verify_image.php';
require_once '../util/test-util.php';


$part1_tests = [['input' =>[[0,1,0,
                            0,2,2,
                            
                            1,2,1,
                            2,3,2,

                        ],2,3], 'result' =>['num_0s' => 0, 'num_1s' => 2, 'num_2s' => 3, 'hash' => 6]
                        ]
                    ];
$part2_tests = [['input' => [[0,2,
                              2,2,
                              
                              1,1,
                              2,2,
                              
                              2,2,
                              1,2,
                              
                              0,0,
                              0,0], 2, 2], 'result' => file_get_contents('./tests/img-1.txt')],
                              ['input' => [
                            [   0,1,1,1,1,1,1,1,1,1,1,1,0,
                                1,0,1,1,1,1,1,1,1,1,1,0,1,
                                1,1,0,1,1,1,0,1,1,1,0,1,1,
                                1,1,1,0,1,1,1,1,1,0,1,1,1], 4, 13
                              ], 'result' => file_get_contents('./tests/img-2.txt')],];
output_suite('Part 1 tests', $part1_tests, 'verify_image');
output_suite('Part 2 tests', $part2_tests, 'decode_image');



?>