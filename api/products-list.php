<?php
require_once("./utilities/conn.php");
require_once("./utilities/response.php");

$sqlConn = getConnection();

function retrieve() {
  try {
    $conn = getConnection(); 
    $stmt = $conn->prepare("SELECT * FROM products");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    $result = $stmt->fetchAll();
    response($result, "Products List Retrieved Successfully");
  } catch(Exception $err) {
    serverError($err->getMessage());
  }
}

retrieve();
?>