<?php
include 'conn.php';

if (!isset($_GET['id'])) {
    die("No ID provided.");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM staff WHERE id=$id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("Profile not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Staff Profile</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; padding:20px; }
        .profile { width:500px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        img { width:100px; height:100px; border-radius:6px; }
        h2 { color:#003366; }
    </style>
</head>
<body>
    <div class="profile">
        <h2>Staff Profile</h2>
        <p><strong>ID:</strong> <?php echo $row['id']; ?></p>
        <p><strong>Name:</strong> <?php echo $row['fname'].' '.$row['mname'].' '.$row['lname']; ?></p>
        <p><strong>College:</strong> <?php echo $row['college']; ?></p>
        <p><strong>Position:</strong> <?php echo $row['position']; ?></p>
        <p><strong>Phone:</strong> <?php echo $row['phone']; ?></p>
        <p><strong>Laptop:</strong> <?php echo $row['laptop_type'].' - '.$row['laptop_serial']; ?></p>
        <p><strong>Photo:</strong><br>
            <?php if(!empty($row['photo']) && file_exists("uploads/".$row['photo'])): ?>
                <img src="uploads/<?php echo $row['photo']; ?>" alt="photo">
            <?php else: ?>
                No Photo
            <?php endif; ?>
        </p>
    </div>
</body>
</html>
