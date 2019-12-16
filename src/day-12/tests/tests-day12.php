<?php
require_once 'calculate_energy.php';
require_once '../util/test-util.php';

$input1 = [
  ['x'=>-1, 'y'=>0, 'z'=>2],
  ['x'=>2, 'y'=>-10, 'z'=>-7],
  ['x'=>4, 'y'=>-8, 'z'=>8],
  ['x'=>3, 'y'=>5, 'z'=>-1]
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
     
output_suite('Part 1 tests', $part1_tests, 'calculate_energy');




?>