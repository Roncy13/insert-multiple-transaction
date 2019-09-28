<?php

function response($data, $message = "", $statusCode = 200, $title = "") {
	   // set response code - 200 OK
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
    http_response_code($statusCode);
 	
 	$body = array(
 		"data" => $data,
 		"message" => $message,
 	);
 	if ($statusCode >= 400) {
 		$body["title"] = $title;
 	}
    // show products data in json format
    echo json_encode($body);
    exit;
}
function serverError($message = "", $data = array()) {
	response($data, $message, 500, "SERVER_ERR");
}
function unauthorizedError($data, $message = "") {
	response($data, $message,  401, "UNAUTHORIZED_ERR");
}
function validationError($data, $message = "") {
	response($data, $message,  400, "VALIDATION_ERR");
}

