<?php
session_start();
include 'conn.php';
// Only allow logged-in userss
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$message = '';

// ========== LANGUAGE SETUP ==========
$lang = $_SESSION['lang'] ?? 'en';
$texts = [
    'en' => [
        'dashboard' => 'User Dashboard',
        'staff' => 'Staff',
        'logout' => 'Logout',
        'system_title' => 'Debre Berhan University Laptop Gate Pass System',
        'registration_form' => 'DBU Staff Registration Form',
        'id' => 'ID',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'last_name' => 'Last Name',
        'college' => 'College/Directorate',
        'position' => 'Position',
        'phone' => 'Phone',
        'photo' => 'Photo',
        'laptop_type' => 'Laptop Type',
        'laptop_serial' => 'Laptop Serial',
        'qr_code' => 'QR Code',
        'add_new' => 'Add New Staff',
        'upload_error' => 'Error uploading photo.',
        'language' => 'Language',
        'submit_btn' => 'Add New Staff',
        'select_college' => 'Select College/Directorate'
    ],
    'am' => [
        'dashboard' => 'የተጠቃሚ ዳሽ_ቦርድ',
        'staff' => 'ሰራተኞች',
        'logout' => 'ውጣ',
        'system_title' => 'የደብረ ብርሃን ዩኒቨርሲቲ የላፕቶፕ በር ማለፊያ ስርዓት ',
        'registration_form' => 'የደብረ ብርሃን ዩኒቨርሲቲ ሰራተኞች ምዝገባ ቅጽ',
        'id' => 'መለያ',
        'first_name' => 'ስም',
        'middle_name' => 'የአባት ስም',
        'last_name' => 'የአያት ስም',
        'college' => 'ኮሌጅ/አስተዳደር',
        'position' => 'ስራ',
        'phone' => 'ስልክ',
        'photo' => 'ፎቶ',
        'laptop_type' => 'የላፕቶፕ አይነት',
        'laptop_serial' => 'የላፕቶፕ ሴሪያል',
        'qr_code' => 'ኪውአር ኮድ/QR_code',
        'add_new' => 'አዲስ ሰራተኛ ይመዝግቡ',
        'upload_error' => 'ፎቶ ማስገባት አልተሳካም።',
        'language' => 'ቋንቋ',
        'submit_btn' => 'አዲስ ሰራተኛ ይጨምሩ',
        'rights' => 'መብቱ በህግ የተጠበቀ ነው።',
        'select_college' => 'ኮሌጅ/አስተዳደር ይምረጡ'
        
    ]
];
$t = $texts[$lang];

// ================= FORM SUBMISSION =================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id            = $_POST['id'] ?? '';
    $fname         = $_POST['fname'] ?? '';
    $mname         = $_POST['mname'] ?? '';
    $lname         = $_POST['lname'] ?? '';
    $college       = $_POST['college'] ?? '';
    $position      = $_POST['position'] ?? '';
    $phone         = $_POST['phone'] ?? '';
    $laptop_type   = $_POST['laptop_type'] ?? '';
    $laptop_serial = $_POST['laptop_serial'] ?? '';
    $qr_code       = $_POST['qr_code'] ?? '';

    // Photo upload
    $photo_name = "";
    if (!empty($_FILES["photo"]["name"])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $photo_name;
        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $message = "<p style='color:red;'>{$t['upload_error']}</p>";
            $photo_name = "";
        }
    }

    if ($message == '') {
        $sql = "INSERT INTO staff 
            (id, fname, mname, lname, college, position, phone, photo, laptop_type, laptop_serial, qr_code, username)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Prepare failed: " . $conn->error);
        $stmt->bind_param("isssssssssss", $id, $fname, $mname, $lname, $college, $position, $phone, $photo_name, $laptop_type, $laptop_serial, $qr_code, $username);
        if ($stmt->execute()) {
            header("Location: Staff_registrations.php");
            exit;
        } else {
            $message = "<p style='color:red;'>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['add_new'] ?></title>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <?= $t['logout'] ?></a>
    </div>

    <div class="main">
        <header>
            <h2><?= $t['system_title'] ?></h2>
            <div class="language-switcher" style="position:absolute;top:10px;right:20px;">
                <span><?= $t['language'] ?>:</span>
                <a href="language_switch.php?lang=en">English</a>
                <a href="language_switch.php?lang=am">አማርኛ</a>
            </div>
        </header>

        <div class="content">
            <div class="form-container">
                <h2><?= $t['registration_form'] ?></h2>
                <?php if($message): ?>
                    <div class="message"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group"><label><?= $t['id'] ?> </label><input type="text" name="id" placeholder="<?= $t['id'] ?>" required></div>
                    <div class="grid-3">
                        <div class="form-group"><label><?= $t['first_name'] ?> </label><input type="text" name="fname" placeholder="<?= $t['first_name'] ?>"  required></div>
                        <div class="form-group"><label><?= $t['middle_name'] ?> </label><input type="text" name="mname"  placeholder="<?= $t['middle_name'] ?>" required></div>
                        <div class="form-group"><label><?= $t['last_name'] ?> </label><input type="text" name="lname"  placeholder="<?= $t['last_name'] ?>"  required></div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group"><label><?= $t['position'] ?> </label><input type="text" name="position"  placeholder="<?= $t['position'] ?>" required></div>
                        <div class="form-group"><label><?= $t['phone'] ?> </label><input type="text" name="phone" placeholder="+251..." required></div>
                    </div>
                    <div class="form-group">
                        <label><?= $t['college'] ?> </label>
                        <select name="college" id="college" style="width:100%;" required>
                            <option value=""><?= $t['select_college'] ?></option>
                            <option>College of Agriculture & Natural Resource</option>
                            <option>College of Business & Economics</option>
                            <option>School of Computing Science</option>
                            <option>College of Education</option>
                            <option>College of Engineering</option>
                            <option>College of Freshman Studies</option>
                            <option>College of Computational Science</option>
                            <option>College of Social Science</option>
                            <option>School of Medicine</option>
                            <option>School of Nursing & Midwifery</option>
                            <option>School of Pharmacy</option>
                            <option>School of Public Health</option>
                            <option>Registrar Directorate</option>
                            <option>ICT Directorate</option>
                            <option>Library Directorate</option>
                            <option>Finance Directorate</option>
                            <option>Research & Community Service Directorate</option>
                        </select>
                    </div>
                    <div class="form-group"><label><?= $t['photo'] ?></label><input type="file" name="photo"></div>
                    <div class="grid-2">
                        <div class="form-group"><label><?= $t['laptop_type'] ?></label><input type="text" name="laptop_type" placeholder ="<?= $t['laptop_type'] ?>" required></div>
                        <div class="form-group"><label><?= $t['laptop_serial'] ?></label><input type="text" name="laptop_serial" placeholder ="<?= $t['laptop_serial'] ?>" required></div>
                    </div>
                    <div class="form-group"><label><?= $t['qr_code'] ?> </label><input type="text" name="qr_code" required></div>
                    <button type="submit" class="btn-submit"><?= $t['submit_btn'] ?></button>
                </form>
            </div>
        </div>

        <footer>
            &copy; 2025 DBU. <?= $t['rights'] ?>
        </footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
    $('#college').select2({ placeholder:"<?= $t['select_college'] ?>", allowClear:true });
});
</script>

</body>
</html>
