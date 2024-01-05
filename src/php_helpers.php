<?php
// Appends databases with new user and hashed+salted password. Returns 1 if successful and 0 if not.
function addNewUser($user, $pw){
    return appendFile($user, '../../DATA/o7usernames.txt') &&
        appendFile(crypt($pw, $user), '../../DATA/o7passwords.txt');
}

// Appends at end of file in DATA directory if it exists. Returns 1 if successful, 0 if file does not exist.
function appendFile($string, $file){
    if(!file_exists($file))
        return 0;

    $feed = fopen($file, 'a');
    fwrite($feed, $string."\n");
    return 1;
}


// 502 ERROR IS HERE SOMEWHERE
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
    for($i = 0; ($i <= $linenum && !feof($fd)); $i++){
        $buffer=fgets($fd,4096);
    }
    fclose($fd);
    return trim($buffer);
}

function factorial($n){
    $output = $n;
    for($i = $n-1; $i > 0; $i--)
        $output *= $i;
    return $output;
}
?>
