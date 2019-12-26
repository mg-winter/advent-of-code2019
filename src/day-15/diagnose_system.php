
<?php
require_once '../day-9/process_intcode-v3.php';

class Array_Output extends Cached_Output {
    function get_lines() {
        return array_map(function($line){return $line;}, $this->Storage);
    }
}


class Diagnostic_Robot extends Array_Output implements Input {

    protected static $Items_Per_Set = 3;
    protected static $Tile_Key = [
        0 => "##",
        1 => "..",
        2 => "\u{1F438}",
        3 => "()",
        4 => "\u{1F431}",
        5 => "  ",
        6 => '**'
    ];

    protected static $Directions = [[0,-1], [0,1],[-1, 0], [1,0]];
    protected $Last_Direction_Index = 0;
    protected $Prev_Direction_Index = 0;

    /**0 for seeking system, 1 for finding path back */
    protected $Mode = 0;

    protected $Boundaries = [[0,0],[0,0]];
    
 
    protected $Known_Coords = [];
    protected $Coord_Directions = [];
  
    protected $Robot_Coords = [0,0];
    protected $System_Location = null;


    public function read() {
      return $this->Last_Direction_Index + 1;
    }

    public function write($value) {

        //for some reason 1/0 get converted to true/false
        if (!$value) {
            $value = 0;
        } else if ($value === true) {
            $value = 1;
        }
        parent::write($value);
        $updated_coords =  $this->preview_move($this->Last_Direction_Index);
        $key = implode(',', $updated_coords);

        $this->Known_Coords[$key] = $value;
    
        $this->update_boundaries($updated_coords);
        
        if ($value != 0) {
            $this->Robot_Coords = $updated_coords;
            if (!isset($this->Coord_Directions[$key])) {
                $this->Coord_Directions[$key] = self::reverse($this->Last_Direction_Index);
            }
        }

       
        
        if ($this->System_Location != null && $updated_coords == [0,0]) {
            $this->output_display([]);
            echo PHP_EOL . ' returned to base. System is at ' . format_array($this->System_Location) . PHP_EOL; 
            $points = [];
            echo 'Shortest path to system is ' . $this->get_shortest_route_length($this->System_Location, [0,0], -1, $points) . PHP_EOL;
            exit();
        } else {
            if ($value === 2) {
                $this->System_Location = $updated_coords;
            } 
            $this->update_direction();
        }


    }

    protected function get_shortest_route_length($coords, $target, $prev_dir, $path) {

        /**Path-finding:
         * 
         * 1. Take the current end points
         * 2. Check if any of them is the target.
         * 3. For each end point that is not a wall and not looping back, try every possible way to make 1 step and make that 
         * the new set of endpoints.
         * 4. Repeat.
         * 
         */

        
        $key = implode(',', $coords);
        $init_path = [];
        $init_path[$key] = true;
        $partial_paths = [['coords' => $coords, 'path' => []]];

        $directions = array_keys(self::$Directions);
        for ($i = 0; $i <= PHP_INT_MAX; $i++) {
           
            $new_paths = [];
            foreach ($partial_paths as $partial_path) {
                if ($partial_path['coords'] == $target) {
                    return $i;
                } else {
                    $coords =  $partial_path['coords'];
                    $subpath_key = implode(',', $coords);
                    if ($this->Known_Coords[$subpath_key] != 0 && !isset($partial_path['path'][$subpath_key])) {
                        $path_keys = array_keys($partial_path['path']);
                        $new_path = [];
                        foreach ($path_keys as $path_key) {
                            $new_path[$path_key] = true;
                        }
                        $new_path[$subpath_key] = $i;
                        foreach ($directions as $dir) {
                            $new_coords = self::apply_direction($coords, $dir);
                            array_push($new_paths, ['coords' => $new_coords, 'path' => $new_path]);
                        }
                    }
                }
            }
            $partial_paths = $new_paths;
        }

        return -1;       
    }

    protected function update_direction() {
       
        $dirs = [self::clockwise($this->Last_Direction_Index), $this->Last_Direction_Index, self::counter_clockwise($this->Last_Direction_Index), self::reverse($this->Last_Direction_Index)];
     
        foreach ($dirs as $cur_dir) {
            
            $key = implode(',', $this->preview_move($cur_dir));
             
            if (!isset($this->Known_Coords[$key])) {
               
                $this->set_direction($cur_dir);
                return;
            }
        }

        $key = implode(',', $this->Robot_Coords);
        $dir = $this->Coord_Directions[$key];
       
        $this->set_direction($dir);
       
    }

    protected function set_direction($dir) {
       
        $this->Last_Direction_Index = $dir;
    }

    protected static function reverse($dir) {
         $to_add = $dir % 2 == 0 ? 1 : -1;
         return $dir + $to_add;
    }

     protected static function clockwise($dir) {
         return wrap_to_length($dir + 2, self::$Directions);
    }

     protected static function counter_clockwise($dir) {
         return self::reverse(self::clockwise($dir));
    }

    protected function preview_move($index) {
        return self::apply_direction($this->Robot_Coords, $index);
    }

     protected static function apply_direction($coords, $index) {
        return [$coords[0] + self::$Directions[$index][0],
                            $coords[1] + self::$Directions[$index][1]];
    }

    function update_boundaries($coords) {
  
        // echo PHP_EOL . "Before "  . format_array($this->Boundaries) . ', ';
        // echo "Change "  . format_array($coords) . ', ';
     
        $this->Boundaries[0][0] = min($this->Boundaries[0][0], $coords[0]);
        $this->Boundaries[0][1] = max($this->Boundaries[0][1], $coords[0]);
        $this->Boundaries[1][0] = min($this->Boundaries[1][0], $coords[1]);
        $this->Boundaries[1][1] = max($this->Boundaries[1][1], $coords[1]);
        //echo "Result " . format_array($this->Boundaries) . PHP_EOL;
    
    }

    public function output_display($path) {
        echo PHP_EOL;
        echo format_array($this->Robot_Coords);
        echo PHP_EOL;

        
        for ($j = $this->Boundaries[0][0]; $j <= $this->Boundaries[0][1]+1; $j++) {
            echo '--';
        }

        for ($i = $this->Boundaries[1][0]; $i <= $this->Boundaries[1][1]; $i++) {
            echo PHP_EOL;
            echo '|';
            for ($j = $this->Boundaries[0][0]; $j <= $this->Boundaries[0][1]; $j++) {
                $key = $j . ',' . $i;
                if ([$j,$i] == $this->Robot_Coords) {
                    echo self::$Tile_Key[4];
                } else if ($j == 0 && $i == 0) {
                    echo self::$Tile_Key[3];
                } else if (isset($path[$key])) {
                    echo self::$Tile_Key[2];
                } else {
                    
                    if (isset($this->Known_Coords[$key])) {
                        echo self::$Tile_Key[$this->Known_Coords[$key]];
                    } else {
                        echo self::$Tile_Key[5];
                    }
                }
            }
            echo '|';
        }
        echo PHP_EOL;
         for ($j = $this->Boundaries[0][0]; $j <= $this->Boundaries[0][1]+1; $j++) {
            echo '--';
        }
        echo PHP_EOL;
    }

    

  
}


function get_steps_to_system_from_file($path, $is_debug) {
   $io = new Diagnostic_Robot();
    
    $result = process_intcode_from_file($path, $io, $io, $is_debug);

    

    return 0;
}



?>