<?php
if (isset($_REQUEST['naam0']))
    $naam0=$_REQUEST["naam0"];
else
    $naam0="Jan";
if (isset($_REQUEST['adres0']))
    $adres0=$_REQUEST["adres0"];
else
    $adres0="";


$gev=0;
$fd=fopen("../../DATA/naam.txt","r");
while(!feof($fd) && $gev==0){
    $buffer=trim(fgets($fd,4096));
    if ($naam0==$buffer){
        $gev=1;
        break;
    }
}
fclose($fd);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Bestanden</title>
</head>
<body>
<form method=post>
    <input type=submit value=zend><br>
    <?php
    echo '<input type=text name="naam0" value="'.$naam0.'"><br>';
    if ($naam0!=""){
        echo "$naam0 is ";
        if ($gev!=1) echo "niet ";
        echo "in het bestand opgenomen";
    }
    ?>
</form>
</body>
</html>