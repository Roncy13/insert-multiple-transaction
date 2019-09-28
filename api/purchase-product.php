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
  $values = array();

  foreach ($details as $value) {
    // i Just append it manually, so that the $query will be dynamic and will depend on the number of items
    
    $query .= "(?,?,?,?,?), ";
   
    //   ------------This how we access items in details pass in front end-------   --- HEDAER ID come from Header insert----
    //          Description   Price             Img             Category            Header ID
    $detail = array($value["description"], $value["price"], $value["img"], $value["category"], $id);
    $values = array_merge($values, $details);
  }

  $stmt = $conn->prepare($query);
  $conn->beginTransaction();
  $stmt->execute($values);
  $conn->commit();

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