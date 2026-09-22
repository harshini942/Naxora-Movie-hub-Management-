<?php 
require_once 'api/config/Database.php';

$database = new Database();
$db = $database->getConnection();

// Fetch all suites for dropdown
$suites = $db->query("SELECT * FROM suites")->fetchAll(PDO::FETCH_ASSOC);

// Check if suite_id is pre-selected from URL
$selected_suite_id = isset($_GET['suite_id']) ? intval($_GET['suite_id']) : ($suites[0]['suite_id'] ?? 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Your Experience | NAXORA HUB</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Time Slots Modern Layout */
        .time-slots-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
            margin-top: 8px;
        }

        .slot-option input[type="radio"] {
            display: none;
        }

        .slot-btn {
            display: block;
            padding: 12px 10px;
            background: #090c14;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .slot-btn:hover {
            border-color: var(--accent-cyan);
            color: #fff;
        }

        .slot-option input[type="radio"]:checked + .slot-btn {
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            color: #05070a;
            font-weight: 700;
            border-color: transparent;
            box-shadow: 0 4px 15px var(--glow-cyan);
        }

        /* Checkbox Custom Styling */
        .addon-item {
            display: flex;
            align-items: center;
            background: #090c14;
            padding: 14px 16px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .addon-item:hover {
            border-color: var(--accent-cyan);
        }

        .addon-item input {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            accent-color: var(--accent-cyan);
            cursor: pointer;
        }

        /* Summary Box */
        .summary-card {
            background: rgba(0, 242, 254, 0.03);
            border: 1px solid rgba(0, 242, 254, 0.2);
            border-radius: 14px;
            padding: 20px;
            margin-top: 25px;
            margin-bottom: 25px;
        }

        .price-display {
            font-size: 2rem;
            font-weight: 800;
            color: #00ff87;
            margin-top: 5px;
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

    <div class="container">
        <div class="form-box">
            <h2 style="font-size: 1.8rem; font-weight: 800; text-align: center; margin-bottom: 8px; background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Reserve Your Experience Slot</h2>
            <p style="text-align: center; color: var(--text-muted); font-size: 0.95rem; margin-bottom: 30px;">Select your suite, date, and preferred time slot below</p>

            <form action="checkout.php" method="POST" id="bookingForm">
                <!-- Full Name -->
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" placeholder="John Doe" required>
                </div>

                <!-- WhatsApp Number -->
                <div class="form-group">
                    <label for="whatsapp_no">WhatsApp Number</label>
                    <input type="tel" id="whatsapp_no" name="whatsapp_no" class="form-control" placeholder="07X XXX XXXX" required>
                </div>

                <!-- Select Suite -->
                <div class="form-group">
                    <label for="suite_id">Select Package / Suite</label>
                    <select id="suite_id" name="suite_id" class="form-control" required onchange="updateTotal()">
                        <?php foreach ($suites as $suite): ?>
                            <option value="<?php echo $suite['suite_id']; ?>" 
                                    data-rate="<?php echo $suite['hourly_rate']; ?>"
                                    <?php echo ($suite['suite_id'] == $selected_suite_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($suite['name']); ?> (LKR <?php echo number_format($suite['hourly_rate'], 2); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Date -->
                <div class="form-group">
                    <label for="booking_date">Date</label>
                    <input type="date" id="booking_date" name="booking_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <!-- Available Time Slots -->
                <div class="form-group">
                    <label>Select Available Time Slot</label>
                    <div class="time-slots-container">
                        <label class="slot-option">
                            <input type="radio" name="time_slot" value="10:00 AM - 12:30 PM" required>
                            <span class="slot-btn">10:00 AM - 12:30 PM</span>
                        </label>
                        <label class="slot-option">
                            <input type="radio" name="time_slot" value="01:00 PM - 03:30 PM">
                            <span class="slot-btn">01:00 PM - 03:30 PM</span>
                        </label>
                        <label class="slot-option">
                            <input type="radio" name="time_slot" value="04:00 PM - 06:30 PM">
                            <span class="slot-btn">04:00 PM - 06:30 PM</span>
                        </label>
                        <label class="slot-option">
                            <input type="radio" name="time_slot" value="07:00 PM - 09:30 PM">
                            <span class="slot-btn">07:00 PM - 09:30 PM</span>
                        </label>
                    </div>
                </div>

                <!-- Add-ons Options -->
                <div class="form-group" style="margin-top: 25px;">
                    <label>Add-ons Options</label>
                    <label class="addon-item">
                        <input type="checkbox" id="addon_decor" name="addons[]" value="2000" onchange="updateTotal()">
                        <span>Birthday / Anniversary Decor (+LKR 2,000)</span>
                    </label>
                    <label class="addon-item">
                        <input type="checkbox" id="addon_snack" name="addons[]" value="1500" onchange="updateTotal()">
                        <span>VIP Snack & Beverage Bucket (+LKR 1,500)</span>
                    </label>
                </div>

                <!-- Total Amount Summary -->
                <div class="summary-card">
                    <span style="color: var(--text-muted); font-size: 0.88rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Payable Amount</span>
                    <div class="price-display">LKR <span id="total_price_text">0.00</span></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn">Proceed to PayHere Checkout</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> NAXORA Entertainment Hub. All Rights Reserved.</p>
    </footer>

    <!-- JavaScript for Live Price Calculation -->
    <script>
        function updateTotal() {
            const suiteSelect = document.getElementById('suite_id');
            const selectedOption = suiteSelect.options[suiteSelect.selectedIndex];
            
            // Package එකේ fixed price එක ලබා ගැනීම
            let total = parseFloat(selectedOption.getAttribute('data-rate')) || 0;

            // Add-ons එකතු කිරීම
            const decor = document.getElementById('addon_decor');
            const snack = document.getElementById('addon_snack');

            if (decor && decor.checked) total += parseFloat(decor.value);
            if (snack && snack.checked) total += parseFloat(snack.value);

            // මුළු ගාන රුපියල් වලින් පෙන්වීම
            document.getElementById('total_price_text').innerText = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Page එක Load වෙද්දීම Total එක Calculate කිරීම
        document.addEventListener('DOMContentLoaded', updateTotal);
    </script>
</body>
</html>