<?php
if (array_key_exists('x',$_REQUEST) && array_key_exists('y',$_REQUEST)) {
    $x = $_REQUEST['x'];
    $y = $_REQUEST['y'];

    if( !(preg_match_all('/[0-9]/', $x) && (preg_match_all('/[0-9]/', $y) ) ) ){
        $msg = '<iframe width="560" height="315" src="https://www.youtube.com/embed/xvFZjo5PgG0?si=wDnPCUxmb1P7kki9&autoplay=1" title="YouTube video player" frameborder="0" allow="autoplay; clipboard-write;picture-in-picture" allowfullscreen></iframe>
        <br>Even lief doen ja? Input een valide integer.';
        $x = "";
        $y = "";
        }
    else{
        if($x > $y){
            $msg = $x.' is groter dan '.$y;
        }
        else if($x<$y){
            $msg = $x.' is kleiner dan '.$y;
        }
        else{
            $msg = $x.' is gelijk aan '.$y;
        }
    }
}
else{
    $x = "";
    $y = "";
    $msg = "Please enter two valid integers.";
}

?>
<html>
  <head>
    <title>Opdracht 3c</title>
  </head>
  <body>
    <?php echo $msg;?>
    <form method="post" >
      <input type="text" size="5" name="x" value=<?php echo $x ?> >
      <input type="text" size="5" name="y" value=<?php echo $y ?> >
      <input type="submit" value="zend">
    </form>
  </body>
</html>