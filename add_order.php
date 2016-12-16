<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'connection.php';
$method = $_SERVER['REQUEST_METHOD'];


if ($method !== 'POST') {
    http_response_code(400);
    $response = array('status' => 'failure', 'message' => "$method not allowed");
    echo json_encode($response, true);
    return;
}

$request_data = validateRequestData();

// add data to db 
add_data($request_data);

function add_data($request_data) {
    $response = array();
    try {
        global $conn;
        $sql = "Insert into Orders (order_amount,store_name,product_id,email,created_date) VALUES (" . $request_data['order_amt'] . ",'" . $request_data['store_name'] . "'," . $request_data['product_id'] . ",'" . $request_data['email'] . "','" . date('Y-m-d') . "')";
        if ($conn->query($sql) === TRUE) {
            $response = array('status' => 'Success', 'message' => 'Order Inserted Successfully', 'order_id' => $conn->insert_id);
        } else {
            $response = array('status' => 'failure', 'message' => 'Order Insertion Failed');
        }
    } catch (Exception $e) {
        http_response_code($e->getCode());
        $response = array('status' => 'failure', 'message' => "Internal Server Error");
    }

    ob_start();
    $conn->close();
    echo json_encode($response);
    
    header("Content-Type: application/json");
    header('Content-Length: ' . ob_get_length());
    header("Connection: close");
    ob_end_flush();
    ob_flush();
    flush();
    sleep(10);
    if ($response['status'] === 'Success') {
        require './orders_mail.php';
        send_mail(array('id' => $response['order_id'], 'order_amount' => $request_data['order_amt'], 'email' => $request_data['email']));
    }
    return;
}

function validateRequestData() {
    $request_data = json_decode(file_get_contents('php://input'), true);
    if (empty($request_data) || is_null($request_data)) {
        http_response_code(400);
        $response = array('status' => 'failure', 'message' => 'JSON is not properly formed.');
        echo json_encode($response, true);
        return;
    }
    $response = array();
    if (!isset($request_data['store_name']) || empty($request_data['store_name']) || !check_input($request_data['store_name'], 'alphanumeric')) {
        $response = array('status' => 'failure', 'message' => 'store_name is invalid');
    } else if (!isset($request_data['order_amt']) || empty($request_data['order_amt']) || !is_numeric($request_data['order_amt'])) {
        $response = array('status' => 'failure', 'message' => 'order_amt is invalid');
    } else if (!isset($request_data['product_id']) || empty($request_data['product_id']) || !is_numeric($request_data['product_id'])) {
        $response = array('status' => 'failure', 'message' => 'product_id is invalid');
    } else if (!isset($request_data['email']) || empty($request_data['email']) || !check_input($request_data['email'], 'email')) {
        $response = array('status' => 'failure', 'message' => 'email is invalid');
    }

    if (!empty($response)) {
        http_response_code(400);
        echo json_encode($response, true);
        return;
    }

    return $request_data;
}

function check_input($value, $type = 'alphanumeric') {
    $regex = array('alphanumeric' => '^[a-z\d\-_\s]+$', 'email' => '^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$');
    if (!array_key_exists($type, $regex)) {
        return false;
    }

    $key = "/" . $regex[$type] . "/i";
    return preg_match($key, $value);
}
