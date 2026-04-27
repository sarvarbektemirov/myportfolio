<?php
require_once "c:/OSPanel/home/myportfolio/public/superadmin/includes/db.php";

$sql = "ALTER TABLE users ADD COLUMN IF NOT EXISTS deleted_at DATETIME DEFAULT NULL";
if ($master_link->query($sql)) {
    echo "Column deleted_at added successfully or already exists.\n";
} else {
    echo "Error adding column: " . $master_link->error . "\n";
}
?>
