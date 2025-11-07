<?php
// TODO document this with doxygen
require "php_helpers.php";

class CustomDatabase
{
    private $db_host;
    private $db_name;
    private $pdo_conn;
    private $driver;

    function __construct($host, $db_name, $username, $password){
        $this->db_host = $host;
        $this->db_name = $db_name;
        try{
            $this->pdo_conn = new PDO($host, $username, $password);
            $this->pdo_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->driver = $this->pdo_conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        }
        catch(PDOException $e){
            $this->pdo_conn = null;
            throw new Exception("Connection failed: " . $e->getMessage()."\r\n");
        }
    }

    function __destruct()
    {
        $this->pdo_conn = null;
    }

    function getColumnMeta($tableName){
        $columns = [];
        // TODO add MariaDB driver
        switch($this->driver){
            case "sqlite":
                $s = $this->pdo_conn->query("PRAGMA table_info('$tableName')");
                $cols = $s->fetchAll();
                foreach($cols as $col){
                    $columns[$col['name']] = [
                        'type' => $col['type'],
                        'isRequired' => $col['notnull'],
                        'isPrimaryKey' => $col['pk']
                    ];
                }
                break;
            default:
                echo "Unsupported database driver: $this->driver";
        }
        return $columns;
    }

    function getRequiredFields($tableName){
        $cols = $this->getColumnMeta($tableName);
        $reqCols = [];
        foreach($cols as $name => $col){
            if($col['isRequired'] && !$col['isPrimaryKey']){
                $reqCols[] = $name;
            }
        }
        return $reqCols;
    }

    function validateDataForTable($tableName, $data)
    {
        $keys = array_keys($data);
        $reqCols = $this->getRequiredFields($tableName);
        $meta = $this->getColumnMeta($tableName);

        // Check if all required fields have data
        if (array_diff($reqCols, $keys)) {
            throw new Exception("Required column(s) missing: " . implode(', ', array_diff($reqCols, $keys)));
        }
        foreach ($reqCols as $reqCol) {
            if ($data[$reqCol] == null || $data[$reqCol] == '') {
                throw new Exception("Required column empty: $reqCol");
            }
        }
        foreach ($keys as $key) {
            $value = $data[$key];
            $type = $meta[$key]['type'];
            switch ($type) {
                case "integer":
                case "int":
                    if (!is_int($value + 0)) {
                        throw new Exception("Invalid type: $key should be $type.");
                    }
                    break;
                case "string" || "text":
                    break;
                case "double":
                case "float":
                    if (!is_numeric($value)) {
                        throw new Exception("Invalid type: $key should be $type.");
                    }
                    break;
                default:
                    echo "Unknown type: $type";
            }
        }
    }

    /* Select
     * Performs select query on fields present in $data, with a WHERE term for each field that isn't empty.
     */
    function select(string $tableName, array $data, array $sort = [], int $limit = -1){
        // Validate sort column and direction
        $columns = $this->getColumnMeta($tableName);
        $columnNames = array_keys($columns);
        if(!empty($sort)) {
            if (!in_array($sort['column'], $columnNames)) {
                throw new Exception("Invalid sort field: " . $sort['column']);
            }
            if ($sort['direction'] !== 'ASC' && $sort['direction'] !== 'DESC') {
                throw new Exception("Invalid sort direction: " . $sort['direction']);
            }
        }

        // Validate that all requested fields exist in the table
        $fields = array_keys($data);
        if ($missingFields = array_diff($fields, $columnNames)) {
            throw new Exception("Field not in table: " . implode(', ', $missingFields));
        }

        // SQL query
        $sql = "SELECT " . implode(', ', $fields) . " FROM $tableName";

        // Add WHERE clause if there are search criteria
        $search = [];
        foreach ($data as $field => $value) {
            if ($value === '' || $value === null)
                continue;
            if(is_array($value)){
                [$op, $val] = each($value);
                $search[] = "$field $op :$field";
                $data[$field] = $val;
            }
            else {
                $search[] = "$field = :$field";
            }
        }
        if (!empty($search)) {
            $sql .= " WHERE " . implode(" AND ", $search);
        }

        // Add ORDER BY clause if sorting present
        if (!empty($sort)) {
            $sql .= " ORDER BY " . $sort['column'] . " " . $sort['direction'];
        }

        // Add LIMIT clause
        if ($limit > 0) {
            $sql .= " LIMIT :limit";
        }

        // Prepare and execute the query
        try {
            //echo json_encode($sql);
            $stmt = $this->pdo_conn->prepare($sql);

            // Bind values
            foreach ($data as $field => $value) {
                if ($value !== '') {
                    $stmt->bindValue(":$field", $value);
                }
            }
            if ($limit > 0) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            }
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception($sql . ": " . $e->getMessage());
        }
    }

    /* Insert
    *
    */
    // TODO Write description
    function insert($tableName, array $data){
        try{
            $this->validateDataForTable($tableName, $data);
        }
        catch(Exception $e){
            throw new Exception($e->getMessage());
        }
        $keys = array_keys($data);
        $keys_string = implode(", ", $keys);
        $placeholder_string = implode(",", array_map(function($key){return ":$key";}, $keys));
        $sql = "INSERT INTO $tableName ($keys_string) VALUES ($placeholder_string)";

        try{
            $this->pdo_conn->prepare($sql)->execute($data);
        }
        catch(PDOException $Exception) {
            throw new Exception($sql.": ".$Exception->getMessage());
        }
    }

    /* Edit
    *
    */
    // TODO Write description
    function edit($tableName, array $data){
        try{
            $this->validateDataForTable($tableName, $data);
            if(!key_exists('id', $data)){
                throw new Exception("Id not provided: ".implode(', ', (array_keys($data))));
            }
            $d['id'] = $data['id'];
            if(empty($this->select($tableName, $d))
            ){
                throw new Exception("No such element found: "."\nData: ".mapped_implode(", ", $data, ": "));
            }
        }
        catch(Exception $e){
            throw new Exception($e->getMessage());
        }

        $sql = "UPDATE $tableName SET ";

        $edit = [];
        foreach ($data as $field => $value) {
            if($field != 'id'){
                $edit[] = "$field = :$field";
            }
        }
        $sql .= implode(", ", $edit)." WHERE id = :id";

        try{
            return $this->pdo_conn->prepare($sql)->execute($data);
        }
        catch(PDOException $e) {
            throw new Exception($sql . ": " . $e->getMessage());
        }
    }

    /* Delete
     *
     */
    // TODO Write description
    function delete($tableName, array $data){
        try{
            if(
                !key_exists('id', $data)
                || empty($this->select($tableName, $data))
            ){
                throw new Exception("No such element found: ".implode(", ", $data));
            }
        }
        catch(Exception $e){
            throw new Exception($e->getMessage());
        }

        $sql = "DELETE FROM $tableName WHERE id = :id";
        try{
            $stmt = $this->pdo_conn->prepare($sql);
            $stmt->bindParam(':id', $data['id']);
            $stmt->execute();
        }
        catch(PDOException $Exception) {
            throw new Exception($sql.": ".$Exception->getMessage());
        }
    }
}

// UNIT TEST
if(!debug_backtrace()){
    /*
    - Gegevens toevoegen
    - Gegevens verwijderen
    - Gegevens wijzigen (adv een lijst aanwezige data)
    - Sorteren
    - Selecties uitvoeren

    json payload map:
    */

    $db_loc = __DIR__ . '/../databases/toeg_inf_db.db';
    $db_name = 'toeg_inf_db';
    var_dump($db_loc);
    var_dump(file_exists($db_loc));

    $db = new CustomDatabase('sqlite:' . $db_loc, $db_name, '', '');
    $display = [
        'id' => '',
        'Released_Year' => '2000',
        'Series_Title' => ''
    ];
    $sort = ['direction' => 'ASC', 'column' => 'id'];
    $tablename = 'imdb_top1000';

    try {
        print("------------------\r\nSelect: ");
        $data = $db->select($tablename, [
            'id' => '',
            'Series_Title' => 'Inception',
            'Released_Year' => ''
        ], $sort, 10);

        if(!assert(count($data) == 1, 'Wrong amount of items returned:') or
            !assert(count($data[0]) == 3, 'Wrong amount of items returned in request: ') or
            !assert($data[0]['Series_Title'] == 'Inception', 'No Inception found with SELECT.')
        ){
            print_r($data);
        }
        else print("CLEAR \n");

        print("------------------\r\nInsert: ");
        $name = "testData_".random_int(0, 1000);
        $data = $db->select($tablename, [
            'id' => '',
            'Series_Title' => $name,
            'Released_Year' => ''
        ], $sort, 10);
        $numItems = count($data);
        $db->insert($tablename, [
            'Released_Year' => '2000',
            'Series_Title' => $name
        ]);
        $data = $db->select($tablename, [
            'id' => '',
            'Series_Title' => $name,
            'Released_Year' => '2000'
        ], $sort, 10);
        $actualNum = count($data);
        $data = $data[$numItems];
        if(!assert($actualNum == $numItems + 1, "Not inserted or inserted too many times. Previous: $numItems Actual: $actualNum") or
            !assert($data['Series_Title'] == $name and $data['Released_Year'] == '2000', 'Wrong data inserted: ')
        ){
            print('Name: '.$name."\n");
            print_r($data);
        }
        else print("CLEAR \n");

        print("------------------\r\nEdit: ");
        $randomNum = random_int(0,1000);
        $id = $data['id'];
        $data['Released_Year'] = $randomNum;
        $db->edit($tablename, $data);
        $data = $db->select($tablename, [
            'id' => $id,
            'Series_Title' => '',
            'Released_Year' => ''
        ], $sort, 10);
        if(!assert($data[0]['Released_Year']==$randomNum, 'Wrongly edited released year: ')
        ) {
            print_r($data);
        }
        $data = $db->select($tablename, [
            'id' => '',
            'Series_Title' => '',
            'Released_Year' => $randomNum
        ], $sort, 10);
        foreach ($data as $item) {
            if(!assert($item['id']==$id, 'Wrongly edited item: ')
            ) {
                print_r($data);
            }
            else print("CLEAR\n");
        }

        print("------------------\r\nDelete: ");
        $search = [
            'id' => $id,
            'Series_Title' => '',
            'Released_Year' => $randomNum
        ];
        $d0 = $db->select($tablename, $search, $sort, 10);
        print_r($d0);
        $db->delete($tablename, $search);
        $d1 = $db->select($tablename, $search, $sort, 10);
        print_r($d1);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}