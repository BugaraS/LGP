<?php
session_start();
include '../conn.php';

// Allow access only for logged-in admin users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $fullname = $_POST['fullname'];
    $new_username = $_POST['username'];
    $hashed_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $position = $_POST['position'];
    $role = $_POST['role'];
    $created_at = date('Y-m-d H:i:s');

    // Insert new user using prepared statement
    $query = $conn->prepare(
        "INSERT INTO user_account (fullname, username, password, email, phone, position, role, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $query->bind_param(
        "ssssssss",
        $fullname,
        $new_username,
        $hashed_password,
        $email,
        $phone,
        $position,
        $role,
        $created_at
    );

    if ($query->execute()) {
        $message = "User created successfully.";
    } else {
        $message = "Unable to save data: " . $query->error;
    }

    $query->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Users</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="main-container">

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>DBULGP</h2>
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
        <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Section -->
    <div class="main">

        <header>
            <h2>Debre Berhan University Laptop Gate Pass</h2>
        </header>

        <div class="content">
            <div class="form-container">
                <h1>Add New User</h1>

                <?php if (!empty($message)): ?>
                    <p class="message"><?php echo $message; ?></p>
                <?php endif; ?>

                <form method="POST">

                    <label>Full Name</label>
                    <input type="text" name="fullname" required>

                    <label>Username</label>
                    <input type="text" name="username" required>

                    <label>Password</label>
                    <input type="password" name="password" required>

                    <label>Email Address</label>
                    <input type="email" name="email" required>

                    <label>Phone Number</label>
                    <input type="text" name="phone" required>

                    <label>Position</label>
                    <input type="text" name="position" required>

                    <label>Role</label>
                    <select name="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>

                    <button type="submit">Save User</button>

                </form>
            </div>
        </div>

        <footer>
            &copy; 2025 DBU. All rights reserved.
        </footer>
    </div>

</div>

</body>
</html>
