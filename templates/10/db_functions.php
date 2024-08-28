<?php
/*
- Gegevens toevoegen
- Gegevens verwijderen
- Gegevens wijzigen (adv een lijst aanwezige data)
- Sorteren
- Selecties uitvoeren

could directly send sql commands from client but that seems horribly insecure

json:
IN:
{
operation: LIST | ADD | REMOVE | EDIT | SEARCH
payload: different obj based on operation:
    LIST:
        $fields
        $sort {
            field
            order: DESC | ASC
        }
    ADD:
        fields
        vals
    REMOVE:
        id
    EDIT:
        id
        field
        val
    SEARCH:
        $fields
        $sort {
            field
            order: DESC | ASC
        }
}

OUT:
response: OK | Error
data
*/
// Database settings

// TODO COMMENT ALL THIS AND JUST RETURN JSON REQUEST FOR NOW
// TODO REDO ALL THIS TO FIT NEW WAY OF DOING THINGS?
// TODO CREATE HELPER FUNCTIONS PER OPERATION?

$payload_maps = array(
    "LIST" => array(
        "fields" => 0,
        "search" => array(
            // Search conditions go here, AND them in statement for each key
        ),
        "sort" => array(
            "field" => 0,
            "order" => 0
        )
    ),
    "ADD" => array( // TODO THIS NEEDS TO MATCH (REQ) DATABASE COLUMNS, but not ID

    ),
    "REMOVE" => array(
        "id" => 0
    ),
    "EDIT" => array(
        "id" => 0,
        "field" => 0,
        "val" => 0
    )
);

$serverhost = "sqlite:C:/Users/Laurens/o10db.db";
$dbname = "o10db.db";
$tablename = "cats";

if (isset($_POST['operation']))
    $op = $_POST['operation'];
else
    JSON_reportError("Operation field not set.");
if (isset($_POST['payload']))
    $pl = json_decode($_POST['payload']);
else
    JSON_reportError("No payload.");



array_diff();

function getTableColumns($host, $tablename){
    $sql_stmt =
        'SELECT COLUMN_NAME * 
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME='.$tablename;

    return executeSQL($host, $sql_stmt);
}

function executeSQL($host, $sql_stmt){
    try {
        /**
         * @var PDO
         */
        $conn = new PDO($host);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare($sql_stmt);
        $stmt->execute();

        $r = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $r = $stmt->fetchAll();

        $conn = null;
        return $r;
    } catch(PDOException $e){
        JSON_reportError("PDO Error: ".$e->getMessage());
    }
}


function keys_exist($arr, $reqkeys){
    foreach($reqkeys as $key){
        if(array_key_exists($key, $arr))
            continue;
        else
            return false;
    }
    return true;
}

switch($op){
    case "select":
        $sql_stmt = 'SELECT '.$fields.' FROM '.$tablename.' ORDER BY '.$sort['field'].' '.$sort['direction'];
        break;
    case "search":
        $sql_stmt = 'SELECT '.$fields.' FROM '.$tablename.' WHERE '.$fields.'='.$vals.' ORDER BY '.$sort['field'].' '.$sort['direction'];
        break;
    case "insert":
        $sql_stmt = 'INSERT INTO '.$tablename.' ('.$fields.') VALUES ('.$vals.');';
        break;
    case "remove":
        $sql_stmt = 'DELETE FROM '.$tablename.' WHERE id = '.$id.';';
        break;
    case "update":
        $sql_stmt = 'UPDATE '.$tablename.' SET '.$fields.'='.$vals.' WHERE id='.$id;
        break;
    default:
        JSON_reportError("Unknown operation.");
}






//if (isset($_POST['data'])){
//    $data_i = json_decode($_POST['data']);
//}
//else
//    JSON_reportError("Data field not set.");
//if (isset($_POST['id'])){
//    $id = $_POST['id'];
//}
//else
//    $id = null;
//if (isset($_POST['sort'])){
//    $sort = json_decode($_POST['sort']);
//}
//else{
//    $sort['direction'] = 'ASC';
//    $sort['field'] = 'id';
//}
//
//$data_sqlParsed = sql_parseData($data_i);
//$fields = $data_sqlParsed['fields'];
//$vals = $data_sqlParsed['values'];
//
//$json = array();
//$json['success'] = false;
//$json['data'] = "";
//
//$sql_stmt = "";
//switch($op){
//    case "select":
//        $sql_stmt = 'SELECT '.$fields.' FROM '.$tablename.' ORDER BY '.$sort['field'].' '.$sort['direction'];
//        break;
//    case "search":
//        $sql_stmt = 'SELECT '.$fields.' FROM '.$tablename.' WHERE '.$fields.'='.$vals.' ORDER BY '.$sort['field'].' '.$sort['direction'];
//        break;
//    case "insert":
//        $sql_stmt = 'INSERT INTO '.$tablename.' ('.$fields.') VALUES ('.$vals.');';
//        break;
//    case "remove":
//        $sql_stmt = 'DELETE FROM '.$tablename.' WHERE id = '.$id.';';
//        break;
//    case "update":
//        $sql_stmt = 'UPDATE '.$tablename.' SET '.$fields.'='.$vals.' WHERE id='.$id;
//        break;
//    default:
//        JSON_reportError("Unknown operation.");
//}
//

//
//// Serving
//echo json_encode($json);
//
//// Helpers
//function sql_parseData($data){
//    $fields = $vals = array();
//    foreach ($data as $item){
//        $fields[] = $item['field'];
//        $vals[] = sql_formatForField($item['field'], $item['data']);
//    }
//    $sql['fields'] = implode(",", $fields);
//    $sql['values'] = implode(",", $vals);
//    return $sql; //wowie
//}
//
//function sql_formatForField($fieldType, $fieldValue){
//    $to_quote = "text, datetime, date, time, boolean";
//    $add_quotes = explode(',', $to_quote);
//
//    if(in_array( strtolower($fieldType), $add_quotes))
//        return "'".$fieldValue."'";
//    else
//        return $fieldValue;
//}
//
//function JSON_reportError($msg){
//    $json['success'] = false;
//    $json['data'] = $msg;
//    echo json_encode($json);
//    exit;
//}
//
//
////$r_keys = array_keys($r[0]);
////$rt = '<tr>';
////foreach ($r_keys as $array_key) {
////    $rt .= '<th>'.$array_key.'</th>';
////}
////$rt .= '</tr>';
////foreach ($r as $item) {
////    $rt .= '<tr>';
////    foreach ($r_keys as $array_key) {
////        $rt .= '<th>'.$item[$array_key].'</th>';
////    }
////    $rt .= '</tr>';
////}
////echo $rt;

