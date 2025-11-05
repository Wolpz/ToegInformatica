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

$reply = ['error' => true];
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
        // TODO
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
        $sort = $request['sort'];
        $reply['data'] = $imdb_top1000->select($tablename, $data, $sort, 10);
        $reply['error'] = false;
    }
    catch(Exception $e) {
        return_error($e->getMessage());
    }
}
echo json_encode($reply);

function return_error($error_msg) {
    echo http_response_code(400);   // <-- non-2xx code is key
    echo json_encode(['error' => true, 'message' => $error_msg]);
}
?>