<?php
// TODO document this with doxygen

class customDatabase
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
            echo "Connection failed: " . $e->getMessage()."\r\n";
        }
    }

    function __destruct(){
        $this->pdo_conn = null;
    }

    function getColumnMeta($tableName){
        $columns = [];

        switch($this->driver){
            case "sqlite":
                $s = $this->pdo_conn->query("PRAGMA table_info('$tableName')");
                $cols = $s->fetchAll();
                foreach($cols as $col){
                    $columns[$col['name']] = [
                        'type' => $col['type'],
                        'isRequired' => $col['notnull']
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
        foreach($cols as $col){
            if($col['required']){
                $reqCols[] = $col['name'];
            }
        }
        return $reqCols;
    }

    function validateDataForTable($tableName, $data){
        $keys = array_keys($data);
        $reqCols = $this->getRequiredFields($tableName);
        $meta = $this->getColumnMeta($tableName);

        // Check if all required fields have data
        if(!array_diff($reqCols, $keys)) {
            throw new Exception("Required column(s) missing: ".implode(', ', array_diff($reqCols, $keys)));
        }
        foreach($reqCols as $reqCol){
            if($data[$reqCol] == null || $data[$reqCol] == ''){
                throw new Exception("Required column empty: $reqCol");
            }
        }
        foreach($keys as $key){
            $value = $data[$key];
            $type = $meta[$key]['type'];
            switch($type){
                case "integer":
                case "int":
                    if(!is_int($value + 0)){
                        throw new Exception("Invalid type: $key should be $type.");
                    }
                    break;
                case "string":
                    break;
                case "double":
                case "float":
                    if(!is_numeric($value)){
                        throw new Exception("Invalid type: $key should be $type.");
                    }
                    break;
                default:
                    echo "Unknown type: $type";
            }
        }
    }


    function select($tablename, array $data, array $sort, $limit){

            // Validate sort column and direction
            $columns = $this->getColumnMeta($tablename);
            $columnNames = array_keys($columns);
            if (!in_array($sort['column'], $columnNames)) {
                throw new Exception("Invalid sort field: " . $sort['column']);
            }
            if ($sort['direction'] !== 'ASC' && $sort['direction'] !== 'DESC') {
                throw new Exception("Invalid sort direction: " . $sort['direction']);
            }

            // Validate that all requested fields exist in the table
            $fields = array_keys($data);
            if ($missingFields = array_diff($fields, $columnNames)) {
                throw new Exception("Field not in table: " . implode(', ', $missingFields));
            }

            // Build the SQL query
            $sql = "SELECT " . implode(', ', $fields) . " FROM $tablename";

            // Add WHERE clause if there are search criteria
            $search = [];
            foreach ($data as $field => $value) {
                if ($value !== '') {
                    $search[] = "$field = :$field";
                }
            }
            if (!empty($search)) {
                $sql .= " WHERE " . implode(" AND ", $search);
            }

            // Add ORDER BY clause
            $sql .= " ORDER BY " . $sort['column'] . " " . $sort['direction'];

            // Add LIMIT clause
            if ($limit > 0) {
                $sql .= " LIMIT :limit";
            }

            // Prepare and execute the query
            try {
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



        /*
        // Get (selection of) values, set sort order, search
        $fields = array_keys($data);
        $columns = $this->getColumnMeta($tablename);
        $columnNames = array_keys($columns);
        if(!in_array($sort['column'], $columnNames)) {
            throw new Exception("Invalid sort field: ".$sort['column']."\r\n");
        }
        if($sort['direction'] != 'ASC' && $sort['direction'] != 'DESC') {
            throw new Exception("Invalid sort direction: ".$sort['direction']);
        }
        if(array_diff($fields, $columnNames)) {
            throw new Exception("Field not in table: ".implode(', ', array_diff($fields, $columnNames)));
        }

        $sql = "SELECT ".implode(', ', $fields)." from $tablename";

        $search = [];
        foreach ($data as $field => $value) {
            if($value != ''){
                $search[] = "$field = :$field";
            }
        }
        if(!empty($search)){
            $sql .= " WHERE ".implode(" AND ", $search);
        }

        $sql .= " ORDER BY ".$sort['column']." ".$sort['direction'];
        if($limit > 0){
            $sql .= " LIMIT $limit";
        }
        try{
            return $this->pdo_conn->prepare($sql)->execute($data)->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e){
            throw new Exception($sql.": ".$e->getMessage());
        }
        */
    }

    function insert($tableName, array $data){
        try{
            $this->validateDataForTable($tableName, $data);
        }
        catch(Exception $e){
            throw new Exception($e->getMessage());
        }

        $keys_string = implode(", ", array_keys($data));
        $placeholder_string = implode(", :", array_map(function($key){return ":$key";}, $keys));
        $sql = "INSERT INTO $tableName ($keys_string) VALUES ($placeholder_string)";

        try{
            $this->pdo_conn->prepare($sql)->execute($data);
        }
        catch(PDOException $Exception) {
            throw new Exception($sql.": ".$Exception->getMessage());
        }
    }

    function edit($tableName, array $data){
        try{
            $this->validateDataForTable($tableName, $data);
            if(
                !key_exists('id', $data)
                || empty($this->select($tableName, $data, ['direction' => 'ASC', 'column' => 'id']))
            ){
                throw new Exception("No such element found.");
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
        $sql .= implode(", ", $edit)."WHERE id = :id";

        try{
            return $this->pdo_conn->prepare($sql)->execute($data)->fetchAll;
        }
        catch(PDOException $e) {
            throw new Exception($sql . ": " . $e->getMessage());
        }


    }

    function delete($tableName, array $data){
        try{
            $this->validateDataForTable($tableName, $data);
            if(
                !key_exists('id', $data)
                || empty($this->select($tableName, $data, ['direction' => 'ASC', 'column' => 'id']))
            ){
                throw new Exception("No such element found.");
            }
        }
        catch(Exception $e){
            throw new Exception($e->getMessage());
        }

        $sql = "DELETE FROM $tableName WHERE id = :id";
        try{
            $this->pdo_conn->prepare($sql)->execute($data);
        }
        catch(PDOException $Exception) {
            throw new Exception($sql.": ".$Exception->getMessage());
        }
    }
}