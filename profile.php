<?php
include 'conn.php';

if (!isset($_GET['id'])) {
    die("No ID provided.");
}
$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM staff WHERE id=$id");

if ($result->num_rows == 0) {
    die(" Profile not found!");
}

$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile Info</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; }
        .card {
            max-width: 500px; margin: auto; background: white; padding: 20px;
            border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .card img { width: 120px; height: 120px; border-radius: 50%; margin-bottom: 15px; }
        .card h2 { margin: 10px 0; color: #003366; }
        .info { text-align: left; }
        .info p { margin: 8px 0; font-size: 16px; }
    </style>
</head>
<body>
    <div class="card" align="center">
        <?php if (!empty($row['photo']) && file_exists("uploads/".$row['photo'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" alt="Profile Photo">
        <?php else: ?>
            <img src="uploads/default.png" alt="No Photo">
        <?php endif; ?>
        <h2><?php echo htmlspecialchars($row['fname']." ".$row['mname']." ".$row['lname']); ?></h2>
        <div class="info">
            <p><b>ID:</b> <?php echo $row['id']; ?></p>
            <p><b>College:</b> <?php echo htmlspecialchars($row['college']); ?></p>
            <p><b>Position:</b> <?php echo htmlspecialchars($row['position']); ?></p>
            <p><b>Phone:</b> <?php echo htmlspecialchars($row['phone']); ?></p>
            <p><b>Laptop Type:</b> <?php echo htmlspecialchars($row['laptop_type']); ?></p>
            <p><b>Laptop Serial:</b> <?php echo htmlspecialchars($row['laptop_serial']); ?></p>
        </div>
    </div>
</body>
</html>
