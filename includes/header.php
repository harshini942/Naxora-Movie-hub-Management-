<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAXORA | Movie Hub & Game Zone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav>
        <a href="index.php" class="logo">NAXORA HUB</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="booking.php">Book Slot</a>
            <a href="admin_dashboard.php">Admin Panel</a>
        </div>
    </nav>