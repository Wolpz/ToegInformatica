<!DOCTYPE html>
<html>
<head>
    <title>Opdracht e</title>
</head>
<body>
<?php
    echo 'Visitors: '.logIP_encrypted();
?>
</body>
</html>
<?php
// Logs bcrypt hash of visitor's IP address in counter.txt if not already present in file, then returns length of file
 function logIP_encrypted(){
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
         $fileLength++;
         if(trim(fgets($fd,4096)) == $ip)
             $found = true;
     }
     if(!$found){
         fwrite($fd, $ip, 4096);
     }
     fclose($fd);

     return $fileLength;
 }
?>