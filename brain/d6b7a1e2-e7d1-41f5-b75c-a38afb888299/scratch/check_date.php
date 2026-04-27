<?php
include_once('public/adminpanel/db.php');
$uid = (int)($_SESSION['id'] ?? 1);
$res = $master_link->query("SELECT last_portfolio_update, NOW() as current_db_time FROM users WHERE id = $uid");
$row = $res->fetch_assoc();
echo "User ID: $uid\n";
echo "Last Update in DB: " . $row['last_portfolio_update'] . "\n";
echo "Current DB Time: " . $row['current_db_time'] . "\n";
?Branch: main
