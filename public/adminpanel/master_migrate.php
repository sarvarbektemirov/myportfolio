<?php
/**
 * Master Database Migration
 * Adds subscription tracking columns to the users table.
 */
include_once("db.php");

echo "Master bazani yangilash boshlandi...<br>";

// 1. Add subscription_plan column
$check_plan = $master_link->query("SHOW COLUMNS FROM users LIKE 'subscription_plan'");
if ($check_plan->num_rows == 0) {
    if ($master_link->query("ALTER TABLE users ADD COLUMN subscription_plan VARCHAR(20) DEFAULT 'free' AFTER role")) {
        echo "OK: 'subscription_plan' ustuni qo'shildi.<br>";
    } else {
        echo "XATO: 'subscription_plan' qo'shishda xato: " . $master_link->error . "<br>";
    }
} else {
    echo "INFO: 'subscription_plan' ustuni allaqachon mavjud.<br>";
}

// 2. Add subscription_expires_at column
$check_exp = $master_link->query("SHOW COLUMNS FROM users LIKE 'subscription_expires_at'");
if ($check_exp->num_rows == 0) {
    if ($master_link->query("ALTER TABLE users ADD COLUMN subscription_expires_at DATETIME NULL AFTER subscription_plan")) {
        echo "OK: 'subscription_expires_at' ustuni qo'shildi.<br>";
    } else {
        echo "XATO: 'subscription_expires_at' qo'shishda xato: " . $master_link->error . "<br>";
    }
} else {
    echo "INFO: 'subscription_expires_at' ustuni allaqachon mavjud.<br>";
}

echo "<br>Yangilash yakunlandi. <a href='login.php'>Kirish sahifasiga o'tish</a>";
?>
