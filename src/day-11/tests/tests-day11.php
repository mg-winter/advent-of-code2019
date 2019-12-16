<?php
require_once 'paint_hull.php';
require_once '../util/test-util.php';


function get_panels_count_mock($program)  {
    return get_panels_count($program, true);
}

    $tests_part1 = [
      ['input' => [[3,0,4,7,4,8,99,0,1]], 'result' => 1],
        
    ];


   

    output_suite('Part 1 Tests', $tests_part1, 'get_panels_count_mock');



?>