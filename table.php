<?php
class Table {
    private $name;
    private $columns;
    private $primary;
    private $query;

    function __construct($name, $columns, $primary_key) {
        $this->name = $name;
        $this->columns = $columns;
        $this->primary = $primary_key;
    }

    function addColumn($values) {
        if (count($values) != count($this->columns)) {
            return 1;
        }
        
        $this->query = 'INSERT INTO ' . $this->name;
        $this->query = $this->query . ' (' . $this->columns[0];

        for($i = 1; $i < count($this->columns); $i++) {
            $this->query = $this->query . ", " . $this->columns[$i];
        }

        $this->query .= ")";
        $this->query = $this->query . " VALUES ('" . $values[0]. "'";

        for($i = 1; $i < count($values); $i++) {
            $this->query = $this->query . ", '" . $values[$i] . "'";
        }

        $this->query = $this->query . ");";
    }

    function fetchColumns ($primary_value) {
        if ($primary_value == "*") {
            $this->query =  "SELECT * FROM " . $this->name . ";";
        } else {
            $this->query = "SELECT * FROM " . $this->name . " WHERE " . $this->primary . " = '" . $primary_value . "';";
        }
    }

    function updateColumn($primary_value, $columns, $values) {
        if (count($values) != count($columns)) {
            return 1;
        }
        $this->query = '';
        $this->query = $this->query . "UPDATE " . $this->name . " SET " . $columns[0] . "='" . $values[0] . "'";
        for ($i = 1; $i < count($columns); $i++) {
            $this->query = $this->query . ","  . $columns[$i] . "='" . $values[$i] . "'";
        }

        $this->query = $this->query . " WHERE " . $this->primary . "= '" . $primary_value . "';";
        return $this->query;
    }

    function deleteColumn($primary_value) {
        return "DELETE FROM " . $this->name . " WHERE " . $this->primary . " = '" . $primary_value . "';";
    }

    function run_query ($host, $username, $dbpass, $dbname) {
    
        $conn = new mysqli($host, $username, $dbpass, $dbname);
        if ($conn->connect_error) {
            die("Could not connect to the database");
        }
    
        $result = $conn->query($this->query);
    
        if($result === TRUE) {
            echo "added successfully <br>";
        } else {
            if (substr($conn->error,0,15) == "Duplicate entry"){
                $_SESSION['barksncanine_login_error'] = "User Already Exists.";
                header('Location:login.php');
            } else {
                if ($conn->error){
                    header("Location:error.php");
                }
            }
        }
    
        $conn->close();
    
        return $result;
    }
}
?>