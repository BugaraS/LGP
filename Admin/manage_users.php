<?php
session_start();
include '../conn.php';

// Allow only logged-in admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM user_account WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php?msg=deleted");
    exit;
}

// Fetch all users
$result = $conn->query("SELECT id, fullname, username, email, phone, position, role, created_at FROM user_account ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../style.css">
<link rel="stylesheet" href="../Manage_table_Style.css">
<script>
// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    document.querySelectorAll('.action-menu').forEach(function(menu) {
        if (!menu.contains(e.target) && !menu.previousElementSibling.contains(e.target)) {
            menu.style.display = 'none';
        }
    });
});

function toggleMenu(el) {
    const menu = el.nextElementSibling;
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}
</script>
</head>
<body>
<div class="main-container">
    <div class="sidebar">
        <h2>DBULGP</h2>
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
        <a href="Addusers.php"><i class="fas fa-user-plus"></i> Add New User</a>
        <a href="manage_users.php" class="active"><i class="fas fa-users"></i> Manage Users</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main">
        <header>
            <h2>Debre Berhan University Laptop Gate Pass</h2>
        </header>
        <center><h2>Manage Users</h2></center>
        <div class="content">

            <?php
            if (isset($_GET['msg'])) {
                if ($_GET['msg'] === 'deleted') {
                    echo "<p class='message success'>User deleted successfully!</p>";
                }
                if ($_GET['msg'] === 'updated') {
                    echo "<p class='message success'>User updated successfully!</p>";
                }
            }
            ?>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Position</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>

                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['position']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td class="action-btns">
                                <button class="action-dots" onclick="toggleMenu(this)">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="action-menu">
                                    <a href="edit_user.php?id=<?= $row['id'] ?>">Edit</a>
                                    <a href="manage_users.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9">No users found.</td></tr>
                <?php endif; ?>
            </table>
        </div>

        <footer>
            &copy; 2025 DBU. All rights reserved.
        </footer>
    </div>
</div>
</body>
</html>
