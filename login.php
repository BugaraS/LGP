<?php
session_start();
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Select user with username AND password
    $stmt = $conn->prepare("SELECT * FROM user_account WHERE username=? AND password=? LIMIT 1");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: userdashboard.php");
        }
        exit;
    } else {
        $msg = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
   <!--<link rel="stylesheet" href="style.css">-->
 <link rel="stylesheet" href="Login_style.css"> 
</head>
<body>
  <div class="main-section">
    <div class="login-container">
      <img src="login_icon.png" alt="Login Icon">
  
      <div class="login-form">
        <?php if(!empty($msg)) echo "<p class='msg'>$msg</p>"; ?>
        <h2>DBU LGP Login</h2>
        <form method="POST">
          <input type="text" id="username" name="username" placeholder="Username" required><br>
          <input type="password" id="password" name="password" placeholder="Password" required><br>
          <button type="submit">Login</button><br>
        </form>
        <!-- <p class="signup-link">Don't have an account? <a href="#">Sign Up</a></p> -->
      </div>
    </div>
  </div>

</body>
</html>
