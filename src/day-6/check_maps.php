
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


function count_subpaths(&$orbit_refs, $key) {
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
        $num_connections += count_subpaths($orbit_refs, $key);
    }
    return $num_connections;
}

function count_orbits_from_file($path) {
    $str = file_get_contents($path);
    return count_orbits(explode(PHP_EOL, $str));
}

?>