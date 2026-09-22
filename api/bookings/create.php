<?php
header("Content-Type: application/json");
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->customer_name) && !empty($data->customer_phone) && !empty($data->suite_id) && !empty($data->booking_date) && !empty($data->slot_id)) {
    
    // Check Double Booking
    $check = $db->prepare("SELECT booking_id FROM bookings WHERE suite_id = :suite_id AND booking_date = :date AND slot_id = :slot_id AND booking_status != 'Cancelled'");
    $check->bindParam(":suite_id", $data->suite_id);
    $check->bindParam(":date", $data->booking_date);
    $check->bindParam(":slot_id", $data->slot_id);
    $check->execute();

    if($check->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "This time slot is already reserved for the selected suite!"]);
        exit();
    }

    $booking_ref = "NX" . strtoupper(uniqid());
    $add_ons_json = json_encode($data->add_ons ?? []);

    $query = "INSERT INTO bookings (booking_ref, customer_name, customer_phone, suite_id, booking_date, slot_id, duration_hours, add_ons, total_amount) 
              VALUES (:ref, :name, :phone, :suite, :date, :slot, :hours, :addons, :total)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":ref", $booking_ref);
    $stmt->bindParam(":name", $data->customer_name);
    $stmt->bindParam(":phone", $data->customer_phone);
    $stmt->bindParam(":suite", $data->suite_id);
    $stmt->bindParam(":date", $data->booking_date);
    $stmt->bindParam(":slot", $data->slot_id);
    $stmt->bindParam(":hours", $data->duration_hours);
    $stmt->bindParam(":addons", $add_ons_json);
    $stmt->bindParam(":total", $data->total_amount);

    if($stmt->execute()) {
        echo json_encode([
            "status" => "success", 
            "booking_ref" => $booking_ref,
            "message" => "Booking generated successfully"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to create booking"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Incomplete form parameters"]);
}
?>