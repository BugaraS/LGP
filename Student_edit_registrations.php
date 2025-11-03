<?php
session_start();
include 'conn.php';

// allow logged in userss
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username'];

// ===================== LANGUAGE SETUP =====================
$lang = $_SESSION['lang'] ?? 'en';

$texts = [
    'en' => [
        'dashboard' => 'Dashboard',
        'student' => 'Students',
        'logout' => 'Logout',
        'system_title' => 'Debre Berhan University Laptop Gate Pass System',
        'form_title' => 'Edit Student Registration',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'last_name' => 'Last Name',
        'college' => 'College',
        'department' => 'Department',
        'year' => 'Year',
        'phone' => 'Phone Number',
        'photo' => 'Photo',
        'current_photo' => 'Current Photo',
        'laptop_type' => 'Laptop Type',
        'laptop_serial' => 'Laptop Serial',
        'qr_code' => 'QR Code',
        'update' => 'Update',
        'cancel' => 'Cancel',
        'language' => 'Language',
        'rights' => 'All rights reserved.'
    ],
    'am' => [
        'dashboard' => 'ዳሽ_ቦርድ',
        'student' => 'ተማሪዎች',
        'logout' => 'ውጣ',
        'system_title' => 'የደብረ ብርሃን ዩኒቨርሲቲ የላፕቶፕ በር ማለፊያ ስርዓት ',
        'form_title' => 'የተማሪ መዝገብ መስተካከያ',
        'first_name' => 'ስም',
        'middle_name' => 'የአባት ስም',
        'last_name' => 'የአያት ስም',
        'college' => 'ኮሌጅ',
        'department' => 'ት/ት ክፍል',
        'year' => 'አመት',
        'phone' => 'ስልክ',
        'photo' => 'ፎቶ',
        'current_photo' => 'ያሁኑ ፎቶ',
        'laptop_type' => 'የላፕቶፕ አይነት',
        'laptop_serial' => 'የላፕቶፕ ሴሪያል',
        'qr_code' => 'QR_ኮድ',
        'update' => 'update/አስተካክል',
        'cancel' => 'ተመለስ',
        'language' => 'ቋንቋ',
        'rights' => 'መብቱ በህግ የተጠበቀ ነው።'
    ]
];
$t = $texts[$lang];

// ===================== VALIDATE STUDENT ID =====================
if (!isset($_GET['id'])) {
    die("Error: No ID provided.");
}
$id = intval($_GET['id']);

// ===================== FETCH CURRENT RECORD =====================
$result = $conn->query("SELECT * FROM student WHERE id='$id'");
if (!$result || $result->num_rows == 0) {
    die("Record not found.");
}
$row = $result->fetch_assoc();

// ===================== HANDLE UPDATE FORM =====================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname         = trim($_POST['fname']);
    $mname         = trim($_POST['mname']);
    $lname         = trim($_POST['lname']);
    $college       = trim($_POST['college']);
    $department    = trim($_POST['department']);
    $year          = trim($_POST['year']);
    $phone         = trim($_POST['phone']);
    $qr_code       = trim($_POST['qr_code']);
    $laptop_type   = trim($_POST['laptop_type']);
    $laptop_serial = trim($_POST['laptop_serial']);

    $photo_name = $row['photo']; // keep old if none uploaded

    // ===== PHOTO UPLOAD =====
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
            echo "<p style='color:red;text-align:center;'>❌ Error uploading photo. Keeping old one.</p>";
            $photo_name = $row['photo'];
        }
    }

    // ===== UPDATE QUERY =====
    $stmt = $conn->prepare("UPDATE student 
        SET fname=?, mname=?, lname=?, college=?, department=?, year=?, phone=?, laptop_type=?, laptop_serial=?, qr_code=?, photo=? 
        WHERE id=?");
    $stmt->bind_param(
        "sssssssssssi",
        $fname, $mname, $lname, $college, $department,
        $year, $phone, $laptop_type, $laptop_serial,
        $qr_code, $photo_name, $id
    );

    if ($stmt->execute()) {
        header("Location: Student_registrations.php");
        exit;
    } else {
        echo "<p style='color:red;text-align:center;'>Error updating record: " . $stmt->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['form_title'] ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="Language_Style.css">

</head>
<body>

<div class="main-container">
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>DBULGP</h2>
    <a href="userdashboard.php"><i class="fas fa-tachometer-alt"></i> <?= $t['dashboard'] ?></a>
    <a href="Student_registrations.php" class="active"><i class="fas fa-user-graduate"></i> <?= $t['student'] ?></a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <?= $t['logout'] ?></a>
  </div>

  <!-- Main -->
  <div class="main">
    <header>
      <h2><?= $t['system_title'] ?></h2>
      <div class="language-switcher">
        <span><?= $t['language'] ?>:</span>
        <a href="language_switch.php?lang=en">English</a>
        <a href="language_switch.php?lang=am">አማርኛ</a>
      </div>
    </header>

    <div class="form-container">
      <h2><?= $t['form_title'] ?></h2>

      <form method="post" enctype="multipart/form-data">
        <label><?= $t['first_name'] ?>:</label>
        <input type="text" name="fname" value="<?= htmlspecialchars($row['fname']) ?>" required>

        <label><?= $t['middle_name'] ?>:</label>
        <input type="text" name="mname" value="<?= htmlspecialchars($row['mname']) ?>">

        <label><?= $t['last_name'] ?>:</label>
        <input type="text" name="lname" value="<?= htmlspecialchars($row['lname']) ?>" required>

        <label><?= $t['college'] ?>:</label>
        <input type="text" name="college" value="<?= htmlspecialchars($row['college']) ?>" required>

        <label><?= $t['department'] ?>:</label>
        <input type="text" name="department" value="<?= htmlspecialchars($row['department']) ?>" required>

        <label><?= $t['year'] ?>:</label>
        <input type="number" name="year" value="<?= htmlspecialchars($row['year']) ?>" required>

        <label><?= $t['phone'] ?>:</label>
        <input type="tel" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" required>

        <label><?= $t['qr_code'] ?>:</label>
        <input type="text" name="qr_code" value="<?= htmlspecialchars($row['qr_code']) ?>" required>

        <label><?= $t['current_photo'] ?>:</label><br>
        <?php if (!empty($row['photo']) && file_exists("uploads/" . $row['photo'])): ?>
          <img src="uploads/<?= htmlspecialchars($row['photo']) ?>" width="100" height="100" style="border-radius:8px;margin-bottom:10px;"><br>
        <?php else: ?>
          <p>No photo uploaded</p>
        <?php endif; ?>
        <input type="file" name="photo">

        <label><?= $t['laptop_type'] ?>:</label>
        <input type="text" name="laptop_type" value="<?= htmlspecialchars($row['laptop_type']) ?>">

        <label><?= $t['laptop_serial'] ?>:</label>
        <input type="text" name="laptop_serial" value="<?= htmlspecialchars($row['laptop_serial']) ?>">

        <button type="submit" class="btn-submit">💾 <?= $t['update'] ?></button>
        <a href="Student_registrations.php" class="cancel">⬅ <?= $t['cancel'] ?></a>
      </form>
    </div>

    <footer>
      &copy; <?= date('Y') ?> DBU. <?= $t['rights'] ?>
    </footer>
  </div>
</div>

</body>
</html>
