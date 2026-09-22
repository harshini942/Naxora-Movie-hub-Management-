<?php
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"));

if(empty($data->booking_ref) || empty($data->amount)) {
    echo json_encode(["status" => "error", "message" => "Invalid payment data"]);
    exit();
}

$merchant_id     = "1234567"; // Insert PayHere Merchant ID
$merchant_secret = "MzQ1Njc4OTAxMjM0NTY3ODkwMTIzNDU2Nzg5MDEy"; // Insert PayHere Merchant Secret

$amount_formatted = number_format($data->amount, 2, '.', '');
$currency         = "LKR";

$hash = strtoupper(
    md5(
        $merchant_id . 
        $data->booking_ref . 
        $amount_formatted . 
        $currency . 
        strtoupper(md5($merchant_secret))
    )
);

echo json_encode([
    "status" => "success",
    "merchant_id" => $merchant_id,
    "booking_ref" => $data->booking_ref,
    "amount" => $amount_formatted,
    "currency" => $currency,
    "hash" => $hash
]);
?>