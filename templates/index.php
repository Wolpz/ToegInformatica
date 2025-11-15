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
    Veel leuker dan individueel linkjes gaan knippen en plakken :) <br>
    <?php
    $dir = __DIR__;
    if (!is_dir($dir)) {
        die("Directory not found: $dir");
    }
    listdir($dir);
    ?>
    </body>
    </html>

<?php
function listdir($dir){
    $dirlist = scandir($dir);
    $path = str_replace(__DIR__, '', $dir);
    if(sizeof($dirlist) <= 0){
        echo 'Empty directory.<br>';
    }
    else{
        foreach ($dirlist as $file) {

            if ($file === '.' || $file === '..') continue;

            if( preg_match('/^opdracht.*\.php$/', $file) )
                echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$path.$file.'">'.preg_replace('/\.php/', '' , $file).'</a><br>';
            else if(!preg_match('/\./', $file)){
                echo $file.':<br>';
                $subdir = rtrim($dir, '/') . '/' . $file . '/';
                listdir($subdir);
            }
        }
    }
}
?>