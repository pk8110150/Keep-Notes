<?php

$mysql_host='localhost';
$mysql_user='root';
$mysql_password='';

$mysql_db='mynote_users';

# Check MYSQL connection to DB
error_reporting(E_ALL ^ E_WARNING); 
$connection=new mysqli($mysql_host,$mysql_user,$mysql_password,$mysql_db);
if ($connection->connect_error) {
    error_reporting(E_ALL);
    # Connection to DB not possible. May be DB doesnt exist. Try creating a new DB
    $connection=new mysqli($mysql_host,$mysql_user,$mysql_password);
    if ($connection->connect_error)
        die("Connection failed: " . $connection->connect_error);
    
    # MySQL connection exist. Creating DB
    $sql = "CREATE DATABASE " . $mysql_db;
    if ($connection->query($sql) === True) {
        # DB Created. Try connecting to DB directly
        $connection->close();
        $connection=new mysqli($mysql_host,$mysql_user,$mysql_password,$mysql_db);
        if ($connection->connect_error) {
            die("Error connecting to database: " . $conn->error);
        }
    } else {
        die("Error creating database: " . $conn->error);
    }
} 

# Check for tables
$sql_tables = array(
        "user_basic_data" => "ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, Name VARCHAR(30) NOT NULL, Username VARCHAR(30) NOT NULL, Password VARCHAR(100) NOT NULL, DOB DATE NOT NULL, Gender VARCHAR(10) NOT NULL",
        "user_notes" => "ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, Username VARCHAR(30), Title VARCHAR(100), Note VARCHAR(1000), Last_Edited VARCHAR(20), Created VARCHAR(20), Pin_Stat VARCHAR(10)",
        "user_todo" => "ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, Username VARCHAR(30), List_Id VARCHAR(50), Title VARCHAR(100), Last_Edited VARCHAR(20), Created VARCHAR(20), Pin_Stat VARCHAR(10)",
        "todo_contents" => "ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, List_Id VARCHAR(50), Checkbox VARCHAR(10), Content VARCHAR(500)",
);
$tables = array("user_basic_data", "user_notes", "user_todo", "todo_contents");
$no_of_tables = count($tables);
$sql = "SHOW TABLES";
if ($result = $connection->query($sql)) {
    if ($result->num_rows < $no_of_tables) {
        # Few or all tables are missing
        while($row = $result->fetch_array()) {
            if (in_array($row[0], $tables)) {
                # The table exists. Delete the entry from array
                if (($key = array_search($row[0], $tables)) !== False) {
                    unset($tables[$key]);
                }
            }
        }
        # $tables will contain table names to create
        foreach ($tables as $t_name) {
            $sql = "CREATE TABLE " . $t_name . "(" . $sql_tables[$t_name] . ")";
            if ($connection->query($sql) !== True) {
                die("Connection Failed: MySQL Error fetching the required tables.");
            }
        }
    }
}
?>