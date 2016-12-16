<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require './mail_connection.php';
require './connection.php';

//check_send_email();

function check_send_email() {
    global $conn;
    $sql = 'Select * from Orders where email_status=0';
    $result = $conn->query($sql);
    $count = 0;
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            if (send_mail($row)) {
                $count++;
            }
        }
    }

    echo 'No of mails sent '.$count;
    $conn->close();
}

function updateTable($id) {
    global $conn;
    $sql = 'Update Orders set email_status=1 where id='.$id;
    if($conn->query($sql)=== TRUE){
        return TRUE;
    }
    
    return FALSE;
}

function send_mail($row) {
    $mail_connection = new MailConnection();

    $mail_obj = $mail_connection->get_mail_connection_obj();

    $mail_obj->From = 'OrdersApi@gmail.com';
    $mail_obj->FromName = 'OrdersApi';
    $mail_obj->addAddress($row['email']);
    $mail_obj->isHTML(true);
    $mail_obj->Subject = "Order Placed Successfully";
    $mail_obj->Body = '<p>Hi</p><p>Your Order of Rs '.  number_format($row['order_amount'],2)." has been placed Successfully!!!</p>";
    $mail_obj->AltBody = '<Hi <br>Your Order of Rs '.  number_format($row['order_amount'],2)." has been placed Successfully!!!";

    if (!$mail_obj->send()) {
        return FALSE;
    } else {
        updateTable($row['id']);
        return TRUE;
    }
}
