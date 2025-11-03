<?php
include 'conn.php';

$result = $conn->query("SELECT id, fname, lname FROM registration ORDER BY id ASC");

if (!$result) {
    die("Error: " . $conn->error);
}

echo "<h2>All IDs in registration table</h2>";
echo "<table border='1' cellpadding='6'><tr><th>ID</th><th>First Name</th><th>Last Name</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . htmlspecialchars($row['id']) . "</td><td>" . htmlspecialchars($row['fname']) . "</td><td>" . htmlspecialchars($row['lname']) . "</td></tr>";
}

echo "</table>";
?>
