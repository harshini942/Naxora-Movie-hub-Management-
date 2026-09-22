<?php 
require_once 'api/config/Database.php';

$database = new Database();
$db = $database->getConnection();

$suites = $db->query("SELECT * FROM suites")->fetchAll(PDO::FETCH_ASSOC);

// Neon LED, Cyberpunk Gaming & Private Dark Ambient Cabins සඳහා විශේෂිත HD Images 6
$neon_cyber_images = [
    1 => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=800&auto=format&fit=crop', // Neon Cyber Gaming Setup
    2 => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?q=80&w=800&auto=format&fit=crop', // Dark Ambient VIP Neon Lounge
    3 => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=800&auto=format&fit=crop', // Blue/Purple Neon Cinema Room
    4 => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=800&auto=format&fit=crop', // Pro E-Sports & Movie Suite
    5 => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?q=80&w=800&auto=format&fit=crop', // Dark Cinema Screening Room
    6 => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=800&auto=format&fit=crop'  // VIP Neon Party & Gaming Lounge
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAXORA | Private Cinema & Movie Hub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .card-img-wrapper {
            position: relative;
            overflow: hidden;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            background: #05070a;
        }
        .card-img-wrapper img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }
        .card:hover .card-img-wrapper img {
            transform: scale(1.05);
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

    <header>
        <h1>NAXORA MOVIE HUB & GAME ZONE</h1>
        <p>Exclusive Private Cinema Cabins & Pro E-Sports Gaming Suites in Kandy</p>
    </header>

    <div class="container">
        <h2 class="section-title">Our Private Experience Suites</h2>
        <div class="grid">
            <?php foreach ($suites as $suite): ?>
                <?php 
                    // 100% Neon/Cyber Ambient Image එකක් ලබා ගැනීම
                    $image_src = $neon_cyber_images[$suite['suite_id']] ?? $neon_cyber_images[1];
                ?>
                <div class="card">
                    <div class="card-img-wrapper">
                        <img src="<?php echo htmlspecialchars($image_src); ?>" 
                             alt="<?php echo htmlspecialchars($suite['name']); ?>">
                    </div>
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($suite['name']); ?></h3>
                        <p class="desc"><?php echo htmlspecialchars($suite['description']); ?></p>
                        <div class="price-tag">LKR <?php echo number_format($suite['hourly_rate'], 2); ?></div>
                        <a href="booking.php?suite_id=<?php echo $suite['suite_id']; ?>" class="btn">Reserve Slot</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> NAXORA Entertainment Hub. All Rights Reserved.</p>
    </footer>
    <script src="assets/js/app.js"></script>
</body>
</html>