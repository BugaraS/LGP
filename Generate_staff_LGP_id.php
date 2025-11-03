<?php
session_start();
include 'conn.php';

// Only allow logged-in users
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username'];

// ========== LANGUAGE SETUP ==========
$lang = $_SESSION['lang'] ?? 'en';
$texts = [
    'en' => [
        'generate_id' => 'Generate ID',
        'dbu_lgp' => 'DBU Staff Laptop Gate Pass ID',
        'id' => 'ID',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'last_name' => 'Last Name',
        'college' => 'College',
        'phone' => 'Phone',
        'laptop_type' => 'Laptop Type',
        'laptop_serial' => 'Laptop Serial',
        'print' => 'Print',
        'back' => 'Back',
        'language' => 'Language',
    ],
    'am' => [
        'generate_id' => 'መለያ',
        'dbu_lgp' => 'የደብረ ብርሃን ዩኒቨርሲቲ ሰራተኛ  የላፕቶፕ በር ማለፊያ መታወቂያ',
        'id' => 'መታወቂያ ቁጥር',
        'first_name' => 'ስም',
        'middle_name' => 'የአባት ስም',
        'last_name' => 'የአያት ስም',
        'college' => 'ኮሌጅ',
        'phone' => 'ስልክ',
        'laptop_type' => 'የላፕቶፕ አይነት',
        'laptop_serial' => 'የላፕቶፕ ሴሪያል',
        'print' => 'ፕሪንት/print',
        'back' => 'ተመለስ/back',
        'language' => 'ቋንቋ',
    ]
];
$t = $texts[$lang];

// Validate ID
if (!isset($_GET['id'])) {
    die("Error: No ID provided.");
}
$id = intval($_GET['id']);

// Fetch record
$result = $conn->query("SELECT * FROM staff WHERE id='$id'");
if (!$result || $result->num_rows == 0) {
    die("Record not found.");
}
$row = $result->fetch_assoc();

// QR code library
require_once __DIR__ . '/phpqrcode-2010100721_1.1.4/phpqrcode/qrlib.php';

// Create QR code folder if not exists
$qrDir = "uploads/qrcodes/";
if (!is_dir($qrDir)) mkdir($qrDir, 0777, true);

$qrFile = $qrDir . "qr_" . $row['id'] . ".png";
QRcode::png($row['qr_code'], $qrFile, QR_ECLEVEL_L, 3);

// Photo path
$photoPath = !empty($row['photo']) && file_exists("uploads/" . $row['photo'])
    ? "uploads/" . $row['photo']
    : "uploads/default.png";
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['generate_id'] ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="Language_Style.css">
<link rel="stylesheet" href="ID_Card_Style.css">
</head>
<body>

<div class="language-switcher">
    <span><?= $t['language'] ?>:</span>
    <a href="language_switch.php?lang=en">English</a>
    <a href="language_switch.php?lang=am">አማርኛ</a>
</div>

<div class="content">
    <div class="id-card">
        <h1><?= $t['dbu_lgp'] ?></h1>
        <img src="<?= htmlspecialchars($photoPath); ?>" class="photo" alt="photo">
        <table>
            <tr><td><b><?= $t['id'] ?>:</b></td><td><?= htmlspecialchars($row['id']); ?></td></tr>
            <tr><td><b><?= $t['first_name'] ?>:</b></td><td><?= htmlspecialchars($row['fname']); ?></td></tr>
            <tr><td><b><?= $t['middle_name'] ?>:</b></td><td><?= htmlspecialchars($row['mname']); ?></td></tr>
            <tr><td><b><?= $t['last_name'] ?>:</b></td><td><?= htmlspecialchars($row['lname']); ?></td></tr>
            <tr><td><b><?= $t['college'] ?>:</b></td><td><?= htmlspecialchars($row['college']); ?></td></tr>
            <tr><td><b><?= $t['phone'] ?>:</b></td><td><?= htmlspecialchars($row['phone']); ?></td></tr>
            <tr><td><b><?= $t['laptop_type'] ?>:</b></td><td><?= htmlspecialchars($row['laptop_type']); ?></td></tr>
            <tr><td><b><?= $t['laptop_serial'] ?>:</b></td><td><?= htmlspecialchars($row['laptop_serial']); ?></td></tr>
        </table>
        <div class="qr">
            <img src="<?= $qrFile; ?>" alt="QR Code">
        </div>
    </div>

    <div class="btn-group">
        <button class="btn print-btn" onclick="window.print()">
            <i class="fa fa-print"></i> <?= $t['print'] ?>
        </button>
        <a href="Staff_registrations.php" class="btn back-btn">
            <i class="fa fa-arrow-left"></i> <?= $t['back'] ?>
        </a>
    </div>
</div>

</body>
</html>
