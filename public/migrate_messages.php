<?php
include_once("adminpanel/db.php");

$sql = "CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    relationship VARCHAR(100),
    message_uz TEXT NOT NULL,
    message_en TEXT,
    status ENUM('pending', 'approved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($link->query($sql)) {
    echo "Table 'messages' created successfully.";
} else {
    echo "Error creating table: " . $link->error;
}
?>
