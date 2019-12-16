
<?php
require_once '../util/util.php';

function is_asteroid($item) {
    return $item != '.';
}

function add_to_key(&$arr, $key, $value) {
    if (!isset($arr[$key])) {
        $arr[$key] = [];
    }

    array_push($arr[$key], $value);
}
function normalize_angle($angle_deg) {
    
    $angle_single_rot = fmod($angle_deg, 360);
    return $angle_single_rot >= 0 ? $angle_single_rot : 360 + $angle_single_rot;
}

function get_asteroid_list($map_arr) {
    $height = count($map_arr);
    $width = count($map_arr[0]);

    $asteroids = [];

    for ($i = 0; $i < $height; $i++) {
        for ($j = 0; $j < $width; $j++) {
            if (is_asteroid($map_arr[$i][$j])) {
                $coords = [$j, $i];
                $coords_key = implode(',', $coords);
                $visible = [];
                foreach ($asteroids as &$asteroid) {

                    /**Having the distances in different directions is weird
                     * but makes atan2 return the correct angle for later
                     * sorting.
                     */
                    $x_distance = $asteroid['coords'][0] - $coords[0];
                    $y_distance = $coords[1] - $asteroid['coords'][1];

                    $angle = normalize_angle(rad2deg(atan2($x_distance, $y_distance)));
                   
                    $distance = sqrt(($x_distance ** 2) + ($y_distance ** 2));
                    
                    add_to_key($visible, $angle . '', ['coords' => implode(',', $asteroid['coords']), 'distance' => $distance]);
                    $reverse_angle = normalize_angle(180 + $angle);
                     
                    add_to_key($asteroid['visible'], $reverse_angle . '', ['coords' => $coords_key, 'distance' => $distance]);

                }
                $asteroids[$coords_key] = ['coords' => $coords, 'visible' => $visible];
            }
        }
    }

    foreach ($asteroids as &$asteroid) {
        foreach ($asteroid['visible'] as &$visible_arr) {
            usort($visible_arr, function($a, $b) {
                return $a['distance'] < $b['distance'] ? -1 : ($a['distance'] > $b['distance'] ? 1 : 0);
            });
        }
    }

    return $asteroids;
}

function get_nth_to_vaporise($map_arr, $base_coords_str, $n) {
    $asteroids = get_asteroid_list($map_arr);
    $base = $asteroids[$base_coords_str];
    
    $visible = $base['visible'];
    $remaining = $n;
    $num_visible = count($visible);

    while ($num_visible < $remaining) {
        $remaining -= $num_visible;

        $new_visible = [];
        $keys = array_keys($visible);
        foreach($keys as $key) {
            $along_line = $visible[$key];
            if (count($along_line) > 1) {
                $new_visible[$key] = array_slice($along_line, 1);
            }
        }
        
        $num_visible = count($new_visible);
        $visible = $new_visible;
    }
 
    $angle_keys = array_keys($visible);
    usort($angle_keys, function($a, $b) {
        $float_a = floatval($a);
        $float_b = floatval($b);
        return $float_a < $float_b ? - 1 : ($float_a > $float_b ? 1 : 0);
    });

    return $visible[$angle_keys[$remaining-1]][0]['coords'];
}

function calculate_visible_asteroids($map_arr) {
    $asteroids = get_asteroid_list($map_arr);
    return array_reduce($asteroids, function($prev_max, $curr) {
        $num_visible = count(array_keys($curr['visible']));
        return $prev_max == null || $prev_max['num_visible'] < $num_visible ? 
                                    ['coords' => $curr['coords'], 'num_visible' => $num_visible] : $prev_max;
    }, null);

}

function calculate_visible_asteroids_from_file($path) {
    $str = file_get_contents($path);
    return calculate_visible_asteroids(array_map(function($line) {
        return str_split($line);
    }, explode(PHP_EOL, $str)));
}

function get_nth_to_vaporise_from_file($path, $base_coords_str, $n) {
    $str = file_get_contents($path);
    return get_nth_to_vaporise(array_map(function($line) {
        return str_split($line);
    }, explode(PHP_EOL, $str)), $base_coords_str, $n);
}

?>