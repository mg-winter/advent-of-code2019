
<?php
require_once '../util/util.php';
require_once '../day-5/process_intcode-v2.php';

/**To use multithreading, PHP must be recompiled  with zts enabled
 * and the pthreads extension added. Instructions: 
 * https://medium.com/@rossbulat/true-php7-multi-threading-how-to-rebuild-php-and-use-pthreads-bed4243c0561
 * 
 * Another option would be to build a Javascript-style tick-based interpreter for Intcode,
 * but that is too much for a part b.
 */
class Connected_Input_Output extends Volatile {
    private $Lines = [];
    private $ID;
    
    public function __construct($initial_lines, $id) {
        $this->Lines = $initial_lines;
        $this->ID = $id;
        $this->Line_Pointer = 0;
    }


    public function read() {  
        $res = $this->synchronized(function($thread) {
            $val = $thread->Lines[$this->Line_Pointer];
            while (!isset($val)) {
                $thread->wait();
                $val = $thread->Lines[$this->Line_Pointer];
            }
            $thread->Line_Pointer++;
            return $val;
        }, $this);
        return $res;
    }

    public function write($value) {
        
        return $this->synchronized(function($thread) use ($value) {
            $next_index = count($thread->Lines);
            $thread->Lines[$next_index] = $value;
            $thread->notify();
            return $next_index;
        }, $this);
    }    
}

class Intcode_Runner extends Worker {
    private $IO;
    private $Program;
    private $Is_Debug;

    public function __construct($program, $io, $Is_Debug) {
        $this->Program = $program;
        $this->IO = $io;
        $this->Is_Debug = $Is_Debug;
    }

    public function run() {
        return $this->synchronized(function($thread) {
        return process_intcode_arr($this->Program, $this->IO['input'], 
                            $this->IO['output'], $this->Is_Debug);
        }, $this);
    }
}

function get_sequences_rec($prev_arr, $arr) {
    if (count($arr) < 2) {
        return [array_merge($prev_arr, $arr)];
    } else {
        $recs = array_map(function($i) use ($arr) {
            return ['head' => $arr[$i], 
                    'tail' => array_merge(array_slice($arr, 0, $i), array_slice($arr, $i+1))];
        }, range(0, count($arr)-1));
        return call_user_func_array('array_merge', array_map(function($rec) use ($prev_arr) {
            $new_prev = array_merge($prev_arr, [$rec['head']]);
            return get_sequences_rec($new_prev, $rec['tail']);
        },$recs));
    }
}
function get_sequences($arr) {
    return get_sequences_rec([], $arr);
}

function test_phase_sequence($sequence, $arr, $is_debug) {

    $output = new Cached_Output();
    $output->write(0);

    for ($i = 0; $i < count($sequence); $i++) {
        process_intcode_arr($arr, new Cached_Input([$output->get_last(), $sequence[$i]]), $output, $is_debug);
    }
    echo PHP_EOL;
    return $output->get_last();
}

function test_phase_sequence_cycle($sequence, $program, $is_debug) {
    $amp_range = range(0,count($sequence) - 1);

    $connectors = array_map(function($i) use ($sequence) {
        return new Connected_Input_Output([$sequence[$i]], $i);
    }, $amp_range);

    $connectors[0]->write(0);
    $pairs = array_map(function($i) use ($connectors) {
        $output_index = $i >= count($connectors) - 1 ? 0 : $i + 1;
        return ['input' => $connectors[$i], 'output' => $connectors[$output_index]];
    }, $amp_range);

    $runners = array_map(function($pair) use ($program, $is_debug) {
        return new Intcode_Runner($program, $pair, $is_debug);
    }, $pairs);

    foreach ($runners as $runner) {
        $runner->start();
    }

    for ($i = count($runners) - 1; $i >=0; $i--) {
        $runners[$i]->join();
    }

    return $connectors[0]->read() . '';
}

function test_amplifiers($arr, $is_debug) {

    $sequences = get_sequences(range(0,4));
    $results = array_map(function($seq) use ($arr, $is_debug) {
        return ['seq' => $seq, 'max' => test_phase_sequence($seq, $arr, $is_debug)];
    }, $sequences);

    return array_reduce($results, function($prev_max, $curr) {
        return $prev_max === null ||  $prev_max['max'] < $curr['max'] ? $curr : $prev_max;
    }, null);
   
   
}

function test_amplifiers_cycle($arr, $is_debug) {

    $sequences = get_sequences(range(5,9));
    $results = array_map(function($seq) use ($arr, $is_debug) {
        return ['seq' => $seq, 'max' => test_phase_sequence_cycle($seq, $arr, $is_debug)];
    }, $sequences);

    return array_reduce($results, function($prev_max, $curr) {
        return $prev_max === null ||  $prev_max['max'] < $curr['max'] ? $curr : $prev_max;
    }, null);
   
   
}
 function test_amplifiers_from_file($path, $is_debug) {
    $str = file_get_contents($path);
    return test_amplifiers(explode(',',$str), $is_debug);
 }

 function test_amplifiers_cycle_from_file($path, $is_debug) {
    $str = file_get_contents($path);
    return test_amplifiers_cycle(explode(',',$str), $is_debug);
 }

?>