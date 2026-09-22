<?php
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();

$merchant_id      = $_POST['merchant_id'] ?? '';
$order_id         = $_POST['order_id'] ?? '';
$payhere_amount   = $_POST['payhere_amount'] ?? '';
$payhere_currency = $_POST['payhere_currency'] ?? '';
$status_code      = $_POST['status_code'] ?? '';
$md5sig           = $_POST['md5sig'] ?? '';
$payment_id       = $_POST['payment_id'] ?? '';

$merchant_secret = "MzQ1Njc4OTAxMjM0NTY3ODkwMTIzNDU2Nzg5MDEy"; 

$local_md5sig = strtoupper(
    md5(
        $merchant_id . 
        $order_id . 
        $payhere_amount . 
        $payhere_currency . 
        $status_code . 
        strtoupper(md5($merchant_secret))
    )
);

if (($local_md5sig === $md5sig) && ($status_code == 2)) {
    // 1. Update Payment & Booking Status
    $update = $db->prepare("UPDATE bookings SET payment_status = 'Paid', booking_status = 'Confirmed' WHERE booking_ref = :order_id");
    $update->bindParam(":order_id", $order_id);
    $update->execute();

    // 2. Audit Trail Log
    $log = $db->prepare("INSERT INTO payment_logs (booking_ref, payhere_payment_id, amount, status_code, raw_response) 
                         VALUES (:ref, :pay_id, :amount, :status, :raw)");
    $raw_json = json_encode($_POST);
    $log->bindParam(":ref", $order_id);
    $log->bindParam(":pay_id", $payment_id);
    $log->bindParam(":amount", $payhere_amount);
    $log->bindParam(":status", $status_code);
    $log->bindParam(":raw", $raw_json);
    $log->execute();
}
?>