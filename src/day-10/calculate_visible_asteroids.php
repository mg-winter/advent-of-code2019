
<?php
require_once '../util/util.php';

class Vector {

    private $Horizontal;
    private $Vertical;

    public function __construct($hor, $vert) {
        $this->Horizontal= $hor;
        $this->Vertical = $vert;
    }

    public function reverse() {
        return new Vector($this->Horizontal * -1, $this->Vertical * -1);
    }

    public function right_angle() {
        return new Vector($this->Vertical, $this->Horizontal);
    }

    public function get_all_rotations() {
        $res = [$this, $this->reverse()];
       return $res;
    }

}

function array_subtract($arr1, $arr2) {

    $arr1_res = [];
 
    foreach($arr1 as $item) {
       
        $search_res = array_search($item, $arr2);

       
    
        if ($search_res !== false) {
            
            unset($arr2[$search_res]);
        } else {
            array_push($arr1_res, $item);
        }
    }

    return [$arr1_res, $arr2];
}


function get_divisors_up_to($max) {
    $res = [0 => [0], 1 =>[1]];
    $first_prime = 2;
    for ($i = $first_prime; $i <= $max; $i++) {
        if (!isset($res[$i])) {
            $res[$i] = [$i];
        }
        for ($j = $first_prime; $j <= $i; $j++) {
            $mult = $j * $i;
            if ($mult <= $max) {
                $res[$mult] = array_merge($res[$j], $res[$i]);
            } else {
                break;
            }
        }
    }

    return $res;
}

function multiply($arr) {
    return array_reduce($arr, function($a, $b){return $a * $b;}, 1);
}
function get_divisor_pairs($width, $height) {
    $max_num = max($width, $height);
    $divisors = get_divisors_up_to($max_num);

    $reduce_fraction = function($pair) use ($divisors) {
        $subtract_res = array_subtract($divisors[$pair[0]], $divisors[$pair[1]]);
       return [multiply($subtract_res[0]), multiply($subtract_res[1])];
    };

    $pairs = ['1,1' => [1,1], '0,1' => [0,1]];
    for ($i = 1; $i < $max_num; $i++) {
        for ($j = $i + 1; $j <= $max_num; $j++) {
            $fract = $reduce_fraction([$i, $j]);
            $key = implode(',', $fract);
            echo PHP_EOL . format_array([$i, $j]) . ' ==' . format_array($fract) . PHP_EOL;
            if (!isset($pairs[$key])) {
                $pairs[$key] = $fract;
            }
        }
    }

    return $pairs;
   
}

function get_vectors($width, $height) {
    return [];
}

function get_grid_distance($pointA, $pointB) {
    return abs($pointB[0] - $pointA[0]) + abs($pointB[1] - $pointA[1]);
}

function is_asteroid($item) {
    return $item == '#';
}

function reverse($direction_arr) {
    return [$direction_arr[0] * -1, $direction_arr[1] * -1];
}

function add_to_key(&$arr, $key, $value) {
    if (!isset($arr[$key])) {
        $arr[$key] = [];
    }

    array_push($arr[$key], $value);
}

function get_sign_num($num) {
    return $num == 0 ? $num : $num / abs($num);
}
function calculate_visible_asteroids($map_arr) {

    $height = count($map_arr);
    $width = count($map_arr[0]);

    $max_num = max($width, $height);
    $divisors = get_divisors_up_to($max_num);

    $reduce_fraction = function($pair) use ($divisors) {
        if ($pair[0] == 0) {
            return [0, get_sign_num($pair[1])];
        } else if ($pair[1] == 0) {
            return [get_sign_num($pair[0]), 0];
        } else {
          
            $abs_pair = [abs($pair[0]), abs($pair[1])];
            $sign_pair = [get_sign_num($pair[0]), get_sign_num($pair[1])];

         
          
            $subtract_res = array_subtract($divisors[$abs_pair[0]], $divisors[$abs_pair[1]] );
            
            return [multiply($subtract_res[0]) * $sign_pair[0], 
                    multiply($subtract_res[1]) * $sign_pair[1]];
        }
    };

    $asteroids = [];

    for ($i = 0; $i < $height; $i++) {
        for ($j = 0; $j < $width; $j++) {
            if (is_asteroid($map_arr[$i][$j])) {
                $coords = [$j, $i];
                $visible = [];
                foreach ($asteroids as &$asteroid) {
                    $distance = [$asteroid['coords'][0] - $coords[0], $asteroid['coords'][1] - $coords[1]];
                    
                    
                    $view_direction = $reduce_fraction($distance);

                   

                 

                    add_to_key($visible, implode(',', $view_direction), implode(',', $asteroid['coords']));

                    $reverse_direction = reverse($view_direction);
  
                    add_to_key($asteroid['visible'], implode(',', $reverse_direction), implode(',',$coords));



                }
                $asteroids[implode(',', $coords)] = ['coords' => $coords, 'visible' => $visible];
            }
        }
    }

  

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



?>