
<?php
require_once '../util/util.php';



function count_subpaths(&$orbit_refs, &$orbit_counts, $key) {
    if (isset($orbit_refs[$key])) {
        $new_key = $orbit_refs[$key];
        $new_subpaths_count = 0;
        if (isset($orbit_counts[$new_key])) {
            $new_subpaths_count = $orbit_counts[$new_key];
        } else {
            $new_subpaths_count = count_subpaths($orbit_refs, $orbit_counts, $new_key);
            $orbit_counts[$new_key] = $new_subpaths_count;
        }

        return 1 + $new_subpaths_count;
    } else {
        return 0;
    }
    return isset($orbit_refs[$key]) ? 1 + count_subpaths($orbit_refs, $orbit_refs[$key]) : 0;
}

function get_orbits($orbits) {
    $orbit_refs = [];
    foreach ($orbits as $orbit) { 
        $exploded = explode(')', $orbit);
        $center = $exploded[0];
        $orbiting = $exploded[1];
        

        $orbit_refs[$orbiting] = $center;
    }

    return $orbit_refs;
}

function count_orbits($orbits) {
    $orbit_counts = ['COM' => 0];
    $orbit_refs = get_orbits($orbits);
    $num_connections = 0;
    foreach ($orbit_refs as $key =>  $value) {
        $num_connections += count_subpaths($orbit_refs, $orbit_counts, $key);
    }
    return $num_connections;
}

function get_distance($orbits, $from, $to) {
    $orbit_refs = get_orbits($orbits);

    $endpoints = [$orbit_refs[$from], $orbit_refs[$to]];
    $paths = [[$endpoints[0] => 0], [$endpoints[1] => 0]];

    $cur_index = 0;
    $other_index = 1;
    $distance = 0;

    while (isset($endpoints[0]) && isset($endpoints[1])) {

        $cur_center =  $endpoints[$cur_index];
        $next_center =  $orbit_refs[$cur_center];

        $cur_distance = $paths[$cur_index][$cur_center];

        if (isset($paths[$other_index][$cur_center])) {

            return $paths[$other_index][$cur_center] + $cur_distance;
        }

        $endpoints[$cur_index] = $next_center;

        if (isset($next_center)) {
            $paths[$cur_index][$next_center]  = $cur_distance + 1;
        }

        if (isset($endpoints[$other_index])) {
            $temp  = $cur_index;
            $cur_index = $other_index;
            $other_index = $temp;
        }
        
        $distance++;
    }


    return -1;
}

function count_orbits_from_file($path) {
    $str = file_get_contents($path);
    return count_orbits(explode(PHP_EOL, $str));
}

function get_distance_from_file($path, $from, $to) {
    $str = file_get_contents($path);
    return get_distance(explode(PHP_EOL, $str), $from, $to);
}

?>