<?php
// 0: rock  1: paper    2: scissors
function rps_resolve($choice, $generation){
    $winners = [2, 0, 1, 3];
    return ($winners[$choice] == $generation);
}

$choice = 3;
$options = ['rock', 'paper', 'scissors', 'none'];

if (isset($_POST['selectedOption'])) {
    $selectedOption = $_POST['selectedOption'];
    $choice = array_search($selectedOption, $options);
}
/*
for($i = 0; $i < 3; $i++){
    if( isset($_POST[$options[$i]])){
        $choice = $i;
        break;
    }
}*/

$json = array();
$json['player'] = $options[$choice];
$comp_choice = rand(0, 2);
$json['computer'] = $options[$comp_choice];
$json['result'] = (rps_resolve($choice, $comp_choice)) ? 'win' : 'lose';
echo json_encode($json);