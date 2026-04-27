<?php
/**
 * Migration Script: Shared DB -> Multi-Database SaaS
 * This script migrates all existing users and their data into separate databases.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to Master DB
$master_link = new mysqli('mysql-8.4', 'root', '', 'portfolio');
if ($master_link->connect_error) {
    die("Master DB ulanishda xato: " . $master_link->connect_error);
}

include_once("schema.php");

echo "<h1>📊 Multi-DB Migratsiya boshlandi...</h1>";

// 1. Foydalanuvchilarni olish
$users_res = $master_link->query("SELECT id, username FROM users");

if (!$users_res) {
    die("Foydalanuvchilarni olishda xato.");
}

// Jadvallar ro'yxatini dinamik olish
$tables_res = $master_link->query("SHOW TABLES");
$tables = [];
while ($trow = $tables_res->fetch_array()) {
    if ($trow[0] != 'users') $tables[] = $trow[0];
}

echo "<p>Ko'chiriladigan jadvallar: " . implode(', ', $tables) . "</p>";

while ($user = $users_res->fetch_assoc()) {
    $uid = $user['id'];
    $uname = $user['username'];
    $db_name = "portfolio_" . $uname;

    echo "<h3>🔹 Foydalanuvchi: <b>$uname</b> (ID: $uid)</h3>";

    // 2. Baza yaratish
    $master_link->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // 3. User DB ga ulanish
    $user_link = new mysqli('mysql-8.4', 'root', '', $db_name);
    
    if ($user_link->connect_error) {
        echo "<p style='color:red'>❌ Baza yaratishda/ulanishda xato: $db_name</p>";
        continue;
    }

    // 4. Jadvallarni yaratish va ko'chirish
    foreach ($tables as $table) {
        // Masterdan strukturani nusxalash (LIKE)
        $user_link->query("DROP TABLE IF EXISTS `$table` ");
        if ($master_link->query("CREATE TABLE `$db_name`.`$table` LIKE `portfolio`.`$table`")) {
            echo "✅ <b>$table</b> strukturasi nusxalandi.<br>";
            
            // Ma'lumotlarni ko'chirish
            $copy_sql = "INSERT INTO `$db_name`.`$table` SELECT * FROM `portfolio`.`$table` WHERE user_id = $uid";
            if ($master_link->query($copy_sql)) {
                echo " - Ma'lumotlar ko'chirildi.<br>";
            } else {
                echo " - <span style='color:orange'>⚠ Ma'lumot ko'chirilmadi</span>. Xato: " . $master_link->error . "<br>";
            }
        } else {
            echo "❌ <span style='color:red'>$table</span> strukturasini yaratib bo'lmadi. Xato: " . $master_link->error . "<br>";
        }
    }
    
    $user_link->close();
    echo "<hr>";
}

echo "<h2>🎉 Barcha ma'lumotlar muvaffaqiyatli ko'chirildi!</h2>";
echo "<p>Endi <b>db.php</b> faylini yangi tizimga moslab o'zgartirishimiz mumkin.</p>";
?>

