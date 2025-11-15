<?php
if (isset($_POST['username']))
    $user=$_POST["username"];
else
    $user="";
if (isset($_POST['password']))
    $password=$_POST["password"];
else
    $password="";
?>

<html>
<head>
    <title>7a Inloggen</title>
</head>
<body>
<?php
$loginpage = true;
if( ($user == "") || ($password == "") ){
    // If password or user empty
    echo 'Vul alstublieft een username en wachtwoord in.<br>';
    $loginpage = true;
}
else if(FindInFile($user, '../DATA/o7usernames.txt', false) > -1){
    if (isset($_POST["nw"])) {
        echo 'Kies een unieke username. Deze bestaat al.<br>';
        $loginpage = true;
    } else if (isset($_POST['log'])) {
        if (getLineFromFile(FindInFile($user, '../DATA/o7usernames.txt', false), '../DATA/o7passwords.txt') == crypt($password, $user)) {
            echo 'Welkom ' . $user . '!<br>';
            $loginpage = false;
        } else {
            echo 'Fout wachtwoord. <br>';
            $loginpage = true;
        }
    }
}
else if(FindInFile($user, '../DATA/o7usernames.txt', false) == -1){
    if(isset($_POST["nw"])){
        addNewUser($user, $password);
        echo 'U heeft succesvol een nieuwe account aangemaakt.<br>';
        $loginpage = false;
    }
    else{
        echo 'User niet gevonden.<br>';
    }
}

if($loginpage){
    echo 'Voer uw gegevens in:<br>
<form name="aanmelden" method="post">
    <input type="text" name="username" value="">
    <input type="password" name="password">
    <input type="submit" value="Login" name="log">
    <input type="submit" value="Nieuw" name="nw">
</form>';
}
else{
    echo 'U bent ingelogd!<br>';
    echo '<img src="../../images/tree-frog.jpg" width = 50%>';
}
?>

</body>
</html>

<?php
// Appends databases with new user and hashed+salted password. Returns 1 if successful and 0 if not.
function addNewUser($user, $pw){
    return appendFile($user, '../DATA/o7usernames.txt') &&
        appendFile(crypt($pw, $user), '../DATA/o7passwords.txt');
}

// Appends at end of file in DATA directory if it exists. Returns 1 if successful, 0 if file does not exist.
function appendFile($string, $file){
    if(!file_exists($file))
        return 0;

    $feed = fopen($file, 'a');
    fwrite($feed, $string."\n");
    fclose($feed);
    return 1;
}


// 502 ERROR IS HERE SOMEWHERE
// Returns -1 if string is not found in file, or position of string if found (starting at 0)
function FindInFile($findString, $filepath, $IGNORECASE = false){
    if($IGNORECASE)
        $findString = strtolower($findString);

    $position = 0;
    $fd = @fopen($filepath, "r");
    if (!$fd) return -1;

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
    $fd = @fopen($filepath, "r");
    if (!$fd) return '';
    $buffer = '';
    for ($i=0; $i <= $linenum && !feof($fd); $i++){
        $buffer = fgets($fd, 4096);
    }
    fclose($fd);
    return $buffer === false ? '' : trim($buffer);
}
?>
