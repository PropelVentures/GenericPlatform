<?php
/**
 * Created by PhpStorm.
 * User: Ryan Linehan
 * Date: 2/2/2018
 * Time: 11:39 PM
 */

class SQLHelper
{
    private $connection;
    private $dbHost;
    private $dbUsername;
    private $dbPassword;
    private $dbName;

    public function __construct()
    {
        $this->dbHost = $_SESSION["dbHost"];
        $this->dbUsername = $_SESSION["dbUsername"];
        $this->dbPassword = $_SESSION["dbPassword"];
        $this->dbName = $_SESSION["dbName"];
        $this->startConnection();
    }

    private function startConnection(){
        if(empty($this->connection)){
            $this->connection = new mysqli($this->dbHost, $this->dbUsername,$this->dbPassword,$this->dbName);
        }
        if ($this->connection->connect_errno) {
            echo "Failed to connect to MySQL: (" . $this->connection->connect_errno . ") " . $this->connection->connect_error;
        }
    }

    public function queryToDatabase($query){
        $sql = $query;
        $result = $this->connection->query($sql);
        if ($result == true) {
            // output data of each row
            if(empty($result->num_rows)){
                return [];
            }
            else{
                $results_array = array();
                $result = $this->connection->query($query);
                while ($row = $result->fetch_assoc()) {
                    $results_array[] = $row;
                }
                return $results_array;
            }
        }
        else{
            return "no results found";
        }
    }

    public function UpdateDatabase($update){
        $sql = $update;
        $result = $this->connection->query($sql);
        if($result == true){
            return 1;
        }
        else{
            return 0;
        }
    }
}