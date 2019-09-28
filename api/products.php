<?php
require_once("./utilities/conn.php");
require_once("./utilities/response.php");

$sqlConn = getConnection();

function purchaseItem($orderDetails) {
  /*
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
  */
  response($orderDetails, "Items has been purchased successfully");
}

if ($_POST) {
  purchaseItem($_POST);
} else {
  validationError(array(), "Purchase Product can only be used by VERB POST");
}
?>