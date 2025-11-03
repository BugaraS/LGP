<?php
// edit_registration.php
session_start();
include 'conn.php';

// Only allow logged-in users
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

// ========== LANGUAGE SETUP ==========
$lang = $_SESSION['lang'] ?? 'en';
$texts = [
    'en' => [
        'dashboard' => 'User Dashboard',
        'staff' => 'Staff',
        'logout' => 'Logout',
        'system_title' => 'Debre Berhan University Laptop Gate Pass System',
        'edit_staff' => 'Edit Staff',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'last_name' => 'Last Name',
        'college' => 'College/Directorate',
        'position' => 'Position',
        'phone' => 'Phone Number',
        'photo' => 'Photo',
        'laptop_type' => 'Laptop Type',
        'laptop_serial' => 'Laptop Serial',
        'qr_code' => 'QR Code',
        'update_btn' => 'Update',
        'cancel' => 'Cancel',
        'upload_error' => 'Error uploading photo. Keeping old one.',
        'language' => 'Language'
    ],
    'am' => [
        'dashboard' => 'የተጠቃሚ ዳሽ_ቦርድ',
        'staff' => 'ሰራተኞች',
        'logout' => 'መውጫ',
        'system_title' => 'የደብረ ብርሃን ዩኒቨርሲቲ የላፕቶፕ በር ማለፊያ ስርዓት',
        'edit_staff' => 'የሰራተኛ መዝገብ መስተካከያ ',
        'first_name' => 'ስም',
        'middle_name' => 'የአባት ስም',
        'last_name' => 'የአያት ስም',
        'college' => 'ኮሌጅ/አስተዳደር',
        'position' => 'ስራ',
        'phone' => 'ስልክ ቁጥር',
        'photo' => 'ፎቶ',
        'laptop_type' => 'የላፕቶፕ አይነት',
        'laptop_serial' => 'የላፕቶፕ ሴሪያል',
        'qr_code' => 'QR ኮድ',
        'update_btn' => 'አስተካክል',
        'cancel' => 'ተመለስ',
        
        'upload_error' => 'ፎቶ ማስገባት አልተሳካም። የቀድሞውን እንደነበረ እንደሚቀይር ይቀይሩ።',
        'rights' => 'መብቱ በህግ የተጠበቀ ነው።',
        'language' => 'ቋንቋ'
        
    ]
];
$t = $texts[$lang];

// Validate ID
if (!isset($_GET['id'])) {
    die("Error: No ID provided.");
}
$id = intval($_GET['id']);

// Fetch current record
$result = $conn->query("SELECT * FROM staff WHERE id='$id'");
if (!$result || $result->num_rows == 0) {
    die("Record not found.");
}
$row = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname         = trim($_POST['fname']);
    $mname         = trim($_POST['mname']);
    $lname         = trim($_POST['lname']);
    $college       = trim($_POST['college']);
    $position      = trim($_POST['position']);
    $phone         = trim($_POST['phone']);
    $qr_code       = trim($_POST['qr_code']);
    $laptop_type   = trim($_POST['laptop_type']);
    $laptop_serial = trim($_POST['laptop_serial']);

    $photo_name = $row['photo']; // default keep old

    // Handle photo upload
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $photo_name;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            if (!empty($row['photo']) && file_exists($target_dir . $row['photo'])) {
                unlink($target_dir . $row['photo']);
            }
        } else {
            echo "<p style='color:red;'>{$t['upload_error']}</p>";
            $photo_name = $row['photo'];
        }
    }

    $stmt = $conn->prepare("UPDATE staff 
        SET fname=?, mname=?, lname=?, college=?, position=?, phone=?, laptop_type=?, laptop_serial=?, qr_code=?, photo=?
        WHERE id=?");
    $stmt->bind_param(
        "ssssssssssi",
        $fname, $mname, $lname, $college, $position, $phone, $laptop_type, $laptop_serial, $qr_code, $photo_name, $id
    );

    if ($stmt->execute()) {
        header("Location: Staff_registrations.php");
        exit;
    } else {
        echo "<p style='color:red;'>Error updating record: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['edit_staff'] ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="Language_Style.css">
</head>
<body>

<div class="main-container">
    <div class="sidebar">
        <h2>DBULGP</h2>
        <a href="userdashboard.php"><?= $t['dashboard'] ?></a>
        <a href="Staff_registrations.php" class="active"><?= $t['staff'] ?></a>
        <a href="logout.php"><?= $t['logout'] ?></a>
    </div>

    <div class="main">
        <header>
            <h2><?= $t['system_title'] ?></h2>
            <div class="language-switcher" style="position:absolute;top:10px;right:20px;">
                <span><?= $t['language'] ?>:</span>
                <a href="language_switch.php?lang=en">English</a> | 
                <a href="language_switch.php?lang=am">አማርኛ</a>
            </div>
        </header>

        <div class="content">
            <div class="form-container">
                <h2><?= $t['edit_staff'] ?></h2>
                <form method="post" enctype="multipart/form-data">
                    <label><?= $t['first_name'] ?>:</label>
                    <input type="text" name="fname" value="<?= htmlspecialchars($row['fname']) ?>" required>

                    <label><?= $t['middle_name'] ?>:</label>
                    <input type="text" name="mname" value="<?= htmlspecialchars($row['mname']) ?>">

                    <label><?= $t['last_name'] ?>:</label>
                    <input type="text" name="lname" value="<?= htmlspecialchars($row['lname']) ?>" required>

                    <label><?= $t['college'] ?>:</label>
                    <input type="text" name="college" value="<?= htmlspecialchars($row['college']) ?>" required>

                    <label><?= $t['position'] ?>:</label>
                    <input type="text" name="position" value="<?= htmlspecialchars($row['position']) ?>" required>

                    <label><?= $t['phone'] ?>:</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" required>

                    <label><?= $t['qr_code'] ?>:</label>
                    <input type="text" name="qr_code" value="<?= htmlspecialchars($row['qr_code']) ?>" required>

                    <label><?= $t['photo'] ?>:</label>
                    <?php if (!empty($row['photo']) && file_exists("uploads/".$row['photo'])): ?>
                        <img src="uploads/<?= htmlspecialchars($row['photo']) ?>" alt="photo" width="100" height="100"><br>
                    <?php endif; ?>
                    <input type="file" name="photo">

                    <label><?= $t['laptop_type'] ?>:</label>
                    <input type="text" name="laptop_type" value="<?= htmlspecialchars($row['laptop_type']) ?>" required>

                    <label><?= $t['laptop_serial'] ?>:</label>
                    <input type="text" name="laptop_serial" value="<?= htmlspecialchars($row['laptop_serial']) ?>" required>

                    <button type="submit"><?= $t['update_btn'] ?></button>
                    <a href="Staff_registrations.php" class="cancel"><?= $t['cancel'] ?></a>
                </form>
            </div>
        </div>

        <footer>
            &copy; <?= date('Y') ?> DBU. All rights reserved.
        </footer>
    </div>
</div>

</body>
</html>
sss