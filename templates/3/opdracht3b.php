<?php
if (array_key_exists('tekst',$_REQUEST)) {
    $tekst = $_REQUEST['tekst'];
    $button = preg_replace('/[a-z]/', '', $tekst);
    $tekst = preg_replace('/[0-9]/', '', $tekst);
}
else{
    $button="zend";
    $tekst = "";
}
?>
<html>
<head>
    <title>Type conversie</title>
</head>
<body bgcolor="#FFFFFF">
<form method="post" >
    <p>
        <input type="text" size="20" name="tekst" value="<?php echo $tekst; ?>"><br>
        <input type="submit" name="start" value=<?php echo $button ?>>
    </p>
</form>
</body>
</html>