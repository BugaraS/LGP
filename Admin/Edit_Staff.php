<?php
session_start();
include '../conn.php';

// Allow only logged-in admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$message = "";

// ---------------- LANGUAGE ----------------
$lang = $_SESSION['lang'] ?? 'en';

$texts = [
    'en' => [
        'dashboard' => 'Admin Dashboard',
        'staff' => 'Staff',
        'logout' => 'Logout',
        'system_title' => 'Debre Berhan University Laptop Gate Pass System',
        'edit_registration' => 'Edit Staff',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'last_name' => 'Last Name',
        'college' => 'College/Directorate',
        'position' => 'Position',
        'phone' => 'Phone',
        'qr_code' => 'QR Code',
        'photo' => 'Photo',
        'laptop_type' => 'Laptop Type',
        'laptop_serial' => 'Laptop Serial',
        'update' => 'Update',
        'cancel' => '⬅ Cancel',
        'rights' => 'All rights reserved.',
        'language' => 'Language',
        'record_not_found' => 'Record not found.',
        'error_upload' => 'Unable to upload photo.',
        'college_placeholder' => 'Search or Select College/Directorate'
    ],

    'am' => [
        'dashboard' => 'የአስተዳዳሪ ዳሽቦርድ',
        'staff' => 'ሰራተኞች',
        'logout' => 'ውጣ',
        'system_title' => 'የደብረ ብርሃን ዩኒቨርሲቲ የላፕቶፕ በር ማለፊያ ስርዓት',
        'edit_registration' => 'የሰራተኛ መመዝገቢያ ቅጥ',
        'first_name' => 'ስም',
        'middle_name' => 'የአባት ስም',
        'last_name' => 'የአያት ስም',
        'college' => 'ኮሌጅ/ዳይሬክቶሬት',
        'position' => 'ስራ',
        'phone' => 'ስልክ',
        'qr_code' => 'QR ኮድ',
        'photo' => 'ፎቶ',
        'laptop_type' => 'የላፕቶፕ አይነት',
        'laptop_serial' => 'ሴሪያል',
        'update' => 'አዘምን',
        'cancel' => '⬅ ተመለስ',
        'rights' => 'መብቱ በህግ ይጠበቃል።',
        'language' => 'ቋንቋ',
        'record_not_found' => 'መዝገብ አልተገኘም።',
        'error_upload' => 'ፎቶ ማስገባት አልተቻለም።',
        'college_placeholder' => 'ፈልግ ወይም ይምረጡ'
    ]
];

$t = $texts[$lang];

// ---------------- VALIDATE ID ----------------
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("<p style='color:red;'>{$t['record_not_found']}</p>");
}

// ---------------- FETCH DATA ----------------
$stmt = $conn->prepare("SELECT * FROM staff WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result();

if ($data->num_rows === 0) {
    die("<p style='color:red;'>{$t['record_not_found']}</p>");
}

$row = $data->fetch_assoc();

// ---------------- UPDATE RECORD ----------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fname         = trim($_POST['fname']);
    $mname         = trim($_POST['mname']);
    $lname         = trim($_POST['lname']);
    $college       = trim($_POST['college']);
    $position      = trim($_POST['position']);
    $phone         = trim($_POST['phone']);
    $qr_code       = trim($_POST['qr_code']);
    $laptop_type   = trim($_POST['laptop_type']);
    $laptop_serial = trim($_POST['laptop_serial']);

    $photo_name = $row['photo'];

    // New photo uploaded?
    if (!empty($_FILES['photo']['name'])) {
        $folder = "uploads/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $photo_name = time() . "_" . $_FILES['photo']['name'];
        $photo_path = $folder . $photo_name;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            $message = "<p style='color:red;'>{$t['error_upload']}</p>";
        } else {
            if (!empty($row['photo']) && file_exists($folder . $row['photo'])) {
                unlink($folder . $row['photo']);
            }
        }
    }

    if (empty($message)) {
        $update = $conn->prepare("
            UPDATE staff SET 
                fname=?, mname=?, lname=?, college=?, position=?, phone=?, 
                laptop_type=?, laptop_serial=?, qr_code=?, photo=? 
            WHERE id=?"
        );

        $update->bind_param(
            "ssssssssssi",
            $fname, $mname, $lname, $college, $position, $phone,
            $laptop_type, $laptop_serial, $qr_code, $photo_name, $id
        );

        if ($update->execute()) {
            header("Location: Staff_Registrations.php");
            exit;
        }

        $message = "<p style='color:red;'>Error updating record.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['edit_registration'] ?></title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../Language_Style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<div class="main-container">

    <div class="sidebar">
        <h2>DBULGP</h2>
        <a href="Admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> <?= $t['dashboard'] ?></a>
        <a class="active" href="Admin_staff_Registrations.php"><i class="fas fa-users"></i> <?= $t['staff'] ?></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <?= $t['logout'] ?></a>
    </div>

    <div class="main">
        <header>
            <h2><?= $t['system_title'] ?></h2>

            <div class="language-switcher">
                <span><?= $t['language'] ?>:</span>
                <a href="language_switch.php?lang=en">English</a>
                <a href="language_switch.php?lang=am">አማርኛ</a>
            </div>
        </header>

        <div class="content">
            <div class="form-container">
                <h2><?= $t['edit_registration'] ?></h2>

                <?php if (!empty($message)) : ?>
                    <div class="message"><?= $message ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">

                    <div class="grid-2">
                        <div class="form-group">
                            <label><?= $t['first_name'] ?></label>
                            <input type="text" name="fname" value="<?= htmlspecialchars($row['fname']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label><?= $t['middle_name'] ?></label>
                            <input type="text" name="mname" value="<?= htmlspecialchars($row['mname']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= $t['last_name'] ?></label>
                        <input type="text" name="lname" value="<?= htmlspecialchars($row['lname']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label><?= $t['college'] ?></label>
                        <select name="college" required>
                            <option value=""><?= $t['college_placeholder'] ?></option>
                            <option <?= $row['college']=="College of Agriculture & Natural Resource"?"selected":"" ?>>College of Agriculture & Natural Resource</option>
                            <option <?= $row['college']=="College of Business & Economics"?"selected":"" ?>>College of Business & Economics</option>
                            <option <?= $row['college']=="School of Computing Science"?"selected":"" ?>>School of Computing Science</option>
                            <option <?= $row['college']=="College of Education"?"selected":"" ?>>College of Education</option>
                            <option <?= $row['college']=="College of Engineering"?"selected":"" ?>>College of Engineering</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= $t['position'] ?></label>
                        <input type="text" name="position" value="<?= htmlspecialchars($row['position']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label><?= $t['phone'] ?></label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label><?= $t['laptop_type'] ?></label>
                        <input type="text" name="laptop_type" value="<?= htmlspecialchars($row['laptop_type']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label><?= $t['laptop_serial'] ?></label>
                        <input type="text" name="laptop_serial" value="<?= htmlspecialchars($row['laptop_serial']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label><?= $t['qr_code'] ?></label>
                        <input type="text" name="qr_code" value="<?= htmlspecialchars($row['qr_code']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label><?= $t['photo'] ?></label>
                        <?php if (!empty($row['photo']) && file_exists("uploads/" . $row['photo'])) : ?>
                            <img src="uploads/<?= $row['photo'] ?>" width="110">
                        <?php else : ?>
                            <p>No photo uploaded</p>
                        <?php endif; ?>

                        <input type="file" name="photo">
                    </div>

                    <button type="submit"><?= $t['update'] ?></button>
                    <a href="Staff_Registrations.php" class="cancel"><?= $t['cancel'] ?></a>
                </form>
            </div>
        </div>

        <footer>
            &copy; <?= date('Y') ?> Debre Berhan University. <?= $t['rights'] ?>
        </footer>
    </div>
</div>
</body>
</html>
