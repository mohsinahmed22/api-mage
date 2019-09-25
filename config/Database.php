<?php
/**
 * Created by PhpStorm.
 * User: Mohsin
 * Date: 9/24/2019
 * Time: 11:42 AM
 */

class Database
{
    private $host = "localhost";
    private $db = "api_db";
    private $username = "root";
    private $password = "";
    private $conn;

    // Get Database Connection
    public function getConnection()
    {
        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db, $this->username, $this->password);
            $this->conn->exec("set names utf8");

        }
        catch (PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}