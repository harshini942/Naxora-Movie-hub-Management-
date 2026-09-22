<?php
header("Content-Type: application/json");
session_start();
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->email) && !empty($data->password)) {
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(":email", $data->email);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if(password_verify($data->password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            echo json_encode(["status" => "success", "message" => "Login successful", "role" => $user['role']]);
            exit();
        }
    }
    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
} else {
    echo json_encode(["status" => "error", "message" => "Please provide email and password"]);
}
?>