
<?php
require_once '../util/util.php';

function lowest_common_multiple($numbers) {

    $cur_numbers = array_map(function($n){return $n;}, $numbers);
    $lowest_prime = 2;
    $max_divisor = intdiv(max($cur_numbers), $lowest_prime);
    $cur_divisor = $lowest_prime;
    $cur_res = 1;
    $known_multiples = [];

    while($cur_divisor <= $max_divisor && count(array_filter($cur_numbers, function($n){return $n > 1;})) > 0) {
     
        $max_log = 0;

        for ($i = 0; $i < count($cur_numbers); $i++) {
            
            $cur_log = 0;
            while($cur_numbers[$i] % $cur_divisor == 0) {
                $cur_numbers[$i] /= $cur_divisor;
                $cur_log++;
            } 
            $max_log = max($max_log, $cur_log);
        }

        $cur_res *= pow($cur_divisor, $max_log);
    
        
        $cur_divisor++;
        $max_divisor = intdiv(max($cur_numbers), $cur_divisor) + 1;
    }

    //Whatever is left at the end in the array will be primes, so multiply them in
    return $cur_res * array_reduce(array_unique($cur_numbers), function($res, $n) {return $res * $n;}, 1);
}

function get_moons($initial_coords) {
    return  array_map(function($arr) {
        return ['position' => [$arr['x'], $arr['y'], $arr['z']], 'velocity' => [0,0,0]];
    },$initial_coords);
}

/** There is probably a faster calculus-based  solution to this, but I don't know what it is.
 * Optimizations applied here:
 * - The axes are independent, so instead of repeating until all axes match, we can do each axis
 * individually and then return the lowest common multiple of the 3 results
 * - Total velocity (sum of absolute values) goes to 0 exactly halfway between initial state and repeat state,
 * so we can return when total velocity is 0 and then multiply the result by 2.
 * With these optimizations, the program returns in ~10 seconds. Trying to run the step simulator for <answer> 
 * steps results in memory blowing up, so optimizations are probably an improvement.
 */

function calculate_energy($initial_coords, $num_steps) {
    $moons = get_moons($initial_coords);
    
    $steps = [$moons];
    for ($i = 0; $i < $num_steps; $i++) {
        $moons = simulate_time_step($moons);
        array_push($steps, $moons);
    }

    return ['moons' => $moons, 'energy' => array_sum(array_map(function($moon) {
        return sum_abs($moon['position']) * sum_abs($moon['velocity']);
    },$moons))];
}


function calculate_steps_until_repeat($initial_coords) {
    $moons = get_moons($initial_coords);

    $counts = [];
    for ($i = 0; $i < 3; $i++) {
        $counts[$i] = calculate_steps_to_0_along_axis($moons, $i);
    }

    return lowest_common_multiple($counts) * 2;
}

function calculate_steps_to_0_along_axis($moons, $axis) {
    $states = [];
    $i = 0;
    $prev_distance = -1;

    $num_0s = 0;
    $num_repeats  = 0;
    while (true) {
  
        $total_velocity = array_sum(array_map(function($moon) use ($axis){
            return abs($moon['velocity'][$axis]);
        }, $moons));

        if ($i > 0 && $total_velocity == 0) {
            return $i;
        }

        $moons = simulate_time_step($moons, [$axis]);
        $i++;
    }

}

function simulate_time_step($moons, $axes=[0,1,2]) {
    for ($i = 0; $i < count($moons); $i++) {
        $next_comparison = $i+1;
        for ($j = $next_comparison; $j < count($moons); $j++) {
            apply_gravity($moons[$i], $moons[$j], $axes);
        }
    }

    for ($i = 0; $i < count($moons); $i++) {
        apply_velocity($moons[$i], $axes);
    }

    return $moons;
}

function apply_gravity(&$moon1, &$moon2, $axes=[0,1,2]) {
    foreach ($axes as $i) {
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

function apply_velocity(&$moon, $axes=[0,1,2]) {
    foreach ($axes as $i) {
        $moon['position'][$i] += $moon['velocity'][$i];
    }
}

function sum_abs($arr) {
    return array_reduce($arr, function($res, $val) {
        return $res + abs($val);
    }, 0);
}

?>