<?php

namespace App\Model;

use PDO;
use PDOException;
use App\Config\Database;

class Model
{
  protected $conn;
  protected $table_name;
  protected $class_name;
  public $id;
  public $deleted_at;
  public $created_at;
  public $updated_at;

  function __construct()
  {
  }

  // function __destruct()
  // {
  //   // $this->conn->destroy();
  // }
  public function getLatest()
  {
    return $this->getData("select * from $this->table_name where deleted_at is NULL order by created_at desc");
  }
  public function orderBy($column, $order = 'asc')
  {
    return $this->getDatas("select * from $this->table_name where deleted_at is NULL order by $column $order");
  }
  public function latest()
  {
    return $this->getDatas("select * from $this->table_name where deleted_at is NULL order by created_at desc");
  }

  public function  all()
  {
    // echo ("select * from $this->table_name where deleted_at is NULL");
    // echo
    return $this->getDatas("select * from $this->table_name where deleted_at is NULL");
  }
  public function  find($id)
  {
    return $this->getData("select * from $this->table_name where id = '$id' and deleted_at is NULL");
  }

  public function  findByOptions($options)
  {
    // echo $this->table_name;
    $ops = "";
    $indexes = count($options);
    // echo($indexes);
    $i = 1;
    foreach ($options as $index => $value) {
      if ($i === $indexes) {
        $ops .= $value === NULL ? "$index is NULL " : "$index = '$value' ";
      } else {
        $ops .= $value === NULL ? "$index is NULL and " : "$index = '$value' and ";
      }
      $i += 1;
    }

    return $this->getData("select * from $this->table_name where $ops and deleted_at is NULL");
  }
  public function  getByOptions($options, $max = null)
  {
    // echo $this->table_name;
    $ops = "";
    $indexes = count($options);
    // echo($indexes);
    $i = 1;
    foreach ($options as $index => $value) {
      if ($i === $indexes) {
        $ops .= $value === NULL ? "$index is NULL " : "$index = '$value' ";
      } else {
        $ops .= $value === NULL ? "$index is NULL and " : "$index = '$value' and ";
      }
      $i += 1;
    }
    // echo $ops;
    // echo ("select * from $this->table_name where $ops and deleted_at is NULL");
    // die();
    return
      $max ?
      $this->getDatas("select * from $this->table_name where $ops and deleted_at is NULL order by created_at desc limit $max")
      : $this->getDatas("select * from $this->table_name where $ops and deleted_at is NULL order by created_at desc");
  }
  public function  count($options)
  {
    // echo $this->table_name;
    $ops = "";
    $indexes = count($options);
    // echo($indexes);
    $i = 1;
    foreach ($options as $index => $value) {
      if ($i === $indexes) {
        $ops .= "$index = '$value' ";
      } else {
        $ops .= "$index = '$value' and ";
      }
      $i += 1;
    }
    // echo $ops;
    // echo ("select * from $this->table_name where $ops and deleted_at is NULL");
    // die();
    $query =  "select count('id') from $this->table_name where $ops and deleted_at is NULL order by created_at desc";
    if ($this->conn === null) {
      $this->conn = Database::connect();
    }
    try {
      $stmt = $this->conn->query($query);
      if ($stmt) {
        return $stmt->fetch()[0];
      } else {
        http_response_code(500);
        return "Error While updating record: ";
      }
    } catch (PDOException $e) {
      http_response_code(500);
      echo "Connection failed: " . $e->getMessage();
    }
  }
  public function  getByCustomQuery($options)
  {
    // // echo $this->table_name;
    // $ops = "";
    // $indexes = count($options);
    // // echo($indexes);
    // $i = 1;
    // foreach ($options as $index => $value) {
    //   if ($i === $indexes) {
    //     $ops .= "$index = '$value' ";
    //   } else {
    //     $ops .= "$index = '$value' and ";
    //   }
    //   $i += 1;
    // }
    // echo $ops;
    // echo ("select * from $this->table_name where $options");
    // // var_dump($options);
    // die();
    return $this->getDatas("select * from $this->table_name where $options");
  }

  public function  select($options, $id)
  {
    $ops = "";
    // echo count($options);
    foreach ($options as $index => $value) {
      if ((count($options) - 1) === $index) {
        $ops .= "$value";
      } else {
        $ops .= "$value,";
      }
    }

    return $this->getData("select $ops from $this->table_name where id = '$id' and deleted_at is NULL order by created_at desc");
  }
  public function  update($options, $id)
  {

    // echo $this->table_name;
    $ops = "";
    $indexes = count($options);
    // echo($indexes);
    $i = 1;
    foreach ($options as $index => $value) {
      if (!is_null($value)) {
        $ops .= '' . $index . ' = "' . $value  . '", ';
      }
      $i += 1;
    }
    $ops .= 'updated_at = "' .  date('Y-m-d H:i')  . '"';

    // echo ("UPDATE $this->table_name set $ops where id = $id");
    return $this->execute("UPDATE $this->table_name set $ops where id = '$id'");
  }
  public function  save($options)
  {

    // echo $this->table_name;
    $ops = "";
    $indexes = count($options);
    // echo($indexes);
    $i = 1;
    foreach ($options as $index => $value) {
      if (!is_null($value)) {
        $ops .= '' . $index . ' = "' . $value  . '", ';
      }
    }
    $ops .= 'updated_at = "' .  date('Y-m-d H:i')  . '"';
    return $this->execute("UPDATE $this->table_name set $ops where id = '$this->id'");
  }

  public function  create($options)
  {
    // return 1;
    $ops = "";
    $values = "";
    $indexes = count($options);
    // echo($indexes);
    $i = 1;
    foreach ($options as $index => $value) {
      $ops .= "$index, ";
      $values .= $value === null ? "NULL,"  : '"' . $value . '", ';
    }
    $ops .= "created_at, updated_at ";
    $values .= '"' . date('Y-m-d H:i') . '", ' . '"' . date('Y-m-d H:i') . '"';


    //  var_dump("INSERT into $this->table_name ($ops) values ($values)");
    // die();
    return $this->execute("INSERT into $this->table_name ($ops) values ($values)");
    // return $this->execute("INSERT intp $this->table_name $ops values ($values)");
  }

  public function delete($id = null)
  {
    // echo("UPDATE $this->table_name set deleted_at = CURRENT_TIMESTAMP  where id = $id");
    if ($id) {
      return $this->execute("UPDATE $this->table_name set deleted_at = CURRENT_TIMESTAMP  where id = '$id'");
    } else {
      return $this->execute("UPDATE $this->table_name set deleted_at = CURRENT_TIMESTAMP  where id = '$this->id'");
    }
  }
  public function restore($id)
  {
    // echo("UPDATE $this->table_name set deleted_at = CURRENT_TIMESTAMP  where id = $id");
    return $this->execute("UPDATE $this->table_name set deleted_at = NULL  where id = '$id'");
  }

  public function  getData($query)
  {
    try {
      if ($this->conn === null) {
        $this->conn = Database::connect();
      }
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt->fetchObject($this->class_name);
      // return $stmt->fetchAll(PDO::FETCH_CLASS, $this->class_name);
      // return $this->class_name;
    } catch (PDOException $e) {
      http_response_code(500);
      echo "Connection failed: " . $e->getMessage();
    }
  }

  public function  getDatas($query)
  {

    try {
      if ($this->conn === null) {
        $this->conn = Database::connect();
      }
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_CLASS, $this->class_name);
      // return $stmt->fetchAll(PDO::FETCH_CLASS, $this->class_name);
      // return $this->class_name;
    } catch (PDOException $e) {
      http_response_code(500);
      echo "Connection failed: " . $e->getMessage();
    }
  }

  public function  execute($query)
  {
    // echo $query;
    // var_dump($this->conn);
    // die();
    if ($this->conn === null) {
      $this->conn = Database::connect();
    }
    try {
      $stmt = $this->conn->query($query);
      if ($stmt) {
        if ($this->id) {
          return $this->find($this->id);
        } else {
          return $this->find($this->conn->lastInsertId());
        }
      } else {
        http_response_code(500);
        return "Error While updating record: ";
      }
    } catch (PDOException $e) {
      http_response_code(500);
      echo "Connection failed: " . $e->getMessage();
    }
  }

  public function  uploadImage($name, $folder, $file = null)
  {
    $path = __DIR__ . '/../../public/images/' . $folder;
    // var_dump($_FILES);
    // return false;
    if (isset($_FILES[$name])) {
      $fileName = $_FILES[$name]['name'];
      $filePath = $_FILES[$name]['tmp_name'];
      $extension = pathinfo($fileName, PATHINFO_EXTENSION);
      $bytes = random_bytes(5);
      $imageCover = bin2hex($bytes) . "." . $extension;
      if (move_uploaded_file($filePath, $path . $imageCover)) {
        return $folder . $imageCover;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}
