<?php

class db_helpers
{
    private $db_host;
    private $db_name;
    private $pdo_conn;
    private $driver;

    function init_db_conn($host, $db_name, $username, $password){
        $this->db_host = $host;
        $this->db_name = $db_name;

        $this->pdo_conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $this->pdo_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->driver = $this->pdo_conn->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    function close_db_conn(){
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

    function select($tablename){
        // TODO
        // Get (number of) values, set sort order, search
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
        // DEBUG
        var_dump($sql);

        try{
            $this->pdo_conn->prepare($sql)->execute($data);
        }
        catch(PDOException $Exception) {
            throw new Exception($Exception->getMessage());
        }
    }

    function edit($tableName, array $data){
        try{
            $this->validateDataForTable($tableName, $data);
        }
        catch(Exception $e){
            throw new Exception($e->getMessage());
        }

        // TODO
    }

    function delete($tableName, array $data){
        // TODO
    }
}