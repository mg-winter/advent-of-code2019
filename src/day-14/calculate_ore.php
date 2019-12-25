
<?php
require_once '../util/util.php';

function update_component_counts($key, $num_required, $component_map, &$cur_counts) {


    $queue = [['key' => $key, 'amount' => $num_required]];
    $leftovers = [];
    
    while(count($queue) > 0) {
        $cur_item = array_shift($queue);
        $cur_key = $cur_item['key'];
       
        $cur_details = $component_map[$cur_key];
       
        $increment = isset($cur_details) && isset($cur_details['increment'])  ? $cur_details['increment'] : 1;
        $cur_count = isset($cur_counts[$cur_key]) ? $cur_counts[$cur_key] : 0;
        
        $to_add = $cur_item['amount'];

        $available = isset($leftovers[$cur_key]) ? $leftovers[$cur_key] : 0;
        if ($available >= $to_add) {
            $leftovers[$cur_key]-=$to_add;
        } else {
            $to_get = $to_add - $available;
            $mult = intdiv($to_get, $increment) + ( $to_get % $increment == 0 ? 0 : 1);
            $incr_amount = $mult * $increment;
            $leftovers[$cur_key] = $incr_amount + $available - $to_add;
            if (isset($cur_details)) {
              $composition = (array) $cur_details['components'];

              foreach($composition as $chemical => $amount) {
                $new_amount_required = $mult * $amount;
                array_push($queue, ['key' => $chemical, 'amount' => $new_amount_required]);
              }
            }
        }
        $cur_counts[$cur_key]+=$to_add;
    }

    $keys = array_keys($leftovers);
    foreach ($keys as $key) {
        $cur_counts[$key] += $leftovers[$key];
    }
}


function calculate_ore($initial_dict, $base, $target) {
    $direct_mappings = [];
    $direct_mappings[$base] = ['increment' => 1, 'base_value' => 1];
    $leftovers = [];
    $required_counts = [];
    update_component_counts($target, 1, $initial_dict, $required_counts);
    return $required_counts[$base];
}


function process_chem_quantity($str) {
    return array_map('trim', explode(' ', trim($str)));
}

function calculate_ore_from_file($path, $base, $target) {
    $str = file_get_contents($path);
    $lines = explode(PHP_EOL, $str);
    $res_dict = [];

    foreach($lines as $line) {
        $split = explode(' => ', $line);
        $split_target = process_chem_quantity($split[1]);
        $components = array_map(function($compound_line){
            return process_chem_quantity($compound_line);
        },explode(',', trim($split[0])));
        $component_map = [];
        foreach ($components as $component) {
            $component_map[$component[1]] = $component[0];
        }
        $res_dict[$split_target[1]] = ['increment' => $split_target[0],  'components' => $component_map];
    }
  
    return calculate_ore($res_dict, $base, $target);
}

?>