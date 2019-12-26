<?php
require_once 'apply_fft.php';
require_once '../util/test-util.php';

$Base_Pattern = [0, 1, 0, -1];

$apply_fft_tests = [
    ['input' => ['./tests/test-1.txt', 1, $Base_Pattern], 'result' => '48226158'],
      ['input' => ['./tests/test-1.txt', 2, $Base_Pattern], 'result' => '34040438'],
      ['input' => ['./tests/test-1.txt', 3, $Base_Pattern], 'result' => '03415518'],
      ['input' => ['./tests/test-1.txt', 4, $Base_Pattern], 'result' => '01029498'],
      ['input' => ['./tests/test-2.txt', 100, $Base_Pattern], 'result' => '24176176'],
       ['input' => ['./tests/test-3.txt', 100, $Base_Pattern], 'result' => '73745418'],
        ['input' => ['./tests/test-4.txt', 100, $Base_Pattern], 'result' => '52432133'],
   
];

output_suite('Part 1 tests', $apply_fft_tests, 'apply_phases_from_file');
?>