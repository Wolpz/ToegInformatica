<?php
if (isset($_REQUEST['username']))
    $user=$_REQUEST["username"];
else
    $user="";
if (isset($_REQUEST['password']))
    $password=$_REQUEST["password"];
else
    $password="";
?>
<html>
<head>
    <title>Inloggen</title>
</head>
<body>
Voer uw gegevens in:<br>
<form name=aanmelden method="post">
    <input type="text" name="username" value="">
    <input type="password" name="password">
    <input type="submit" value="Login" name="log">
    <input type="submit" value="Nieuw" name="nw">
</form>

</body>
</html>

<?php

function addNewUser($user, $pw, $db){

}

// Returns -1 if string (case insensitive) is found in file, or position of string if found (starting at 0)
function FindInFile($findString, $filepath, $IGNORECASE = false){
    if($IGNORECASE)
        $findString = strtolower($findString);

    $position = 0;
    $fd=fopen($filepath,"r");
    while(!feof($fd)){
        if($IGNORECASE)
            $buffer=strtolower(trim(fgets($fd,4096)));
        else
            $buffer=trim(fgets($fd,4096));

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
    for($i = 0; ($i <= $linenum || feof($fd)); $i++){
        $buffer=strtolower(fgets($fd,4096));
    }
    fclose($fd);
    return $buffer;
}
?>
