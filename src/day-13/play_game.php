
<?php
require_once '../day-9/process_intcode-v3.php';

class Array_Output extends Cached_Output {
    function get_lines() {
        return array_map(function($line){return $line;}, $this->Storage);
    }
}



function get_tile_count_from_file($path, $is_debug) {
    $input = new Cached_Input([]);
    $output = new Array_Output();
    
    $result = process_intcode_from_file($path, $input, $output, $is_debug);

    $instructions =   $output->get_lines();
    $num_blocks = 0;
    for ($i = 2; $i < count($instructions); $i += 3) {
        if ($instructions[$i] == 2) {
            $num_blocks++;
        }
    }

    return $num_blocks;
}


?>