
<?php
require_once '../util/util.php';


function check_prev_orbits(&$orbit_counts, &$orbit_refs, $center) {
    if (isset($orbit_refs[$center])) {
        $orbit_counts[$center]++;
        foreach ($orbit_refs[$center] as $ref) {
            check_prev_orbits($orbit_counts, $orbit_refs, $ref);
        }
    }
}


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
function count_orbits($orbits) {
    $orbit_counts = ['COM' => 0];
    $orbit_refs = [];
    foreach ($orbits as $orbit) { 
        $exploded = explode(')', $orbit);
        $center = $exploded[0];
        $orbiting = $exploded[1];
        

        $orbit_refs[$orbiting] = $center;
    }
    $num_connections = 0;
    foreach ($orbit_refs as $key =>  $value) {
        $num_connections += count_subpaths($orbit_refs, $orbit_counts, $key);
    }
    return $num_connections;
}

function count_orbits_from_file($path) {
    $str = file_get_contents($path);
    return count_orbits(explode(PHP_EOL, $str));
}

?>