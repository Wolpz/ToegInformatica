<?php
if (array_key_exists('tekst',$_REQUEST))
    $tekst=$_REQUEST['tekst'];
else
    $tekst="";
?>
<html>
<head>
    <title>arrays</title>
</head>
<body bgcolor="#FFFFFF">
<?php
if (strlen($tekst)>0){
    echo "<table border=3><tr>"."\n";
    $words = explode (" ",$tekst); //Split sentence into words
    for ($y=0;$y<count($words);$y++){
        $letters=str_split($words[$y]); //Split words into letters
        for ($x=0;$x<count($letters);$x++){
            $colorVal = (0xAAAA*($x+1)*($y+1));
            echo "<td style='background-color:".sprintf("#%06X", $colorVal & 0xFFFFFF)."'>".$letters[$x]."</td>";
        }
        echo "</tr><tr>";
    }
    echo "</table>";
}
if ($tekst=="") $tekst="hoi ik ben een zin!";
?>
<div></div>
<form method="post" >
    <p> <label  for="tekst">Vul een zin in:</label>
        <input type="text" size="50" name="tekst" value="<?php echo $tekst; ?>"><br>
        <input type="submit" name="start" value="zend"></p>
</form>
</body>
</html>
