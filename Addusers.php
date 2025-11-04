<?php
        session_start();
        include 'conn.php';

        // Only allow logged-in admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
            header("Location: login.php");
            exit;
        }

        $username = $_SESSION['username'];
        $msg = '';

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $fullname  = $_POST['fullname'];
            $username_input = $_POST['username'];
            $password_input = password_hash($_POST['password'], PASSWORD_BCRYPT); 
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $position = $_POST['position'];
            $role_input = $_POST['role'];
            $created_at = date('Y-m-d H:i:s');

            // Use prepared statement for security
            $stmt = $conn->prepare("INSERT INTO user_account (fullname, username, password, email, phone, position, role, created_at) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $fullname, $username_input, $password_input, $email, $phone, $position, $role_input, $created_at);

            if ($stmt->execute()) {
                $msg = "User added successfully!";
            } else {
                $msg = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Users</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="main-container">
    <div class="sidebar">
        <h2>DBULGP</h2>
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
        <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="main">
        <header>
            <h2>Debre Berhan University Laptop Gate Pass</h2>
        </header>

        <div class="content">
            <div class="form-container">
                <h1>Add New User</h1>
                <?php if (!empty($msg)) echo "<p class='message ".(strpos($msg,'')!==false?'success':'error')."'>$msg</p>"; ?>

                <form method="POST">
                    <label>Full Name</label>
                    <input type="text" name="fullname" required>

                    <label>Username</label>
                    <input type="text" name="username" required>

                    <label>Password</label>
                    <input type="password" name="password" required>

                    <label>Email</label>
                    <input type="email" name="email" required>

                    <label>Phone</label>
                    <input type="text" name="phone" required>

                    <label>Position</label>
                    <input type="text" name="position" required>

                    <label>Role</label>
                    <select name="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>

                    <button type="submit">Add User</button>
                </form>
            </div>
        </div>

        <footer>
            &copy; 2025 DBU. All rights reservedddd.
        </footer>
    </div>
</div>
</body>
</html>
