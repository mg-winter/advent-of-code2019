<?php
require_once 'calculate_energy.php';
require_once '../util/test-util.php';

$input1 = [
  ['x'=>-1, 'y'=>0, 'z'=>2],
  ['x'=>2, 'y'=>-10, 'z'=>-7],
  ['x'=>4, 'y'=>-8, 'z'=>8],
  ['x'=>3, 'y'=>5, 'z'=>-1]
];

$input2 = [
  ['x'=>-8, 'y'=>-10, 'z'=>0],
['x'=>5, 'y'=>5, 'z'=>10],
['x'=>2, 'y'=>-7, 'z'=>3],
['x'=>9, 'y'=>-8, 'z'=>-3]
];


$part1_tests = [
  ['input' =>[$input1, 0], 'result' =>['moons' => [
      ['position' =>[-1,0,2], 'velocity' => [0,0,0]],
      ['position' =>[2,-10,-7], 'velocity' => [0,0,0]],
      ['position' =>[4,-8,8], 'velocity' => [0,0,0]],
      ['position' =>[3,5,-1], 'velocity' => [0,0,0]],
    ], 'energy' => 0]
   ],
  ['input' =>[$input1, 1], 'result' =>['moons' => [
      ['position' =>[2,-1,1], 'velocity' => [3,-1,-1]],
      ['position' =>[3,-7,-4], 'velocity' => [1,3,3]],
      ['position' =>[1,-7,5], 'velocity' => [-3,1,-3]],
      ['position' =>[2,2,-0], 'velocity' => [-1,-3,1]],
    ], 'energy' => 229]
  ],
];

$common_multiple_tests = [
  ['input' => [[2]], 'result' => 2 ],
  ['input' => [[2,2]], 'result' => 2 ],
  ['input' => [[2, 8]], 'result' => 8],
  ['input' => [[7, 5]], 'result' => 35],
  ['input' => [[15, 20]], 'result' => 60]
];


$part2_tests = [
  ['input' =>[$input1], 'result' =>2772
   ],
   ['input' =>[$input2], 'result' =>4686774924
   ],


];
     
output_suite('Part 1 tests', $part1_tests, 'calculate_energy');
output_suite('Common multiple tests', $common_multiple_tests, 'lowest_common_multiple');
output_suite('Part 2 tests', $part2_tests, 'calculate_steps_until_repeat');



?>