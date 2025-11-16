<?php
session_start();
include '../conn.php';

// Restrict access to administrators only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Language selection
$lang = $_SESSION['lang'] ?? 'en';

$labels = [
    'en' => [
        'dashboard'      => 'Admin Dashboard',
        'user_mgmt'      => 'User Management',
        'staff'          => 'Staff',
        'students'       => 'Students',
        'guest'          => 'Guest',
        'others'         => 'Others',
        'change_pass'    => 'Change Password',
        'logout'         => 'Logout',
        'system_title'   => 'Debre Berhan University Laptop Gate Pass',
        'total_staff'    => 'Total Staff',
        'total_students' => 'Total Students',
        'total_others'   => 'Total Others',
        'rights'         => 'All rights reserved.',
        'language'       => 'Language'
    ],

    'am' => [
        'dashboard'      => 'የአስተዳዳሪ ዳሽቦርድ',
        'user_mgmt'      => 'የተጠቃሚ አስተዳደር',
        'staff'          => 'ሰራተኞች',
        'students'       => 'ተማሪዎች',
        'guest'          => 'እንግዶች',
        'others'         => 'ሌሎች',
        'change_pass'    => 'የይለፍ ቃል ቀይር',
        'logout'         => 'ውጣ',
        'system_title'   => 'የደብረ ብርሃን ዩኒቨርሲቲ የላፕቶፕ በር ማለፊያ ስርዓት',
        'total_staff'    => 'ጠቅላላ ሰራተኞች',
        'total_students' => 'ጠቅላላ ተማሪዎች',
        'total_others'   => 'ጠቅላላ ሌሎች',
        'rights'         => 'መብቱ በህግ የተጠበቀ ነው።',
        'language'       => 'ቋንቋ'
    ]
];

$t = $labels[$lang];

// Count rows from any table
function fetchCount($conn, $table) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM `$table`");
    return $result ? $result->fetch_assoc()['total'] : 0;
}

$staff_count   = fetchCount($conn, 'staff');
$student_count = fetchCount($conn, 'student');
$other_count   = fetchCount($conn, 'others');
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['dashboard'] ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../style.css">
<link rel="stylesheet" href="../Language_Style.css">
</head>
<body>

<div class="main-container">

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>DBULGP</h2>
        <a href="Admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> <?= $t['dashboard'] ?></a>
        <a href="Staff_Registrations.php"><i class="fas fa-users"></i> <?= $t['staff'] ?></a>
        <a href="Student_registrations.php"><i class="fas fa-user-graduate"></i> <?= $t['students'] ?></a>
        <a href="Guest_register.php"><i class="fas fa-briefcase"></i> <?= $t['guest'] ?></a>
        <a href="manage_users.php"><i class="fas fa-user-plus"></i> <?= $t['user_mgmt'] ?></a>
        <a href="change_password.php"><i class="fas fa-key"></i> <?= $t['change_pass'] ?></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <?= $t['logout'] ?></a>
    </div>

    <!-- Main Content -->
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

            <div class="stats">

                <div onclick="location.href='Staff_Registrations.php'">
                    <i class="fas fa-users"></i>
                    <h3><?= $t['staff'] ?></h3>
                    <p><?= $staff_count ?></p>
                </div>

                <div onclick="location.href='Student_registrations.php'">
                    <i class="fas fa-user-graduate"></i>
                    <h3><?= $t['students'] ?></h3>
                    <p><?= $student_count ?></p>
                </div>

                <div onclick="location.href='Guest_register.php'">
                    <i class="fas fa-briefcase"></i>
                    <h3><?= $t['others'] ?></h3>
                    <p><?= $other_count ?></p>
                </div>

            </div>
        </div>

        <footer>
            &copy; 2025 DBU. <?= $t['rights'] ?>
        </footer>

    </div>

</div>

</body>
</html>
