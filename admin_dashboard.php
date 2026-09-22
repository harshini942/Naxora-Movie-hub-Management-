<?php
require_once 'api/config/Database.php';

$database = new Database();
$db = $database->getConnection();

// Action logic for Confirm or Cancel
if (isset($_GET['action']) && isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'confirm') {
        $stmt = $db->prepare("UPDATE bookings SET status = 'Confirmed' WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
    } elseif ($action === 'cancel') {
        $stmt = $db->prepare("UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
    }
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all bookings with Suite name
$query = "SELECT b.*, s.name AS suite_name 
          FROM bookings b 
          LEFT JOIN suites s ON b.suite_id = s.suite_id 
          ORDER BY b.booking_id DESC";
$bookings = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | NAXORA HUB</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            background: #090c14;
            border-radius: 12px;
            border: 1px solid var(--border-color, #1e293b);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.92rem;
        }

        .admin-table th, 
        .admin-table td {
            padding: 16px 18px;
            border-bottom: 1px solid #1e293b;
            white-space: nowrap;
        }

        .admin-table th {
            background-color: #0d121f;
            color: var(--accent-cyan, #00f2fe);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.82rem;
        }

        .admin-table tr:hover {
            background-color: rgba(0, 242, 254, 0.03);
        }

        .ref-badge {
            font-family: monospace;
            background: rgba(0, 242, 254, 0.1);
            color: #00f2fe;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
            display: inline-block;
        }

        .badge-confirmed {
            background: rgba(0, 255, 135, 0.15);
            color: #00ff87;
            border: 1px solid #00ff87;
        }

        .badge-pending {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
            border: 1px solid #ffc107;
        }

        .badge-cancelled {
            background: rgba(255, 77, 77, 0.15);
            color: #ff4d4d;
            border: 1px solid #ff4d4d;
        }

        .btn-action {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 5px;
            transition: all 0.2s ease;
        }

        .btn-confirm {
            background: #00ff87;
            color: #05070a;
        }

        .btn-confirm:hover {
            box-shadow: 0 0 10px rgba(0, 255, 135, 0.5);
        }

        .btn-cancel {
            background: #ff4d4d;
            color: #fff;
        }

        .btn-cancel:hover {
            box-shadow: 0 0 10px rgba(255, 77, 77, 0.5);
        }
    </style>
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

    <div class="admin-container">
        <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 25px; color: #fff;">
            Admin Management Dashboard
        </h2>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Suite</th>
                        <th>Date & Slot</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $row): ?>
                            <tr>
                                <td>
                                    <span class="ref-badge"><?php echo htmlspecialchars($row['booking_ref'] ?? 'N/A'); ?></span>
                                </td>
                                <td><strong><?php echo htmlspecialchars($row['customer_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['whatsapp_no']); ?></td>
                                <td><?php echo htmlspecialchars($row['suite_name'] ?? 'Suite #' . $row['suite_id']); ?></td>
                                <td>
                                    <div><?php echo htmlspecialchars($row['booking_date']); ?></div>
                                    <small style="color: #94a3b8;"><?php echo htmlspecialchars($row['time_slot']); ?></small>
                                </td>
                                <td style="font-weight: 700; color: #00ff87;">
                                    LKR <?php echo number_format($row['total_amount'], 2); ?>
                                </td>
                                <td>
                                    <?php 
                                        $status = $row['status'] ?? 'Pending';
                                        $badge_class = 'badge-pending';
                                        if ($status === 'Confirmed') $badge_class = 'badge-confirmed';
                                        if ($status === 'Cancelled') $badge_class = 'badge-cancelled';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td>
                                    <?php if ($status !== 'Confirmed'): ?>
                                        <a href="admin_dashboard.php?action=confirm&id=<?php echo $row['booking_id']; ?>" class="btn-action btn-confirm">Confirm</a>
                                    <?php endif; ?>
                                    <?php if ($status !== 'Cancelled'): ?>
                                        <a href="admin_dashboard.php?action=cancel&id=<?php echo $row['booking_id']; ?>" class="btn-action btn-cancel" onclick="return confirm('Cancel this booking?');">Cancel</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #94a3b8; padding: 30px;">
                                No bookings found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer style="margin-top: 50px; text-align: center; color: #64748b; font-size: 0.85rem;">
        <p>&copy; <?php echo date('Y'); ?> NAXORA Entertainment Hub. All Rights Reserved.</p>
    </footer>
</body>
</html>