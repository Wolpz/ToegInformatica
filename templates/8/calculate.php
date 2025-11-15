<?php
//- Optellen
//- Aftrekken
//- Vermenigvuldigen
//- Delen
//- Wortel trekken
//- Kwadrateren
//- Faculteit
//- World domination
include '../../src/php_helpers.php';

if (isset($_POST['input_1']))
    $in1 = floatval($_POST["input_1"]);
else
    $in1 = 0;;
if (isset($_POST['input_2']))
    $in2 = floatval($_POST["input_2"]);
else
    $in2 = 0;
if (isset($_POST['mode']))
    $mode = $_POST["mode"];
else
    $mode = 'none';

$output = "The ".$mode." of ".$in1;

switch($mode){
    case 'sum':
        $output .= " and ".$in2." is ".($in1+$in2);
        break;
    case 'subtraction':
        $output .= " and ".$in2." is ".($in1-$in2);
        break;
    case 'multiplication':
        $output .= " and ".$in2." is ".($in1*$in2);
        break;
    case 'division':
        $output .= " and ".$in2." is ".($in1/$in2);
        break;
    case 'square root':
        $output .= " is ".sqrt($in1);
        break;
    case 'square':
        $output .= " is ".pow($in1,2);
        break;
    case 'factorial':
        $output .= " is ".factorial($in1);
        break;
    case 'world domination':
        $output = '<audio controls autoplay><source src="../DATA/lego-yoda-death-sound-effect.mp3" type="audio/mpeg"></audio>';
        break;
    default:
        $output = "Please select a mode. You really shouldn't be seeing this.";
}

    $json = array();
    $json['output'] = $output;
    echo json_encode($json);

function factorial($n){
    if ($n < 0) return "undefined";
    if ($n === 0) return 1;
    $result = 1;
    for($i = 1; $i <= $n; $i++){
        $result *= $i;
    }
    return $result;
}


