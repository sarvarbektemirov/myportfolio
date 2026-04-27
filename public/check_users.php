<?php
include_once("adminpanel/db.php");
$res = $link->query("SELECT id, username, email FROM users");
if ($res) {
    echo "<h3>Foydalanuvchilar ro'yxati:</h3>";
    echo "<table border='1'><tr><th>ID</th><th>Username</th><th>Email</th></tr>";
    while($row = $res->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['username']}</td><td>{$row['email']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "Xatolik: users jadvali topilmadi. Sababi: " . $link->error;
}
?>
