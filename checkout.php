<?php
require_once 'api/config/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $customer_name = $_POST['full_name'] ?? '';
    $whatsapp_no   = $_POST['whatsapp_no'] ?? '';
    $suite_id      = $_POST['suite_id'] ?? 0;
    $booking_date  = $_POST['booking_date'] ?? '';
    $time_slot     = $_POST['time_slot'] ?? '';
    
    // Auto Generate Unique Booking Reference Code (e.g., NX-A1B2C)
    $booking_ref   = 'NX-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

    // Database එකෙන් තෝරාගත් Package එකේ මිල ලබා ගැනීම
    $suite_query = $db->prepare("SELECT hourly_rate FROM suites WHERE suite_id = ?");
    $suite_query->execute([$suite_id]);
    $suite = $suite_query->fetch(PDO::FETCH_ASSOC);

    // Package එකේ සම්පූර්ණ මිල ගණනය කිරීම
    $total_amount = $suite ? floatval($suite['hourly_rate']) : 0;

    // Add-ons තිබේ නම් ඒවා එකතු කිරීම
    if (isset($_POST['addons']) && is_array($_POST['addons'])) {
        foreach ($_POST['addons'] as $addon_price) {
            $total_amount += floatval($addon_price);
        }
    }

    try {
        $sql = "INSERT INTO bookings (booking_ref, suite_id, customer_name, whatsapp_no, booking_date, time_slot, total_amount, status, created_at) 
                VALUES (:booking_ref, :suite_id, :customer_name, :whatsapp_no, :booking_date, :time_slot, :total_amount, 'Pending', NOW())";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':booking_ref'   => $booking_ref,
            ':suite_id'      => $suite_id,
            ':customer_name' => $customer_name,
            ':whatsapp_no'   => $whatsapp_no,
            ':booking_date'  => $booking_date,
            ':time_slot'     => $time_slot,
            ':total_amount'  => $total_amount
        ]);

        echo "<script>
                alert('Booking Successful! Reference: " . $booking_ref . "');
                window.location.href = 'admin_dashboard.php';
              </script>";
        exit();

    } catch (PDOException $e) {
        echo "Error saving booking: " . $e->getMessage();
    }
}
?>