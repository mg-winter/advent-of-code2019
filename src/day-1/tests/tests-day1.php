<?php
require_once 'calculate_fuel.php';
require_once '../util/test-util.php';

    $simple_tests = [
        ['input' => [12], 'result' => 2],
        ['input' => [14], 'result' => 2],
        ['input' => [1969], 'result' => 654],
        ['input' => [100756], 'result' => 33583]
    ];

    $compounded_tests = [
        ['input' => [12], 'result' => 2],
        ['input' => [14], 'result' => 2],
        ['input' => [1969], 'result' => 966],
        ['input' => [100756], 'result' => 50346]
    ];


    output_suite('Simple calculation', $simple_tests, 'calculate_fuel');
    output_suite('Compounded calculation', $compounded_tests, 'calculate_fuel_compounded');


?>