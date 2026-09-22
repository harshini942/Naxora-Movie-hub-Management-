<?php
header("Content-Type: application/json");
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();

$suite_id = isset($_GET['suite_id']) ? intval($_GET['suite_id']) : 0;
$date     = isset($_GET['date']) ? $_GET['date'] : '';

if(!$suite_id || !$date) {
    echo json_encode(["status" => "error", "message" => "Missing suite or date parameters"]);
    exit();
}

$query = "SELECT slot_id FROM bookings WHERE suite_id = :suite_id AND booking_date = :date AND booking_status != 'Cancelled'";
$stmt = $db->prepare($query);
$stmt->bindParam(":suite_id", $suite_id);
$stmt->bindParam(":date", $date);
$stmt->execute();

$booked_slots = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    "status" => "success",
    "booked_slots" => array_map('intval', $booked_slots)
]);
?>