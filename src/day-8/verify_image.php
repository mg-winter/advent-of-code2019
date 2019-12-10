
<?php
require_once '../util/util.php';

function verify_image($arr, $height, $width) {
    $digits_per_layer = $height * $width;
    $chunks = array_chunk($arr, $digits_per_layer);

    $digits_to_count = [0, 1, 2];
   
    $chunks_by_count = array_map(function($chunk) use ($digits_to_count) {
        $all_counts = array_count_values($chunk);
        $counts = [];
       
        foreach($digits_to_count as $digit) {
            $key = 'num_' . $digit . 's';
            $counts[$key] = isset($all_counts[$digit]) ? $all_counts[$digit] : 0;
         
        }

        return $counts;

    }, $chunks);

    $with_least_0s = array_reduce($chunks_by_count, function($cur_min, $item) {
        if ($cur_min === null || $cur_min['num_0s'] > $item['num_0s']) {
            return $item;
        } else {
            return $cur_min;
        }
    }, null);

    $with_least_0s['hash'] = $with_least_0s['num_1s'] * $with_least_0s['num_2s'];
  
    return $with_least_0s;
}

function verify_image_from_file($path, $height, $width) {
    $str = file_get_contents($path);
    return verify_image(str_split($str), $height, $width);
}

?>