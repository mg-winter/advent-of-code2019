
<?php
require_once '../util/util.php';

function manhattan_distance($point_arr) {
    return array_sum(array_map('abs', $point_arr));
}

class Grid_Area {
    
    public $Left = 0;
    public $Bottom = 0;
    public $Right = 0;
    public $Top = 0;
    
    public function __construct($left, $bottom, $right, $top) {
 
        $this->Bottom = $bottom;
        $this->Left = $left;
        $this->Right = $right;
        $this->Top = $top;
    }

    public function get_x_range() {
        return [$this->Left, $this->Right];
    }

    public function get_y_range() {
        return [$this->Bottom, $this->Top];
    }

    public function get_area() {
        return $this->get_width() * $this->get_height();
    }
    public function get_width() {
        return self::get_grid_distance($this->get_x_range());
    }

    public function get_height() {
        return self::get_grid_distance($this->get_y_range());
    }

    public function get_manhattan_distance() {
        return manhattan_distance([self::get_closer_value($this->get_x_range()),
                                    self::get_closer_value($this->get_y_range())]);
    }

    public function contains_coords($x, $y) {
        $keys = ['min_range', 'max_range'];
        $x_range = ['options' => array_combine($keys, $this->get_x_range())];
        $y_range = ['options' => array_combine($keys, $this->get_y_range())];
        return filter_var($x, FILTER_VALIDATE_INT, $x_range) 
                        && filter_var($y, FILTER_VALIDATE_INT, $y_range);
    }
    
    public function split() {
        $results = [];
        $X_ranges = self::split_range($this->get_x_range());
        $Y_ranges = self::split_range($this->get_y_range());
        foreach ($X_ranges as $X_range) {
            foreach ($Y_ranges as $Y_range) {
                array_push($results, new Grid_Area($X_range[0], $Y_range[0], 
                                                    $X_range[1], $Y_range[1]));
            }
        }
        return $results;
    }

    private static function split_range($range) {
        $range_size = self::get_grid_distance($range);
        if ($range_size > 1) {
            $midpoint = $range[0] + intdiv($range_size, 2);
            $half1 = [$range[0], $midpoint];
            $half2 =  [$midpoint+1, $range[1]];
            return [$half1, $half2];
        } else {
            return [$range];
        }
    }

    public static function intersect($area1, $area2) {

        $overlap_x = self::get_overlap($area1->get_x_range(), $area2->get_x_range());

        if ($overlap_x === null) {
            return null;
        }

        $overlap_y = self::get_overlap($area1->get_y_range(), $area2->get_y_range());

        if ($overlap_y === null) {
            return null;
        }

        return new Grid_Area(min($overlap_x), min($overlap_y), 
                            max($overlap_x), max($overlap_y));
    }

    public static function union($area1, $area2) {
        return new Grid_Area(
            min($area1->Left, $area2->Left),
            min($area1->Bottom, $area2->Bottom),
            max($area1->Right, $area2->Right),
            max($area1->Top, $area2->Top)
        );
    }

    private static function get_overlap($range1, $range2) {
        sort($range1);
        sort($range2);
        $res = [max($range1[0], $range2[0]), min($range1[1], $range2[1])];
        return $res[0] > $res[1] ? null : $res;
    }

    private static function get_closer_value($range) {
        return min(array_map('abs', $range));
    }

    private static function get_grid_distance($range) {
        return abs($range[1] - $range[0]) + 1;
    }

    public function __toString() {
        return 'Grid area ' . implode(',', [$this->Left, $this->Bottom, $this->Right, $this->Top]);
    }
}
class Delta {
    private static $VECTORS = [
        'U' => [0,1], 
        'D' => [0,-1], 
        'R' => [1,0], 
        'L' => [-1,0]
    ];

    public $X;
    public $Y;
    public $Vector;
 
    function __construct($x, $y, $vector) {
        $this->X = $x;
        $this->Y = $y;
        $this->Vector = $vector;
    }

    public function __toString() {

        return '+' . '[' . $this->X . ',' . $this->Y . ']';
    }
    public static function parse($str) {
        
        $vector_str = substr($str,0,1); 
        $vector = self::$VECTORS[$vector_str];
        $length = intval(substr($str,1));
        
        return new Delta($vector[0] * $length, $vector[1] * $length, $vector);
    }

    public function shave_start() {
        $this->X -= $this->Vector[0];
        $this->Y -= $this->Vector[1];
    }
    
    public static function parse_deltas($arr) {
        return array_map('self::parse', $arr);
    }
}

abstract class Shape {
    abstract public function get_grid_area();
    abstract public function crop($grid_area);

    protected static function get_intersection($shape1, $shape2) {
        if ($shape1 === null || $shape2 === null) {
            return null;
        }

        $intersect_area = Grid_Area::intersect($shape1->get_grid_area(), 
                                            $shape2->get_grid_area());

        return $intersect_area;
    }

    public static function find_intersections($shape1, $shape2) {
        $intersection = self::get_intersection($shape1, $shape2);
        if ($intersection === null) {
            return [];
        } else if ($intersection->get_area() === 1) {
            $distance = $intersection->get_manhattan_distance();
            
            return $distance === 0 ? [] : [$intersection];
        } else {
            $areas = $intersection->split();
            $results = array_map(function($area) use ($shape1, $shape2) {
                return self::find_intersections
                    ($shape1->crop($area), $shape2->crop($area));
            }, $areas);

            return  call_user_func_array('array_merge', $results);         
        }
    }

    public static function find_nearest_intersection_distance($shape1, $shape2) {
        $intersections = self::find_intersections($shape1, $shape2);
        return min(array_map(function($area) {
                return $area->get_manhattan_distance();
            }, $intersections));
                                                   

    }
}

class Vertex extends Shape {
    public $X;
    public $Y;

    public function __construct($x, $y) {
        $this->X = $x;
        $this->Y = $y;
    }

    public function get_grid_area() {
        return new Grid_Area($this->X, $this->Y, $this->X, $this->Y);
    }

    public function crop($grid_area) {
        $intersect = Grid_Area::intersect($this->get_grid_area(), $grid_area);
        return $intersect === null ? null : new Vertex($intersect->Left, $intersect->Bottom);
    }

    public function __toString() {
        return '{' . $this->X  . ',' . $this->Y . '}';
    }

    public static function create_vertex_from_delta($original_vertex, $delta) {
        return new Vertex($original_vertex->X + $delta->X, 
                            $original_vertex->Y + $delta->Y);
    }
}
class Line extends Shape {
    public $From_Vertex = null;
    public $To_Vertex = null;

    public function __construct($from_vertex, $to_vertex) {
        $this->From_Vertex = $from_vertex;
        $this->To_Vertex = $to_vertex;
    }

    public function get_grid_area() {
        return Grid_Area::union($this->From_Vertex->get_grid_area(), 
                                    $this->To_Vertex->get_grid_area());
    }

    public function crop($grid_area) {
        $intersect = Grid_Area::intersect($this->get_grid_area(), $grid_area);
        return $intersect === null ? null : 
                            new Line(new Vertex($intersect->Left, $intersect->Bottom),
                                    new Vertex($intersect->Right, $intersect->Top));
    }

    public function __toString() {
        return $this->From_Vertex . '<->' . $this->To_Vertex;
    }

    public static function create_line_from_delta($vertex, $delta) {
        return new Line($vertex, Vertex::create_vertex_from_delta($vertex,  $delta));
    }

}

class Path extends Shape {
    public $Lines;

    public function __construct($lines) {
        $this->Lines = $lines;
    }

    public function get_grid_area() {
        $union_all = function($area, $shape) {
            if ($area === null) {
                return $shape === null ? null : $shape->get_grid_area();
            } else if ($shape === null) {
                return $area;
            } else {
                return Grid_Area::union($area, $shape->get_grid_area());
            }
        };

        return array_reduce($this->Lines, $union_all, null);
    }

    public function crop($grid_area) {
        $cropped_lines = array_map(function($line) use ($grid_area) {
            return $line->crop($grid_area);
         }, $this->Lines);

        $valid_lines = array_filter($cropped_lines, function($line) {
                                    return $line !== null;
                                });

        return count($valid_lines) > 0 ? new Path($valid_lines) : null;
    }

    /** This could be optimized by caching the up-to-here distance calculation 
     * for full lines, as well as checking just the intersected lines,
     * but then we'd have to deal with read-only properties, which is a pain in PHP */
    public function calculate_distance($intersect_area) {
       
        $distance = 0;
        foreach ($this->Lines as $line) {
            $intersect = Grid_Area::intersect($line->get_grid_area(), $intersect_area);
            if ($intersect === null) {
                $distance += $line->get_grid_area()->get_area() - 1;
                
            } else {
                $segment_area = Grid_Area::union($line->From_Vertex->get_grid_area(), $intersect);
               
                return $distance + $segment_area->get_area();
            }
        }
        return -1;
    }

    public static function create_path_from_deltas($origin, $deltas) {
        $last_origin = $origin;
        $lines = [];
        foreach ($deltas as $delta) {
            $line = Line::create_line_from_delta($last_origin, $delta);
            array_push($lines, $line);
            $last_origin = $line->To_Vertex;
        }

        return new Path($lines);
    }

    public static function find_nearest_intersection_steps($path1, $path2) {
        $intersections = self::find_intersections($path1, $path2);
       
        $min_by_distance = function($cur_min, $item) use ($path1, $path2) {
            $new_min1 = $path1->calculate_distance($item);
            $new_min2 = $path2->calculate_distance($item);
            $new_min = $new_min1 + $new_min2;
            return $cur_min > 0 ? min($cur_min, $new_min) : $new_min;
        };
        $min_distance = array_reduce($intersections, $min_by_distance, -1);
        return $min_distance;
    }

    public function __toString() {
        return format_array($this->Lines);
    }
}


function convert_to_shapes($arrs) {
    return  array_map(function($arr) {
        $delta_arr = Delta::parse_deltas($arr);
        $delta_arr[0]->shave_start();
        $vector = $delta_arr[0]->Vector;
        $origin = new Vertex($vector[0], $vector[1]);
        return Path::create_path_from_deltas($origin, $delta_arr);
    }, $arrs);
}


function get_arrs($path) {
    $str = file_get_contents($path);
    $wires = explode(PHP_EOL, $str);
    return array_map(function($str_arr){return explode(',',$str_arr);}, $wires);
}
function find_intersections_arr($arrs, $is_debug) {
    $shapes = convert_to_shapes($arrs);
    return Shape::find_nearest_intersection_distance($shapes[0], $shapes[1]);
}

function find_intersections_from_file($path, $is_debug) {
    return find_intersections_arr(get_arrs($path), $is_debug);
}

function find_intersections_by_steps_arr($arrs, $is_debug) {
    $shapes = convert_to_shapes($arrs);
    return Path::find_nearest_intersection_steps($shapes[0], $shapes[1]);
}

function find_intersections_by_steps_from_file($path, $is_debug) {
    return find_intersections_by_steps_arr(get_arrs($path), $is_debug);
}


?>