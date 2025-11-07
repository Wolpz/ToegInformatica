<?php
/*
- Gegevens toevoegen
- Gegevens verwijderen
- Gegevens wijzigen (adv een lijst aanwezige data)
- Sorteren
- Selecties uitvoeren

json payload map:
*/
require_once __DIR__.'/../../src/customDatabase.php';

$db_loc = __DIR__.'/../../databases/toeg_inf_db.db';
$dbname = 'toeg_inf_db';
$tablename = "imdb_top1000";

$imdb_top1000 = new CustomDatabase('sqlite:'.$db_loc, $dbname, '', '');

if (isset($_POST['DELETE'])) {
    try {
        $data = $_POST['DELETE'];
        $imdb_top1000->delete($tablename, $data);
        $reply['error'] = false;
    }
    catch(Exception $e) {
        return_error($e);
    }
}
if (isset($_POST['ADD'])) {
    try {
        // TODO add insert handling
        $imdb_top1000->insert();
        $reply['error'] = false;
    }
    catch(Exception $e) {
        return_error($e);
    }
}
if (isset($_POST['UPDATE'])) {
    try {
        $data = $_POST['UPDATE'];
        $imdb_top1000->edit($tablename, $data);
        $reply['error'] = false;
    }
    catch(Exception $e) {
        return_error($e);
    }
}
if (isset($_POST['SEARCH'])) {
    try {
        $request = json_decode($_POST['SEARCH'], true);
        $data = $request['data'];
        foreach($data as $key => $item) {
            if ($data[$key] != "")
            $data[$key] = ['LIKE' => "%".$item."%"];
        }
        $sort = $request['sort'];
        $reply['debug'] = $data;
        $reply['data'] = $imdb_top1000->select($tablename, $data, $sort, 10);
        $reply['error'] = false;
    }
    catch(Exception $e) {
        return_error($e->getMessage());
    }
}
echo json_encode($reply);

function return_error($error_msg) {
    echo http_response_code(400);
    echo json_encode(['error' => true, 'message' => $error_msg]);
    die();
}
// Login system: 401 unauthenticated, 403 unauthorized
?>