<?php
require_once("./utilities/conn.php");
require_once("./utilities/response.php");

$sqlConn = getConnection();

function purchaseItem($orderDetails) {
  
  try {
    
    $headerDetails = insertHeader($orderDetails["header"]);
    $purchaseDetails = insertDetails($orderDetails["details"], $headerDetails["id"]);
    
    unset($headerDetails["id"]);

    $result = array(
      "header" => $headerDetails,
      "details" => $purchaseDetails
    );

    response($result, "Items purchased successfully");
  } catch(Exception $err) {
    serverError($err->getMessage());
  }
}

function insertHeader($data) {
  $conn = getConnection(); 
  $stmt = $conn->prepare("INSERT into header (customer_name) values (:customer_name)");
  $stmt->bindParam(':customer_name', $data["customer_name"]);
  $stmt->execute();

  $id = $conn->lastInsertId();

  $headerStmt = $conn->prepare("SELECT * FROM header where id = :id");
  $headerStmt->bindParam(':id', $id);
  $headerStmt->execute();
  $headerStmt->setFetchMode(PDO::FETCH_ASSOC); 
  $result = $headerStmt->fetch();

  return array("id" => $id, "header" => $result);
}

function insertDetails($details, $id) {
  // For this setup, pls refer to https://stackoverflow.com/questions/1176352/pdo-prepared-inserts-multiple-rows-in-single-query
  // Find the answer of JM4, concept there is simplified rather than this
  // I implement bulk inserts

  $conn = getConnection();
  $query = 'INSERT INTO details (`description`, `price`, `img`, `category`, `header_id`) VALUES ';
  $query_fields = array();
  $values = array();

  foreach ($details as $key => $value) {
    // i Just append it manually, so that the $query will be dynamic and will depend on the number of items
    $fields = array(
      ":{$key}_description",
      ":{$key}_price",
      ":{$key}_img",
      ":{$key}_category",
      ":{$key}_header_id"
    );

    $join_fields = implode(", ", $fields);
    $query_fields[]= "({$join_fields})";

    //   ------------This how we access items in details pass in front end-------   --- HEDAER ID come from Header insert----
    //          Description   Price             Img             Category            Header ID
    $values[] = array($value["description"], $value["price"], $value["img"], $value["category"], $id);
  }

  $appendFields = implode(', ', $query_fields);
  $stmt = $conn->prepare("{$query} {$appendFields}");
  
  foreach($values as $key => $value) {
    $stmt->bindValue(":{$key}_description", $value[0]);
    $stmt->bindValue(":{$key}_price", $value[1]);
    $stmt->bindValue(":{$key}_img", $value[2]);
    $stmt->bindValue(":{$key}_category", $value[3]);
    $stmt->bindValue(":{$key}_header_id", $value[4]);
  }

  $stmt->execute();

  return retrieveDetailsByHeader($id);
}

function retrieveDetailsByHeader($id) {
  $conn = getConnection();
  $stmt = $conn->prepare("SELECT * FROM details where header_id = :header_id");
  $stmt->bindParam(':header_id', $id);
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC); 
  return $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  purchaseItem($_POST);
} else {
  validationError(array(), "Purchase Product can only be used by VERB POST");
}
?>