<?php
session_start();
include 'conn.php';
// Only allow logged-in admins
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$message = '';

// ========== LANGUAGE SETUP ==========
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
        'error_upload' => 'Error uploading photo.',
        'college_placeholder' => 'Search or Select College/Directorate',
    ],
    'am' => [
        'dashboard' => 'የአስተዳዳሪ ዳሽ_ቦርድ',
        'staff' => 'ሰራተኞች',
        'logout' => 'ውጣ',
        'system_title' => 'የደብረ ብርሃን ዩኒቨርሲቲ የላፕቶፕ በር ማለፊያ ስርዓት ',
        'edit_registration' => 'የሠራተኛ መመዝገቢያ ቅጥ',
        'first_name' => 'ስም',
        'middle_name' => 'የአባት ስም',
        'last_name' => 'የአያት ስም',
        'college' => 'ኮሌጅ/ዳይሬክቶሬት',
        'position' => 'ስራ',
        'phone' => 'ስልክ',
        'qr_code' => 'QR_ኮድ',
        'photo' => 'ፎቶ',
        'laptop_type' => 'የላፕቶፕ አይነት',
        'laptop_serial' => 'የላፕቶፕ ሴሪያል',
        'update' => 'አዘምን/update',
        'cancel' => '⬅ ተመለስ/cancel',
        'rights' => 'መብቱ በህግ የተጠበቀ ነው።',
        'language' => 'ቋንቋ',
        'record_not_found' => 'መዝገብ አልተገኘም።',
        'error_upload' => 'ፎቶ መስቀል ላይ ስህተት ተከስቷል።',
        'college_placeholder' => 'ፈልግ ወይም ኮሌጅ/ዳይሬክቶሬት ምረጡ',
    ]
];

$t = $texts[$lang];

// ========== VALIDATE ID ==========
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("<p style='color:red;'>{$t['record_not_found']}</p>");
}

// ========== FETCH RECORD ==========
$stmt = $conn->prepare("SELECT * FROM staff WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("<p style='color:red;'>{$t['record_not_found']}</p>");
}
$row = $result->fetch_assoc();

// ========== HANDLE UPDATE ==========
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
    $photo_name    = $row['photo'];

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
            $message = "<p style='color:red;'>{$t['error_upload']}</p>";
            $photo_name = $row['photo'];
        }
    }

    if (empty($message)) {
        $stmt_update = $conn->prepare("UPDATE staff 
            SET fname=?, mname=?, lname=?, college=?, position=?, phone=?, laptop_type=?, laptop_serial=?, qr_code=?, photo=? 
            WHERE id=?");
        $stmt_update->bind_param(
            "ssssssssssi",
            $fname, $mname, $lname, $college, $position, $phone, $laptop_type, $laptop_serial, $qr_code, $photo_name, $id
        );
        if ($stmt_update->execute()) {
            header("Location: Admin_staff_Registrations.php");
            exit;
        } else {
            $message = "<p style='color:red;'>Error updating record: " . $stmt_update->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['edit_registration'] ?></title>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="Language_Style.css">

</head>
<body>

<div class="main-container">
    <div class="sidebar">
        <h2>DBULGP</h2>
        <a href="Admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> <?= $t['dashboard'] ?></a>
        <a href="Admin_staff_Registrations.php" class="active"><i class="fas fa-users"></i> <?= $t['staff'] ?></a>
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
                <?php if($message): ?><div class="message"><?= $message ?></div><?php endif; ?>

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
                        <select name="college" id="college" style="width:100%;" required>
                            <option value=""><?= $t['college_placeholder'] ?></option>
                            <option <?= $row['college']=='College of Agriculture & Natural Resource'?'selected':'' ?>>College of Agriculture & Natural Resource</option>
                            <option <?= $row['college']=='College of Business & Economics'?'selected':'' ?>>College of Business & Economics</option>
                            <option <?= $row['college']=='School of Computing Science'?'selected':'' ?>>School of Computing Science</option>
                            <option <?= $row['college']=='College of Education'?'selected':'' ?>>College of Education</option>
                            <option <?= $row['college']=='College of Engineering'?'selected':'' ?>>College of Engineering</option>
                            <option <?= $row['college']=='College of Freshman Studies'?'selected':'' ?>>College of Freshman Studies</option>
                            <option <?= $row['college']=='College of Computational Science'?'selected':'' ?>>College of Computational Science</option>
                            <option <?= $row['college']=='College of Social Science'?'selected':'' ?>>College of Social Science</option>
                            <option <?= $row['college']=='School of Medicine'?'selected':'' ?>>School of Medicine</option>
                            <option <?= $row['college']=='School of Nursing & Midwifery'?'selected':'' ?>>School of Nursing & Midwifery</option>
                            <option <?= $row['college']=='School of Pharmacy'?'selected':'' ?>>School of Pharmacy</option>
                            <option <?= $row['college']=='School of Public Health'?'selected':'' ?>>School of Public Health</option>
                            <option <?= $row['college']=='Registrar Directorate'?'selected':'' ?>>Registrar Directorate</option>
                            <option <?= $row['college']=='ICT Directorate'?'selected':'' ?>>ICT Directorate</option>
                            <option <?= $row['college']=='Library Directorate'?'selected':'' ?>>Library Directorate</option>
                            <option <?= $row['college']=='Finance Directorate'?'selected':'' ?>>Finance Directorate</option>
                            <option <?= $row['college']=='Research & Community Service Directorate'?'selected':'' ?>>Research & Community Service Directorate</option>
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
                        <?php if (!empty($row['photo']) && file_exists("uploads/".$row['photo'])): ?>
                            <img src="uploads/<?= htmlspecialchars($row['photo']) ?>" width="100" height="100"><br>
                        <?php else: ?>
                            <p>No photo uploaded</p>
                        <?php endif; ?>
                        <input type="file" name="photo">
                    </div>
                    <button type="submit"><?= $t['update'] ?></button>
                    <a href="Admin_staff_Registrations.php" class="cancel"><?= $t['cancel'] ?></a>
                </form>
            </div>
        </div>

        <footer>&copy; <?= date('Y') ?> Debre Birhan University. <?= $t['rights'] ?></footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
    $('#college').select2({ placeholder:"<?= $t['college_placeholder'] ?>", allowClear:true });
});
</script>
</body>
</html>
