<!DOCTYPE html>
<html>
<head>
    <title>opdracht 5c</title>
    <meta http-equiv="refresh" content="30" />
    <link rel="stylesheet" href="/styles/styles.css">
</head>
<body>

<div class="flex-container-row">Het is vandaag:</div>
<div class="flex-container-row">
<?php
echo toGif(date('d'));
echo monthToText(date('m'));
echo toGif(date('Y'));
?>
</div>
<div class="flex-container-row">
<?php
echo 'De tijd is ';
echo toGif(date('H')).':';
echo toGif(date('i'));
?>
</div>

</body>
</html>

<?php
function toGif($c){
    settype($c,"integer");
    settype($c,"string");

    for ($x=0;$x<strlen($c);$x++)
    echo '<img src="/images/p'.$c[$x].'.gif">';
}

function monthToText($month){
    if($month > 12 || $month < 1)
        return "Janfebmaartuary (this is an error)";

    $months = array('Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December');

    return $months[$month-1];
}
?>