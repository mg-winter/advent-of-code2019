<?php
require_once 'calculate_fuel.php';


function run_test($test) {
    $res = calculate_fuel($test['input']);
    return ['result' => $res, 
            'pass' => $res === $test['result'],
            'test' => $test];
}

function run_test_compounded($test) {
    $res = calculate_fuel_compounded($test['input']);
    return ['result' => $res, 
            'pass' => $res === $test['result'],
            'test' => $test];
}

function run_tests() {

    $tests = [
        ['input' => 12, 'result' => 2],
        ['input' => 14, 'result' => 2],
        ['input' => 1969, 'result' => 654],
        ['input' => 100756, 'result' => 33583]
    ];

    return array_map('run_test', $tests);

}

function run_tests_compounded() {

    $tests = [
        ['input' => 12, 'result' => 2],
        ['input' => 14, 'result' => 2],
        ['input' => 1969, 'result' => 966],
        ['input' => 100756, 'result' => 50346]
    ];

    return array_map('run_test_compounded', $tests);

}


function format_result($result) {
    $pass_str = $result['pass'] ? 'pass' : 'fail';
    return $result['test']['input'] . ': ' . $pass_str  
                        . ' (expected ' . $result['test']['result'] . ', got '
                        . $result['result'] . ')';
}


echo 'Simple  calculation';
echo PHP_EOL;
echo implode(PHP_EOL, array_map('format_result', run_tests()));

echo PHP_EOL;
echo 'Compounded calculation';
echo PHP_EOL;
echo implode(PHP_EOL, array_map('format_result', run_tests_compounded()));
echo PHP_EOL;


?>