<?php

$DATADIR = "../../DATA/";

if (isset($_POST['graphType']))
    $type = $_POST["graphType"];
else
    $type = null;
if (isset($_POST['fileSelect']))
    $fileName = $DATADIR . "o9_graphdata_" . $_POST["fileSelect"];
else
    $fileName = null;

if(!file_exists($fileName) or $fileName == null)
    $data = "Failed";
else{
    $data = file($fileName);
}

$json = array();
$json['fileData'] = $data;
echo json_encode($json);