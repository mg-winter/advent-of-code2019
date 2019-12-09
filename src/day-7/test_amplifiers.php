
<?php
require_once '../util/util.php';
require_once '../day-5/process_intcode-v2.php';

function get_sequences_rec($prev_arr, $arr) {
    if (count($arr) < 2) {
        return [array_merge($prev_arr, $arr)];
    } else {
        $recs = array_map(function($i) use ($arr) {
            return ['head' => $arr[$i], 
                    'tail' => array_merge(array_slice($arr, 0, $i), array_slice($arr, $i+1))];
        }, range(0, count($arr)-1));
        return call_user_func_array('array_merge', array_map(function($rec) use ($prev_arr) {
            $new_prev = array_merge($prev_arr, [$rec['head']]);
            return get_sequences_rec($new_prev, $rec['tail']);
        },$recs));
    }
}
function get_sequences($arr) {
    return get_sequences_rec([], $arr);
}

function test_phase_sequence($sequence, $arr, $is_debug) {

    $output = new Cached_Output();
    $output->write(0);

    for ($i = 0; $i < count($sequence); $i++) {
        process_intcode_arr($arr, new Cached_Input([$output->get_last(), $sequence[$i]]), $output, $is_debug);
    }
    echo PHP_EOL;
    return $output->get_last();
}
function test_amplifiers($arr, $is_debug) {

    $sequences = get_sequences(range(0,4));
    $results = array_map(function($seq) use ($arr, $is_debug) {
        return ['seq' => $seq, 'max' => test_phase_sequence($seq, $arr, $is_debug)];
    }, $sequences);

    return array_reduce($results, function($prev_max, $curr) {
        return $prev_max === null ||  $prev_max['max'] < $curr['max'] ? $curr : $prev_max;
    }, null);
   
   
}
 function test_amplifiers_from_file($path, $is_debug) {
    $str = file_get_contents($path);
    return test_amplifiers(explode(',',$str), $is_debug);
 }

?>