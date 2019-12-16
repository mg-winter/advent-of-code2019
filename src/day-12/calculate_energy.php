
<?php
require_once '../util/util.php';

function calculate_energy($initial_coords, $num_steps) {
    $moons = array_map(function($arr) {
        return ['position' => [$arr['x'], $arr['y'], $arr['z']], 'velocity' => [0,0,0]];
    },$initial_coords);
    
    $steps = [$moons];
    for ($i = 0; $i < $num_steps; $i++) {
        $moons = simulate_time_step($moons);
        array_push($steps, $moons);
    }

    return ['moons' => $moons, 'energy' => array_sum(array_map(function($moon) {
        return sum_abs($moon['position']) * sum_abs($moon['velocity']);
    },$moons))];
}

function simulate_time_step($moons) {
    for ($i = 0; $i < count($moons); $i++) {
        $next_comparison = $i+1;
        for ($j = $next_comparison; $j < count($moons); $j++) {
            apply_gravity($moons[$i], $moons[$j]);
        }
    }

    for ($i = 0; $i < count($moons); $i++) {
        apply_velocity($moons[$i]);
    }

    return $moons;
}

function apply_gravity(&$moon1, &$moon2) {
    for ($i = 0; $i < 3; $i++) {
        $pos1 = $moon1['position'][$i];
        $pos2 = $moon2['position'][$i];
        if ($pos1 > $pos2) {
            $moon1['velocity'][$i]--;
            $moon2['velocity'][$i]++;
        } else if ($pos1 < $pos2) {
            $moon1['velocity'][$i]++;
            $moon2['velocity'][$i]--;
        }
    }
}

function apply_velocity(&$moon) {
    for ($i = 0; $i < 3; $i++) {
        $moon['position'][$i] += $moon['velocity'][$i];
    }
}

function sum_abs($arr) {
    return array_reduce($arr, function($res, $val) {
        return $res + abs($val);
    }, 0);
}

?>