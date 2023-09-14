<?php
if (array_key_exists('getal',$_REQUEST))
    $getal=$_REQUEST['getal'];
else
    $getal="";
?>

<html>
<head>
    <title>Opdracht b</title>
</head>
<body>
<?php

function converteer($c){
    settype($c,"integer");
    settype($c,"string");

    for ($x=0;$x<strlen($c);$x++)
        echo '<img src="../images/p'.$c[$x].'.gif">';
}

converteer($getal);

?>

<form  method="post">
    <input type="text"  name="getal" value="<?php echo $getal ?>">
    <input type="submit" value="zend">
</form>

</body>
</html>