<?php
// create table for Student 
session_start();
require 'conn.php';

$sql = "CREATE TABLE student (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fname VARCHAR(50),
    mname VARCHAR(50),
    lname VARCHAR(50),
    college VARCHAR(50),
    department VARCHAR(50),
    year INT,
    phone VARCHAR(20),
    photo VARCHAR(255),
    laptop_type VARCHAR(100),
    laptop_serial VARCHAR(100),
    qr_code VARCHAR(255)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
