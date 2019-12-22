
<?php
require_once '../day-9/process_intcode-v3.php';

class Array_Output extends Cached_Output {
    function get_lines() {
        return array_map(function($line){return $line;}, $this->Storage);
    }
}

class Joystick_Input implements Input {
    private static $Positions = ['n' => 0, 'l' => -1, 'r' => 1];
    private $Last_Position = '';
    
    public function read() {
        $prompt = 'Joystick position: ' . PHP_EOL
                    . 'n for neutral, l for left, r for right' . PHP_EOL
                    . 'blank to repeat previous (' . $this->Last_Position . '): ';
        $answer = readline($prompt);
        $parsed_answer = $answer ? $answer : $this->$Last_Position;
        $this->$Last_Position = $parsed_answer;

        return self::$Positions[$parsed_answer];
    }
}

class Display_Output extends Array_Output {

    protected static $Items_Per_Set = 3;
    protected static $Tile_Key = [
        0 => ' ',
        1 => '|',
        2 => "\u{2588}",
        3 => '-',
        4 => "\u{1F431}"
    ];
 
    protected $Display_Grid = [];
  
    protected $Score = 0;
    protected $Paddle_X = -1;
    protected $Ball_Coords = [-1,-1];


    public function write($value) {

        parent::write($value);
        $num_items = count($this->Storage);
        

        if ($num_items % self::$Items_Per_Set == 0) {

            $keys = range($num_items - self::$Items_Per_Set, $num_items-1);
            $cur_storage = array_map(function($k) {
                return $this->get_line($k);
            }, $keys);
            $this->update_state($cur_storage);
        }
    }

    function update_state($cur_storage) {
        $x = $cur_storage[0];
        $y = $cur_storage[1];

        $value = $cur_storage[2];

        if ($x == -1 && $y == 0) {
            $this->set_score($value);
        } else {
            $this->update_display($x, $y, $value);
        }
    }
    public function update_display($x, $y, $value) {
        if (!isset($this->Display_Grid[$y])) {
            $this->Display_Grid[$y] = [];
        }
        
        switch ($value) {
            case 3:
                $this->Paddle_X = $x;
                break;
            case 4: 
                $this->Ball_Coords = [$x, $y];
                break;
        }
        $this->Display_Grid[$y][$x] = self::$Tile_Key[$value];
        $this->output_display();

    }

    public function output_display() {
        echo PHP_EOL;
        echo implode(PHP_EOL, array_map(function($arr){return implode('', $arr);}, $this->Display_Grid));
        echo PHP_EOL;
    }

    public function set_score($value) {
        $this->Score = $value;
        echo PHP_EOL . 'Score: ' . $this->Score . PHP_EOL;
    }
    public function get_score() {
        return $this->Score;
    }
}

/**I am a clutz, so will get a robot to play the game for me. */
class Self_Playing_Input_Output extends Display_Output implements Input {
    public function read() {
        $diff = $this->Ball_Coords[0] - $this->Paddle_X;

        return $diff == 0 ? $diff : $diff/abs($diff);
    }
}



function get_tile_count_from_file($path, $is_debug) {
    $input = new Cached_Input([]);
    $output = new Array_Output();
    
    $result = process_intcode_from_file($path, $input, $output, $is_debug);

    $instructions =   $output->get_lines();
    $num_blocks = 0;
    for ($i = 2; $i < count($instructions); $i += 3) {
        if ($instructions[$i] == 2) {
            $num_blocks++;
        }
    }

    return $num_blocks;
}

function play_game_from_file($path, $is_debug) {
    $io = new Self_Playing_Input_Output();
    
    $result = process_intcode_from_file($path, $io, $io, $is_debug);

    return $io->get_score();
}

?>