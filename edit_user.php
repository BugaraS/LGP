<?php
session_start();
include 'conn.php';
// Only allow logged-in admins
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Get user ID from query parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php");
    exit;
}

$user_id = intval($_GET['id']);

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM user_account WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: manage_users.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $fullname = $_POST['fullname'];
    $username_input = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $position = $_POST['position'];
    $role_input = $_POST['role'];
    $password_input = $_POST['password'];

    if (!empty($password_input)) {
        $password_hashed = password_hash($password_input, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE user_account SET fullname=?, username=?, email=?, phone=?, position=?, role=?, password=? WHERE id=?");
        $stmt->bind_param("sssssssi", $fullname, $username_input, $email, $phone, $position, $role_input, $password_hashed, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE user_account SET fullname=?, username=?, email=?, phone=?, position=?, role=? WHERE id=?");
        $stmt->bind_param("ssssssi", $fullname, $username_input, $email, $phone, $position, $role_input, $user_id);
    }

    if ($stmt->execute()) {
        $stmt->close();
        // Redirect after successful update
        header("Location: manage_users.php?msg=updated");
        exit;
    } else {
        $msg = "❌ Error: " . $stmt->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit User</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>DBULGP</h2>
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
        <a href="manage_users.php" class="active"><i class="fas fa-users"></i> Manage Users</a>
       
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main">
        <header>
            <h2>Debre Berhan University Laptop Gate Pass</h2>
        </header>

        <div class="form-container">
            <h1>Edit User</h1>

            <?php if (!empty($msg)) echo "<p class='message ".(strpos($msg,'✅')!==false?'success':'error')."'>$msg</p>"; ?>

            <form method="POST">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>

                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

                <label>Password <small>(leave blank to keep current)</small></label>
                <input type="password" name="password">

                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

                <label>Position</label>
                <input type="text" name="position" value="<?= htmlspecialchars($user['position']) ?>" required>

                <label>Role</label>
                <select name="role">
                    <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                    <option value="user" <?= $user['role']=='user'?'selected':'' ?>>User</option>
                </select>

                <button type="submit">Update User</button>
            </form>

            <p><a href="manage_users.php"><i class="fas fa-arrow-left"></i> Back to Manage Users</a></p>
        </div>

        <footer>
            &copy; 2025 DBU. All rights reserved.
        </footer>
    </div>
</div>
</body>
</html>
