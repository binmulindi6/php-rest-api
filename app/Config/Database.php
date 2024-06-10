<?php

namespace App\Config;

use DOTNET;
use PDO;
use PDOException;

class Database
{

  private $servername = "localhost:8889";
  private $username = "root";
  private $password = "root";
  private $db = "php_rest_api";
  private $conn;

  function __construct()
  {
    try {
      $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->db", $this->username, $this->password);
      // set the PDO error mode to exception
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // $this->conn-
      // echo "Connected successfully";
    } catch (PDOException $e) {
      http_response_code(500);
      echo "Connection failed: " . $e->getMessage();
    }
  }

  public static function connect()
  {
    $db = new Database();
    // $db->conn->
    return $db->conn;
  }
}
