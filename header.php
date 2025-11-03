<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<header>
    <h2>
        <?php 
        if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
            echo "Welcome Admin, " . htmlspecialchars($_SESSION['username']);
        } elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'user') {
            echo "Welcome User, " . htmlspecialchars($_SESSION['username']);
        } else {
            echo "DBU Laptop Exit System (DBULES)";
        }
        ?>
    </h2>
    <nav>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="admin_dashboard.php">🏠 Dashboard</a>
            <a href="Addusers.php">➕ Add User</a>
            <a href="ManageRegistration.php">📋 Manage Registrations</a>
            <a href="changePassword.php">🔑 Change Password</a>
            <a href="logout.php">🚪 Logout</a>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
            <a href="userdashboard.php">🏠 Dashboard</a>
            <a href="my_registrations.php">📋 My Registrations</a>
            <a href="register1.php">➕ Add New</a>
            <a href="changePassword.php">🔑 Change Password</a>
            <a href="logout.php">🚪 Logout</a>
        <?php endif; ?>
    </nav>
</header>
