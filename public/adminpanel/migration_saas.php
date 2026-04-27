<?php
include_once("db.php");

echo "<h2>Migration: Single-Database SaaS</h2>";

// 1. Create `users` table
$sql_create_users = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email_verified TINYINT(1) DEFAULT 0,
    verification_code INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($link->query($sql_create_users) === TRUE) {
    echo "Table 'users' created successfully.<br>";
} else {
    echo "Error creating table 'users': " . $link->error . "<br>";
}

// 2. Insert default Admin User if not exists
$password_hash = password_hash('admin123', PASSWORD_DEFAULT);
$sql_insert_admin = "
INSERT IGNORE INTO users (id, username, firstname, lastname, email, password_hash, email_verified)
VALUES (1, 'admin', 'Asosiy', 'Admin', 'admin@example.com', '$password_hash', 1);
";

if ($link->query($sql_insert_admin) === TRUE) {
    echo "Default admin user ensured (user_id = 1).<br>";
} else {
    echo "Error inserting admin: " . $link->error . "<br>";
}

// 3. Get all tables to alter
$res = $link->query("SHOW TABLES");
$tables = [];
while ($row = $res->fetch_array()) {
    $tables[] = $row[0];
}

// Exclude 'users' and any system tables if needed
$tables_to_alter = array_diff($tables, ['users']);

foreach ($tables_to_alter as $table) {
    // Check if user_id column already exists
    $check_col = $link->query("SHOW COLUMNS FROM `$table` LIKE 'user_id'");
    if ($check_col && $check_col->num_rows == 0) {
        $sql_alter = "ALTER TABLE `$table` ADD `user_id` INT NOT NULL DEFAULT 1";
        if ($link->query($sql_alter) === TRUE) {
            echo "Added user_id to table '$table'.<br>";
        } else {
            echo "Error altering table '$table': " . $link->error . "<br>";
        }
    } else {
        echo "Column user_id already exists in '$table'.<br>";
    }
}

echo "<h3>Migration completed successfully!</h3>";
?>

