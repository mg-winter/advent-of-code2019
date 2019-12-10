
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

function decode_image($arr, $height, $width) {
    $digits_per_layer = $height * $width;
    $chunks = array_chunk($arr, $digits_per_layer);
    $final_layer = [];
    for ($i = 0; $i < $digits_per_layer; $i++) {
        $final_layer[$i] = 0;
        foreach($chunks as $chunk) {
            if ($chunk[$i] < 2) {
                $final_layer[$i] = $chunk[$i];
                break;
            }
        }
    }

    $hor_line = str_repeat('-', $width + 2);

    /**Picking which characters represent which colours was the hardest part! */
    return  $hor_line . PHP_EOL .  implode(PHP_EOL, array_map(function($row) {
        $string_rep = array_map(function($item) {
            return [" ", "\u{1F431}","\u{1F431}"][$item];
        }, $row);
        return '|' . implode('', $string_rep) . '|';
    }, array_chunk($final_layer, $width))) . PHP_EOL . $hor_line;
}

function verify_image_from_file($path, $height, $width) {
    $str = file_get_contents($path);
    return verify_image(str_split($str), $height, $width);
}

function decode_image_from_file($path, $height, $width) {
    $str = file_get_contents($path);
    return decode_image(str_split($str), $height, $width);
}

?>