<?php
if (array_key_exists('tekst',$_REQUEST)) {
    $tekst = $_REQUEST['tekst'];
    $field = preg_replace('/[^0-9]/', '', $tekst);
    $button = preg_replace('/[0-9]/', '', $tekst);
}
else{
    $button="zend";
    $field = "";
}
?>
<html>
<head>
    <title>opdracht 3b</title>
</head>
<body bgcolor="#FFFFFF">
<form method="post" >
    <p>
        <div>Vul een combinatie van letters en cijfers in:</div>
        <input type="text" size="20" name="tekst" value="<?php echo $field; ?>"><br>
        <input type="submit" name="start" value=<?php echo $button ?>>
    </p>
</form>
</body>
</html>