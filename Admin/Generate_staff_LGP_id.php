<?php
session_start();
include '../conn.php';

// Only admins can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// ---------- LANGUAGE HANDLING ----------
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

$labels = [
    'en' => [
        'generate_id'   => 'Generate ID',
        'dbu_lgp'       => 'DBU Staff Laptop Gate Pass ID',
        'id'            => 'ID',
        'first_name'    => 'First Name',
        'middle_name'   => 'Middle Name',
        'last_name'     => 'Last Name',
        'college'       => 'College',
        'phone'         => 'Phone Number',
        'laptop_type'   => 'Laptop Type',
        'laptop_serial' => 'Laptop Serial No.',
        'print'         => 'Print',
        'back'          => 'Back',
        'language'      => 'Language'
    ],
    'am' => [
        'generate_id'   => 'መታወቂያ ፍጠር',
        'dbu_lgp'       => 'የደብረ ብርሃን ዩኒቨርሲቲ ሰራተኛ የላፕቶፕ በር መታወቂያ',
        'id'            => 'መታወቂያ ቁጥር',
        'first_name'    => 'ስም',
        'middle_name'   => 'የአባት ስም',
        'last_name'     => 'የአያት ስም',
        'college'       => 'ኮሌጅ',
        'phone'         => 'ስልክ ቁጥር',
        'laptop_type'   => 'የላፕቶፕ አይነት',
        'laptop_serial' => 'የላፕቶፕ ሴሪያል ቁጥር',
        'print'         => 'ፕሪንት / Print',
        'back'          => 'ተመለስ / Back',
        'language'      => 'ቋንቋ'
    ]
];

$t = $labels[$lang];

// Check for a valid ID
if (!isset($_GET['id'])) {
    die("Invalid Request: Missing ID.");
}

$id = intval($_GET['id']);

// Fetch record from database
$query = $conn->query("SELECT * FROM staff WHERE id = '$id'");
if (!$query || $query->num_rows === 0) {
    die("Record not found.");
}

$employee = $query->fetch_assoc();

// QR Code library
require_once __DIR__ . '/phpqrcode-2010100721_1.1.4/phpqrcode/qrlib.php';

// QR folder setup
$qrFolder = "uploads/qrcodes/";
if (!is_dir($qrFolder)) {
    mkdir($qrFolder, 0777, true);
}

$qrFileName = $qrFolder . "qr_" . $employee['id'] . ".png";
QRcode::png($employee['qr_code'], $qrFileName, QR_ECLEVEL_L, 3);

// Staff photo path
$photo = "uploads/" . $employee['photo'];
if (empty($employee['photo']) || !file_exists($photo)) {
    $photo = "uploads/default.png";
}
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

<div class="content">
    <div class="id-card">
        <h1><?= $t['dbu_lgp'] ?></h1>

        <img src="<?= htmlspecialchars($photo); ?>" class="photo" alt="Photo">

        <table>
            <tr><td><b><?= $t['id'] ?>:</b></td><td><?= htmlspecialchars($employee['id']); ?></td></tr>
            <tr><td><b><?= $t['first_name'] ?>:</b></td><td><?= htmlspecialchars($employee['fname']); ?></td></tr>
            <tr><td><b><?= $t['middle_name'] ?>:</b></td><td><?= htmlspecialchars($employee['mname']); ?></td></tr>
            <tr><td><b><?= $t['last_name'] ?>:</b></td><td><?= htmlspecialchars($employee['lname']); ?></td></tr>
            <tr><td><b><?= $t['college'] ?>:</b></td><td><?= htmlspecialchars($employee['college']); ?></td></tr>
            <tr><td><b><?= $t['phone'] ?>:</b></td><td><?= htmlspecialchars($employee['phone']); ?></td></tr>
            <tr><td><b><?= $t['laptop_type'] ?>:</b></td><td><?= htmlspecialchars($employee['laptop_type']); ?></td></tr>
            <tr><td><b><?= $t['laptop_serial'] ?>:</b></td><td><?= htmlspecialchars($employee['laptop_serial']); ?></td></tr>
        </table>

        <div class="qr">
            <img src="<?= $qrFileName; ?>" alt="QR Code">
        </div>
    </div>

    <div class="btn-group">
        <button class="btn print-btn" onclick="window.print()">
            <i class="fa fa-print"></i> <?= $t['print'] ?>
        </button>

        <a href="Staff_Registrations.php" class="btn back-btn">
            <i class="fa fa-arrow-left"></i> <?= $t['back'] ?>
        </a>
    </div>
</div>

</body>
</html>
