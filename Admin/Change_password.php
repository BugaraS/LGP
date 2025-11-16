<?php
session_start();
include '../conn.php';

// Check login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// ---------------- LANGUAGE HANDLING ----------------
$lang = $_SESSION['lang'] ?? 'en';

$texts = [
    'en' => [
        'dashboard' => 'Dashboard',
        'staff' => 'Staff',
        'students' => 'Students',
        'guest' => 'Guest',
        'change_password' => 'Change Password',
        'logout' => 'Logout',
        'system_title' => 'Debre Berhan University Laptop Gate Pass System',
        'old_password' => 'Old Password',
        'new_password' => 'New Password',
        'submit' => 'Change Password',
        'password_updated' => 'Password updated successfully!',
        'wrong_password' => 'Incorrect old password!',
        'back' => 'Back',
        'language' => 'Language',
        'rights' => 'All rights reserved.'
    ],
    'am' => [
        'dashboard' => 'ዳሽቦርድ',
        'staff' => 'ሠራተኞች',
        'students' => 'ተማሪዎች',
        'guest' => 'እንግዳ',
        'change_password' => 'የይለፍ ቃል መቀየር',
        'logout' => 'መውጫ',
        'system_title' => 'የደብረ ብርሃን ዩኒቨርሲቲ የላፕቶፕ ውጪ ስርዓት',
        'old_password' => 'የቀድሞ የይለፍ ቃል',
        'new_password' => 'አዲስ የይለፍ ቃል',
        'submit' => 'የይለፍ ቃል ይቀይሩ',
        'password_updated' => 'የይለፍ ቃል ተሻሽሏል!',
        'wrong_password' => 'የቀድሞው የይለፍ ቃል የተሳሳተ ነው!',
        'back' => 'ተመለስ',
        'language' => 'ቋንቋ',
        'rights' => 'ሁሉም መብቶች በቅጥ ይጠበቃሉ።'
    ]
];

$t = $texts[$lang];
$msg = "";

// ---------------- CHANGE PASSWORD ----------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $oldPass = $_POST['old_password'];
    $newPass = $_POST['new_password'];

    $stmt = $conn->prepare("SELECT password FROM user_account WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // validate old password
    if ($user && password_verify($oldPass, $user['password'])) {
        $newHashed = password_hash($newPass, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE user_account SET password=? WHERE username=?");
        $update->bind_param("ss", $newHashed, $username);
        $update->execute();

        $msg = $t['password_updated'];
    } else {
        $msg = $t['wrong_password'];
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['change_password'] ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../style.css">
<link rel="stylesheet" href="../Language_Style.css">
</head>

<body>
<div class="main-container">

    <div class="sidebar">
        <h2>DBULGP</h2>
        <a href="userdashboard.php"><i class="fas fa-tachometer-alt"></i> <?= $t['dashboard'] ?></a>
        <a href="change_password.php" class="active"><i class="fas fa-key"></i> <?= $t['change_password'] ?></a>
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
            <div class="change-box">
                <h2><?= $t['change_password'] ?></h2>

                <?php if ($msg !== ""): ?>
                    <p class="message"><?= $msg ?></p>
                <?php endif; ?>

                <form method="POST">
                    <label><?= $t['old_password'] ?></label>
                    <input type="password" name="old_password" required>

                    <label><?= $t['new_password'] ?></label>
                    <input type="password" name="new_password" required>

                    <button type="submit"><?= $t['submit'] ?></button>
                </form>

                <a class="back-link" href="<?= ($_SESSION['role'] == 'admin') ? 'admin_dashboard.php' : 'userdashboard.php' ?>">
                    <?= $t['back'] ?>
                </a>
            </div>
        </div>

        <footer>
            &copy; <?= date('Y') ?> DBU. <?= $t['rights'] ?>
        </footer>
    </div>

</div>
</body>
</html>
