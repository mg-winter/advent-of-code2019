
<?php
require_once '../day-9/process_intcode-v3.php';


function wrap_to_length($pos, $arr) {
      if ($pos < 0) {
            return count($arr) - 1;
        } else if ($pos >= count($arr)) {
            return 0;
        } else {
            return $pos;
        }

}

class Painter implements Input, Output {

    static $DIRECTIONS = [[0,1],[1,0],[0,-1],[-1,0]];
    static $PAINT_CHARS = [" ", "\u{1F431}"];

    private $Painted_Grid = [];
    private $Current_Coords = [0,0];
    private $Current_Direction = 0;
    private $Computer;
    private $Output_Log = [];
    private $Output_Pointer = 0;
    private $Program;
    private $Is_Debug;

    private $X_Range = [0,0];
    private $Y_Range = [0,0];

    public function __construct($program, $is_debug, $initial_colour) {
        $this->Is_Debug = $is_debug;
        $this->Program = $program;
        $this->Painted_Grid['0,0'] = $initial_colour;
    }

    public function get_colour($coords_str) {
        if (isset($this->Painted_Grid[$coords_str])) {
            return $this->Painted_Grid[$coords_str];
        } else {
            return 0;
        }
    }

    public function turn($direction_code) {
        $this->Current_Direction = wrap_to_length($this->Current_Direction + ($direction_code ? 1 : -1), self::$DIRECTIONS);        
        $this->Current_Coords[0] += self::$DIRECTIONS[$this->Current_Direction][0];
        $this->Current_Coords[1] += self::$DIRECTIONS[$this->Current_Direction][1];
    }

    public function paint($colour) {
        $this->Painted_Grid[implode(',', $this->Current_Coords)] = $colour;
        $this->X_Range[0] = min($this->Current_Coords[0], $this->X_Range[0]);
        $this->X_Range[1] = max($this->Current_Coords[0], $this->X_Range[1]);

        $this->Y_Range[0] = min($this->Current_Coords[1], $this->Y_Range[0]);
        $this->Y_Range[1] = max($this->Current_Coords[1], $this->Y_Range[1]);
       
    }

    public function get_current_colour() {
        return $this->get_colour(implode(',', $this->Current_Coords));
    }

    public function read() {
        return $this->get_current_colour();
    }

    public function write($value) {
        if (isset($this->Output_Log[$this->Output_Pointer])) {
            $row = $this->Output_Log[$this->Output_Pointer];
            array_push($row, $value);

            $this->implement_instruction($row[0], $row[1]);
            $this->Output_Pointer++;
        } else {
            $this->Output_Log[$this->Output_Pointer] = [$value];
        }

        return $this->Output_Pointer;
    }

    public function implement_instruction($new_colour, $dir_code) {
        $this->paint($new_colour);
        $this->turn($dir_code);
    }

    public function get_touched_panels_count() {
        return count($this->Painted_Grid);
    }

    public function run() {
        var_dump($this->Program);
        $this->Computer = new Intcode_Computer($this->Program, $this, $this, $this->Is_Debug);
        $this->Computer->run();
    }

    public function get_painted_str() {
        $lines = [];
        for ($i = $this->Y_Range[1]; $i >= $this->Y_Range[0]; $i--) {
            $line = [];
            for ($j = $this->X_Range[0]; $j <= $this->X_Range[1]; $j++) {
                array_push($line, self::$PAINT_CHARS[$this->get_colour($j . ',' . $i)]);
            }
            array_push($lines, $line);
        }
        return implode(PHP_EOL, array_map(function($arr){return implode('', $arr);}, $lines));
    }
}

function paint_identifier($program, $is_debug) {
    $painter = new Painter($program, $is_debug, 1);
    $painter->run();
    return $painter->get_painted_str();
}
function get_panels_count($program,  $is_debug) {
    $painter = new Painter($program, $is_debug, 0);
    $painter->run();
    return $painter->get_touched_panels_count();
}

function get_panels_count_from_file($path, $is_debug) {
    $str = file_get_contents($path);
    return get_panels_count(explode(',', $str), $is_debug);
}

function paint_identifier_from_file($path, $is_debug) {
    $str = file_get_contents($path);
    return paint_identifier(explode(',', $str), $is_debug);
}

?>