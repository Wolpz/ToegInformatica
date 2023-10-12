<!DOCTYPE html>
<html>
<head>
    <title>Opdracht e</title>
</head>
<body>
<?php
    echo 'Page visitors: '.toGif(logIP_hash()).'<br>';
?>
Don't worry, your IP address is hashed and salted.
</body>
</html>
<?php
// Logs bcrypt hash of visitor's IP address in counter.txt if not already present in file, then returns length of file
 function logIP_hash(){
     if (getenv('HTTP_X_FORWARDED_FOR')){
         $ip=getenv('HTTP_X_FORWARDED_FOR');
     }
     else {
         $ip=getenv('REMOTE_ADDR');
     }
     $ip = crypt($ip, $ip); //No need for unique salt as we're specifically checking for duplicates

     $fd = fopen('../../DATA/counter.txt',"r+");

     $found = false;
     $fileLength = 0;
     while(!feof($fd)){
         $buf = trim(fgets($fd,4096));
         if($buf != '')
            $fileLength++;
         if($buf == $ip)
             $found = true;
     }
     if(!$found){
         $fileLength++;
         fwrite($fd, $ip."\n", 4096);
     }
     fclose($fd);

     return $fileLength;
 }

function toGif($c){
    settype($c,"integer");
    settype($c,"string");
    $gifHTML = '';

    for ($x=0;$x<strlen($c);$x++)
        $gifHTML .= '<img src="/images/p'.$c[$x].'.gif">';
    return $gifHTML;
}
?>