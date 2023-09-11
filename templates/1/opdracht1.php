<!DOCTYPE html>
<html>
<head>
    <title>Opdracht 1</title>
</head>
<body>
<?php
$text = array("Wordt", "dit", "nou", "steeds", "groter?");
for ($i=0; $i<count($text); $i++){
    $fsize = (($i+1)*10)."px";
    if($i%2){
        echo "<div style='font-size:$fsize;'>". $text[$i] ."</div>";
    }
    else {
        echo "<div style='font-size:$fsize;'><i>". $text[$i] . "</i> </div>";
    }
}
?>
</body>
</html>
