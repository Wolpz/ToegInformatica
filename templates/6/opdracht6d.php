<?php
if (isset($_REQUEST['naam0']))
    $naam0=$_REQUEST["naam0"];
else
    $naam0="Jan";
if (isset($_REQUEST['adres0']))
    $adres0=$_REQUEST["adres0"];
else
    $adres0="";

$pos = FindInFile($naam0, "../DATA/namen.txt");
if($pos != -1){
    $naamMsg = $naam0.' is gevonden.';
    $adresMsg = 'Adres: '.getLineFromFile($pos, "../DATA/adressen.txt");
}
else{
    $naamMsg = $naam0.' is niet gevonden.';
    $adresMsg = '';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bestanden</title>
</head>
<body>
<div>
    Valid names:<br>
    henk<br>
    jan<br>
    piet<br>
    klaas<br>
</div>
<form method=post>
    <input type=submit value=zend><br>
    <?php
    echo '<input type=text name="naam0" value="'.$naam0.'"><br>';
    ?>
</form>
<div>
    <?php echo $naamMsg.'<br>'.$adresMsg.'<br>'; ?>
</div>
</body>
</html>

<?php
// Returns -1 if string (case insensitive) is found in file, or position of string if found (starting at 0)
    function FindInFile($findString, $filepath){
        $findString = strtolower($findString);
        $position = 0;

        $fd = @fopen($filepath, "r");
        if (!$fd) return -1; // file not found or cannot be opened

        while(!feof($fd)){
            $buffer=strtolower(trim(fgets($fd,4096)));
            if ($buffer == $findString){       //preg_match('/'.$findString.'/', $buffer)){
                fclose($fd);
                return $position;
            }
            $position++;
        }
        fclose($fd);
        return -1;
    }

// Returns a desired line of a file (starting at 0)
function getLineFromFile($linenum, $filepath){
    $buffer = '';
    $fd = fopen($filepath,"r");
    for($i = 0; ($i <= $linenum && !feof($fd)); $i++){
        $buffer=strtolower(fgets($fd,4096));
    }
    fclose($fd);
    return $buffer;
}
?>