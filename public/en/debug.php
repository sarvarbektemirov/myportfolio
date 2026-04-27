<?php
include_once("init.php");

echo "<h2>🔍 DEBUG MA'LUMOTLARI</h2>";
echo "<hr>";

// 1. Session
echo "<h3>📌 Session</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// 2. $link holati
echo "<h3>🔗 \$link holati</h3>";
if (isset($link) && $link instanceof mysqli) {
    $db_res = $link->query("SELECT DATABASE() as db");
    $db_row = $db_res->fetch_assoc();
    echo "<p style='color:green'>✅ \$link ulangan: <strong>" . $db_row['db'] . "</strong></p>";
} else {
    echo "<p style='color:red'>❌ \$link = NULL (ulanish yo'q)</p>";
}

// 3. portfolio_user_id
echo "<h3>👤 portfolio_user_id</h3>";
echo "<p><strong>\$portfolio_user_id = " . ($portfolio_user_id ?? 'yo\'q') . "</strong></p>";

// 4. users jadvalidan
echo "<h3>🗃️ Master DB - users jadvali</h3>";
if (isset($master_link) && $master_link instanceof mysqli) {
    $ures = $master_link->query("SELECT id, username FROM users");
    if ($ures && $ures->num_rows > 0) {
        echo "<table border='1' cellpadding='5'><tr><th>id</th><th>username</th></tr>";
        while ($u = $ures->fetch_assoc()) {
            echo "<tr><td>{$u['id']}</td><td>{$u['username']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red'>❌ users jadvalida hech kim yo'q!</p>";
    }
} else {
    echo "<p style='color:red'>❌ master_link yo'q</p>";
}

// 5. talim jadvali
echo "<h3>📚 talim jadvali (barcha yozuvlar)</h3>";
if (isset($link) && $link instanceof mysqli) {
    $tres = $link->query("SELECT * FROM talim");
    if ($tres && $tres->num_rows > 0) {
        echo "<table border='1' cellpadding='5'><tr><th>id</th><th>user_id</th><th>bosqich</th><th>tavsif_uz</th></tr>";
        while ($t = $tres->fetch_assoc()) {
            echo "<tr><td>{$t['id']}</td><td>{$t['user_id']}</td><td>{$t['bosqich']}</td><td>" . substr($t['tavsif_uz'], 0, 50) . "...</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:orange'>⚠️ talim jadvali bo'sh yoki jadval yo'q</p>";
    }
} else {
    echo "<p style='color:red'>❌ \$link yo'q — talim ni o'qib bo'lmaydi</p>";
}

// 6. header jadvali
echo "<h3>🏷️ header jadvali</h3>";
if (isset($link) && $link instanceof mysqli) {
    $hres = $link->query("SELECT * FROM header");
    if ($hres && $hres->num_rows > 0) {
        echo "<table border='1' cellpadding='5'><tr><th>id</th><th>user_id</th><th>ism</th><th>familiya</th></tr>";
        while ($h = $hres->fetch_assoc()) {
            echo "<tr><td>{$h['id']}</td><td>{$h['user_id']}</td><td>{$h['ism']}</td><td>{$h['familiya']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:orange'>⚠️ header jadvali bo'sh</p>";
    }
}
?>
