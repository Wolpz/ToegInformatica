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

<?php
 if( ($user == "") || ($password == "") ){
     // If password or user empty
     echo 'Vul alstublieft een username en wachtwoord in.<br>';
 }
 else if()

 else

?>

</body>
</html>

<?php
// Appends databases with new user and hashed+salted password. Returns 1 if successful and 0 if not.
function addNewUser($user, $pw, $db){
    return appendFile($user, 'o7usernames.txt') &&
        appendFile(crypt($pw, $user), 'o7passwords.txt');
}

// Appends at end of file in DATA directory if it exists. Returns 1 if successful, 0 if file does not exist.
function appendFile($string, $file){
    if(!file_exists('../DATA/'.$file))
        return 0;

    $feed = fopen('../DATA/'.$file, 'a');
    fwrite($feed, $string.'\n');
    return 1;
}

// Returns -1 if string is not found in file, or position of string if found (starting at 0)
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
