
<?php

function calculate_fuel($number) {
    return max(0, intdiv($number, 3) - 2);
}

function calculate_fuel_compounded($number) {
    $cur_fuel = calculate_fuel($number);
    $total_fuel = 0;
    while ($cur_fuel > 0) {
        $total_fuel += $cur_fuel;
        $cur_fuel = calculate_fuel($cur_fuel);
    }

    return $total_fuel;
}

function calculate_fuel_all($arr) {
    return array_sum(array_map('calculate_fuel', $arr));
}

function calculate_fuel_compounded_all($arr) {
    return array_sum(array_map('calculate_fuel_compounded', $arr));
}


function calculate_from_file($path) {
    $str = file_get_contents($path);
    return calculate_fuel_all(array_map('intval', explode(PHP_EOL, $str)));
}

function calculate_from_file_compounded($path) {
    $str = file_get_contents($path);
    return calculate_fuel_compounded_all(array_map('intval', explode(PHP_EOL, $str)));
}

?>