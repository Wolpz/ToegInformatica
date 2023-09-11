<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="refresh" content="30" />
</head>
<body>
<?php
$months = array('Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December');
$month = date('m')-1;

echo "Het is vandaag:<br>".
    date('d')." ".$months[$month]." ".date('Y')."<br>".
    "De tijd is ".date('H.i.')."<br>".
    "Zie voor het inlezen van de datum bij de informatie<br>
    tik na de volgende code in (:<br>"
?>
</body>
</html>