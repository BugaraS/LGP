<?php
session_start();
include 'conn.php';

// allow logged in userss
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// ========== LANGUAGE SETUP ==========
$lang = $_SESSION['lang'] ?? 'en';

$texts = [
    'en' => [
        'dashboard' => 'User Dashboard',
        'student' => 'Students',
        'logout' => 'Logout',
        'system_title' => 'Debre Berhan University Laptop Gate Pass System',
        'form_title' => 'DBU Student Registration Form',
        'add_new' => 'Add New Student',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'last_name' => 'Last Name',
        'college' => 'College',
        'department' => 'Department',
        'year' => 'Year',
        'phone' => 'Phone',
        'photo' => 'Photo',
        'laptop_type' => 'Laptop Type',
        'laptop_serial' => 'Laptop Serial',
        'qr_code' => 'QR Code',
        'submit' => 'Add New Student',
        'language' => 'Language',
        'rights' => 'All rights reserved.'
    ],
    'am' => [
        'dashboard' => 'የተጠቃሚ ዳሽቦርድ',
        'student' => 'ተማሪዎች',
        'logout' => 'መውጫ',
        'system_title' => 'የደብረ ብርሃን ዩኒቨርሲቲ የላፕቶፕ ውጪ ስርዓት',
        'form_title' => 'የተማሪ ምዝገባ ቅጽ',
        'add_new' => 'አዲስ ተማሪ ይጨምሩ',
        'first_name' => 'ስም',
        'middle_name' => 'የአባት ስም',
        'last_name' => 'የአያት ስም',
        'college' => 'ኮሌጅ',
        'department' => 'ክፍል',
        'year' => 'አመት',
        'phone' => 'ስልክ',
        'photo' => 'ፎቶ',
        'laptop_type' => 'የላፕቶፕ አይነት',
        'laptop_serial' => 'ሴሪያል ቁጥር',
        'qr_code' => 'ኪውር ኮድ',
        'submit' => 'አዲስ ተማሪ ይጨምሩ',
        'language' => 'ቋንቋ',
        'rights' => 'ሁሉም መብቶች በቅጥ ይጠበቃሉ።'
    ]
];

$t = $texts[$lang];

$message = "";

// ========== FORM SUBMISSION ==========
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname         = $_POST['fname'] ?? '';
    $mname         = $_POST['mname'] ?? '';
    $lname         = $_POST['lname'] ?? '';
    $college       = $_POST['college'] ?? '';
    $department    = $_POST['department'] ?? '';
    $year          = $_POST['year'] ?? '';
    $phone         = $_POST['phone'] ?? '';
    $laptop_type   = $_POST['laptop_type'] ?? '';
    $laptop_serial = $_POST['laptop_serial'] ?? '';
    $qr_code       = $_POST['qr_code'] ?? '';

    // Photo Upload
    $photo_name = "";
    if (!empty($_FILES["photo"]["name"])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $photo_name;
        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $message = "<p style='color:red;'>Error uploading photo.</p>";
            $photo_name = "";
        }
    }

    if ($message == '') {
        $sql = "INSERT INTO student 
            (fname, mname, lname, college, department, year, phone, photo, laptop_type, laptop_serial, qr_code)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Prepare failed: " . $conn->error);
        $stmt->bind_param(
            "sssssisssss",
            $fname, $mname, $lname, $college, $department, $year, $phone,
            $photo_name, $laptop_type, $laptop_serial, $qr_code
        );
        if ($stmt->execute()) {
            header("Location: Student_registrations.php");
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
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>DBULGP</h2>
        <a href="userdashboard.php"><i class="fas fa-tachometer-alt"></i> <?= $t['dashboard'] ?></a>
        <a href="Student_registrations.php" class="active"><i class="fas fa-user-graduate"></i> <?= $t['student'] ?></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <?= $t['logout'] ?></a>
    </div>

    <!-- Main Content -->
    <div class="main">
        <!-- Header -->
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
                <h2><?= $t['form_title'] ?></h2>
                <?php if($message): ?>
                    <div class="message"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="grid-3">
                        <div class="form-group">
                            <label><?= $t['first_name'] ?></label>
                            <input type="text" name="fname" required>
                        </div>
                        <div class="form-group">
                            <label><?= $t['middle_name'] ?></label>
                            <input type="text" name="mname" required>
                        </div>
                        <div class="form-group">
                            <label><?= $t['last_name'] ?></label>
                            <input type="text" name="lname" required>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label><?= $t['college'] ?></label>
                            <select name="college" id="college" required style="width:100%;">
                                <option value="">Select College</option>
                                <option value="computing">School of Computing</option>
                                <option value="engineering">School of Engineering</option>
                                <option value="business">College of Business and Economics</option>
                                <option value="social">College of Social Science and Humanities</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?= $t['department'] ?></label>
                            <select name="department" id="department" required style="width:100%;">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label><?= $t['year'] ?></label>
                            <input type="text" name="year" required>
                        </div>
                        <div class="form-group">
                            <label><?= $t['phone'] ?></label>
                            <input type="text" name="phone" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= $t['photo'] ?></label>
                        <input type="file" name="photo" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label><?= $t['laptop_type'] ?></label>
                            <input type="text" name="laptop_type">
                        </div>
                        <div class="form-group">
                            <label><?= $t['laptop_serial'] ?></label>
                            <input type="text" name="laptop_serial">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= $t['qr_code'] ?></label>
                        <input type="text" name="qr_code" required>
                    </div>

                    <button type="submit" class="btn-submit"><?= $t['submit'] ?></button>
                </form>
            </div>
        </div>

        <footer>&copy; <?= date('Y') ?> Debre Berhan University. <?= $t['rights'] ?></footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
    $('#college').select2({ placeholder: "Select College", allowClear: true });

    const departments = {
        computing: ['Information Systems', 'Information Technology', 'Computer Science', 'Data Science', 'Software Engineering'],
        engineering: ['Electrical Engineering', 'Mechanical Engineering', 'Civil Engineering'],
        business: ['Accounting', 'Management', 'Economics'],
        social: ['Psychology', 'Sociology', 'Political Science']
    };

    $('#college').on('change', function() {
        const college = $(this).val();
        const deptSelect = $('#department');
        deptSelect.empty().append('<option value="">Select Department</option>');
        if(departments[college]) {
            departments[college].forEach(d => deptSelect.append('<option value="'+d+'">'+d+'</option>'));
        }
    });
});
</script>

</body>
</html>
