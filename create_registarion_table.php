<?php
// create staff table
session_start();
require 'conn.php';

$sql = "CREATE TABLE staff (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fname VARCHAR(50),
    mname VARCHAR(50),
    lname VARCHAR(50),
    college VARCHAR(50),
    position VARCHAR(50),
    phone VARCHAR(20),
    photo VARCHAR(255),
    laptop_type VARCHAR(100),
    laptop_serial VARCHAR(100),
    qr_code VARCHAR(255)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table is created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

?>
