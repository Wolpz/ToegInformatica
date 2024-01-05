<?php
if (isset($_REQUEST['keuze']))
    $keuze=$_REQUEST['keuze'];
else
    $keuze=0;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Toegepaste Informatica Index</title>
</head>
<body>
<h1>Opdrachten</h1>
Dit was even werk om te automatiseren met php maar veel leuker dan individueel linkjes gaan knippen en plakken :) <br>
<?php
    $dir = '../templates/';
    listdir($dir);
?>
</body>
</html>

<?php
 function listdir($dir){
     $dirlist = scandir($dir);

     if(sizeof($dirlist) <= 0){
         echo 'Empty directory.<br>';
     }
     else{
         for($i = 0; $i < sizeof($dirlist); $i++){
             if( preg_match('/^opdracht.*\.php$/', $dirlist[$i]) )
                 echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$dir.$dirlist[$i].'">'.preg_replace('/\.php/', '' , $dirlist[$i]).'</a><br>';
             else if(!preg_match('/\./', $dirlist[$i])){
                 echo $dirlist[$i].':<br>';
                 listdir($dir.$dirlist[$i].'/');
             }
         }
     }
}
?>