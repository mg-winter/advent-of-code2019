<?php 
require_once 'util.php';

    function run_simple_test($test, $func_to_test) {
        $res = call_user_func_array($func_to_test, $test['input']);

        return ['result' => $res, 
                'pass' => $res == $test['result'],
                'test' => $test];
    }

    function run_suite($tests, $func_to_test) {
        $runner = function($test) use ($func_to_test) {
            return run_simple_test($test, $func_to_test);
        };

        return array_map($runner, $tests);
    }


    function format_result($result) {
        $pass_str = $result['pass'] ? 'pass' : 'fail';
        return format_array($result['test']['input']) . ': ' . $pass_str  
                            . ' (expected ' . format_array($result['test']['result']) 
                            . ', got '
                            . format_array($result['result']) . ')';
    }

 
    function output_suite($name, $tests, $func_to_test) {
        echo $name;
        echo PHP_EOL;
        $results = run_suite($tests, $func_to_test);
        echo implode(PHP_EOL, array_map('format_result', $results));
        echo PHP_EOL;
    }



?>