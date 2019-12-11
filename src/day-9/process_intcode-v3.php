
<?php
require_once '../util/util.php';

interface Input {
    function read();
}

interface Output {
    function write($value);
}

class Cached_Input implements Input {
    private $Storage;
    private $Current_Line = 0;

    public function __construct($values) {
        $this->Storage = $values;
    }

    public function read() {
        return $this->Storage[$this->Current_Line];
        $this->Current_Line++;
    }
}

class Cached_Output implements Output {
    private $Storage = [];


    public function write($value) {
        return array_push($this->Storage, $value) - 1;
    }

    public function get_line($index) {
        return $this->Storage[$index];
    } 

    public function get_last() {
        return $this->Storage[count($this->Storage) - 1];
    }

    public function __toString() {
        return implode(',', array_map(function($line) {
            return $line . '';
        },$this->Storage));
    }
}

class Intcode_Computer {

    static $Program_Codes = [
                    '01' => 'calculate_add', 
                    '02' => 'calculate_mult', 
                    '03' => 'save_input',
                    '04' => 'output',
                    '05' => 'jump_if_true',
                    '06' => 'jump_if_false',
                    '07' => 'calculate_less_than',
                    '08' => 'calculate_equals',
                    '09' => 'adjust_relative_base',
                    '99' => 'halt' 
                ];
    private $Program;
    private $Instruction_Pointer = 0;
    private $Input;
    private $Output;
    private $Is_Debug;
    private $Relative_Base = 0;

    public function __construct($program, $input, $output, $is_debug) {
        $this->Program = $program;
        $this->Input = $input;
        $this->Output = $output;
        $this->Is_Debug = $is_debug;
    }

    function run() {
        $res = ['next_index' => 0, 'halt' => false];
        $arr_length = count($this->Program);

    
        while(!$res['halt'] && $res['next_index'] < $arr_length) {
        
            $this->Instruction_Pointer = $res['next_index'];
            
            $cur_code = $this->Program[$this->Instruction_Pointer];

            $opcode_pos = strlen($cur_code) - 2;
            $opcode =  $opcode_pos > 0 ? substr($cur_code, $opcode_pos) 
                        : str_pad($cur_code, 2, "0", STR_PAD_LEFT);

            $cur_func = self::$Program_Codes[$opcode];
            if (!isset($cur_func)) {
                throw new Exception('Unknown code: ' + $cur_code);
            }

            $modes = $opcode_pos > 0 ? array_reverse(str_split(substr($cur_code, 0, $opcode_pos))) 
                                : [];
            if ($this->Is_Debug) {
                echo PHP_EOL . $this->Instruction_Pointer . ' (' . $cur_func . ')';
            }
            $res = call_user_func_array([$this, $cur_func], [$modes]);
            if ($this->Is_Debug) { 
                echo ': '. $res['desc'] . PHP_EOL;
            }
        }

        return explode(',',implode($this->Program,','));
    }

    function calculate_add($modes) {
        return $this->calculate_binary($modes, function($a,$b){return $a + $b;}, 'add');
    }

    function calculate_mult($modes) {
        return $this->calculate_binary($modes, function($a,$b){return $a * $b;}, 'multiply');
    }

    function calculate_less_than($modes) {
        return $this->calculate_binary($modes, function($a, $b){return $a < $b;}, 'less than');
    }

    function calculate_equals($modes) {
        return $this->calculate_binary($modes, function($a,$b){return $a == $b;}, 'equals');
    }

    function jump_if_true($modes) {
        return $this->jump_if(2, $modes, 3, 0 != $this->get_value(1, $modes));
    }

    function jump_if_false($modes) {
        return $this->jump_if(2, $modes, 3, 
                            0 == $this->get_value(1, $modes));
    }

    function jump_if($jump_val_offset, $modes, $slots_to_skip, $is_jump) {

        $next_index = $is_jump ? $this->get_value($jump_val_offset, $modes) 
                    : $this->Instruction_Pointer + $slots_to_skip;
        return ['next_index' => $next_index, 'halt' => false, 
        'desc' => $is_jump ? 'jump to ' . $next_index : 'continue to ' . $next_index];
    }

    function adjust_relative_base($modes) {
        $this->Relative_Base += $this->get_value(1,$modes);
        return ['next_index' => $this->Instruction_Pointer+2, 'halt' => false, 
        'desc' => 'change relative base to ' . $this->Relative_Base];
    }

    function save_input($modes) {
        $input_val = $this->Input->read();
        $output_offset = $this->set_value(1, $modes, $input_val);

        return ['next_index' => $this->Instruction_Pointer + 2, 'halt' => false, 
        'desc' => 'saved ' . $input . ' into position ' . $output_index];
    }

    function output($modes) {
   
        $val_to_output = $this->get_value(1, $modes);
        
        $output_res = $this->Output->write($val_to_output);

        return ['next_index' => $this->Instruction_Pointer + 2, 'halt' => false, 
        'desc' => 'output ' . $val_to_output . ', got back ' . $output_res];
    }


    function halt($modes) {
        return ['next_index' => -1, 'halt' => true, 
                'desc' => 'halt @ position ' . $this->Instruction_Pointer];
    }


    function calculate_binary($modes, $calc_func, $calc_desc) {
        return $this->calculate($modes, 2, $calc_func, $calc_desc);
    }
    function calculate($modes, $num_params, $calc_func, $calc_desc) {

        $output_offset = $num_params + 1;        
        $args = $this->get_values($num_params, $modes);

        $set_res = $this->set_value($output_offset, $modes, call_user_func_array($calc_func, $args));
        return ['next_index' => $this->Instruction_Pointer + $output_offset + 1, 'halt' => false, 
                'desc' => $calc_desc . ' ' . implode(',', $args) 
                . ' into position ' . $set_res];
    } 

    function get_values($num_params, $modes) {
        return array_map(function($offset) use ($modes) {
            return $this->get_value($offset, $modes);
        }, range(1, $num_params));
    }

    function get_value($offset, $modes) { 
        $index = $this->get_mode_address($offset, $modes);
        $this->check_index($index, 'read address');
        return $this->Program[$index];
    }

    private function set_value($offset, $modes, $value) {
        $index = $this->get_mode_address($offset, $modes);
        $this->check_index($index, ' output address ');
        $this->Program[$index] = $value;    
        return $index;
    }

    private function get_mode_address($offset, $modes) {
        $mode = isset($modes[$offset-1]) ? $modes[$offset-1] : 0;
        $index = $this->Instruction_Pointer+$offset;
        switch ($mode) {
            case 2:
                return $this->Relative_Base + $this->Program[$index];
            case 1:
                return $index;
            default:
                return $this->Program[$index];
        }
    }

    function check_index($index, $index_desc) {
        if ($index < 0 ) {
            throw new Exception('Index "'.$index.'" for ' . $index_desc . ' out of bounds: ' 
                                . $index . '. Must be above 0.');
        }
    }
}


function process_intcode_arr($program, $input, $output, $is_debug) {
    $computer = new Intcode_Computer($program, $input, $output, $is_debug);
    return $computer->run();
}

function process_intcode_from_file($path, $input, $output, $is_debug) {
    $str = file_get_contents($path);
    return implode(',', process_intcode_arr(explode(',', $str), $input, $output, $is_debug));
}

?>