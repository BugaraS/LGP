<?php
session_start();
include 'conn.php';


// Only allow logged-in admins
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username'];

// ========== LANGUAGE SETUP ==========
$lang = $_SESSION['lang'] ?? 'en';
$texts = [
    'en' => [
        'id_card' => 'Student Laptop Gate Pass ID',
        'id' => 'ID',
        'fname' => 'First Name',
        'mname' => 'Middle Name',
        'lname' => 'Last Name',
        'college' => 'College',
        'department' => 'Department',
        'phone' => 'Phone',
        'laptop_type' => 'Laptop Type',
        'laptop_serial' => 'Laptop Serial',
        'print' => 'Print',
        'back' => 'Back'
    ],
    'am' => [
        'id_card' => 'የደብረ ብርሃን ዩኒቨርሲቲ ተማሪ የላፕቶፕ በር ማለፊያ መታወቂያ',
        'id' => 'መታወቂያ ቁጥር',
        'fname' => 'ስም',
        'mname' => 'የአባት ስም',
        'lname' => 'የአያት ስም',
        'college' => 'ኮሌጅ',
        'department' => 'ት/ት ክፍል',
        'phone' => 'ስልክ',
        'laptop_type' => 'የላፕቶፕ ዓይነት',
        'laptop_serial' => 'የላፕቶፕ ሴሪያል',
        'print' => 'ፕሪንት/print',
        'back' => 'ተመለስ/back'
    ]
];

// ========== VALIDATE ID ==========
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No ID provided.");
}
$id = intval($_GET['id']);

// ========== FETCH RECORD ==========
$result = $conn->query("SELECT * FROM student WHERE id='$id'");
if (!$result || $result->num_rows == 0) {
    die("Record not found.");
}
$row = $result->fetch_assoc();

// ========== QR CODE GENERATION ==========
require_once __DIR__ . '/phpqrcode-2010100721_1.1.4/phpqrcode/qrlib.php';

$qrDir = "uploads/qrcodes/";
if (!is_dir($qrDir)) mkdir($qrDir, 0777, true);

$qrFile = $qrDir . "qr_" . $row['id'] . ".png";
QRcode::png($row['qr_code'], $qrFile, QR_ECLEVEL_L, 3);

// ========== PHOTO PATH ==========
$photoPath = !empty($row['photo']) && file_exists("uploads/" . $row['photo'])
    ? "uploads/" . $row['photo']
    : "uploads/default.png";
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($texts[$lang]['id_card']) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="Language_Style.css">
<link rel="stylesheet" href="ID_Card_Style.css">
</head>
<body>

<div class="content">
    <div class="id-card">
        <h2><?= htmlspecialchars($texts[$lang]['id_card']) ?></h2>
        <img src="<?= htmlspecialchars($photoPath) ?>" class="photo" alt="Photo">
        
        <table>
            <tr><td><b><?= $texts[$lang]['id'] ?>:</b></td><td><?= htmlspecialchars($row['id']) ?></td></tr>
            <tr><td><b><?= $texts[$lang]['fname'] ?>:</b></td><td><?= htmlspecialchars($row['fname']) ?></td></tr>
            <tr><td><b><?= $texts[$lang]['mname'] ?>:</b></td><td><?= htmlspecialchars($row['mname']) ?></td></tr>
            <tr><td><b><?= $texts[$lang]['lname'] ?>:</b></td><td><?= htmlspecialchars($row['lname']) ?></td></tr>
            <tr><td><b><?= $texts[$lang]['college'] ?>:</b></td><td><?= htmlspecialchars($row['college']) ?></td></tr>
            <tr><td><b><?= $texts[$lang]['department'] ?>:</b></td><td><?= htmlspecialchars($row['department']) ?></td></tr>
            <tr><td><b><?= $texts[$lang]['phone'] ?>:</b></td><td><?= htmlspecialchars($row['phone']) ?></td></tr>
            <tr><td><b><?= $texts[$lang]['laptop_type'] ?>:</b></td><td><?= htmlspecialchars($row['laptop_type']) ?></td></tr>
            <tr><td><b><?= $texts[$lang]['laptop_serial'] ?>:</b></td><td><?= htmlspecialchars($row['laptop_serial']) ?></td></tr>
        </table>

        <div class="qr">
            <img src="<?= $qrFile ?>" alt="QR Code">
        </div>
    </div>

    <div class="btn-group">
        <button class="btn print-btn" onclick="window.print()">
            <i class="fa fa-print"></i> <?= htmlspecialchars($texts[$lang]['print']) ?>
        </button>
        <a href="Admin_student_registrations.php" class="btn back-btn">
            <i class="fa fa-arrow-left"></i> <?= htmlspecialchars($texts[$lang]['back']) ?>
        </a>
    </div>
</div>



</body>
</html>
