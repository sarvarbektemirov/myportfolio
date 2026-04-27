<?php
include_once('includes/db.php');

// Get all databases
$res = $master_link->query("SHOW DATABASES LIKE 'portfolio_%'");
$dbs = [];
while ($row = $res->fetch_array()) {
    $dbs[] = $row[0];
}

echo "<h2>Starting Migration for Skills Columns</h2>";
echo "Total databases found: " . count($dbs) . "<br><br>";

foreach ($dbs as $db) {
    echo "Processing <strong>$db</strong>... ";
    
    // Connect to user database
    $user_link = new mysqli('mysql-8.4', 'root', '', $db);
    if ($user_link->connect_error) {
        echo "<span style='color:red'>Connection failed: " . $user_link->connect_error . "</span><br>";
        continue;
    }

    // Add columns if they don't exist
    $sql_uz = "ALTER TABLE home ADD COLUMN IF NOT EXISTS skills_uz TEXT AFTER bio_en";
    $sql_en = "ALTER TABLE home ADD COLUMN IF NOT EXISTS skills_en TEXT AFTER skills_uz";

    if ($user_link->query($sql_uz) && $user_link->query($sql_en)) {
        echo "<span style='color:green'>Success</span><br>";
    } else {
        echo "<span style='color:red'>Error: " . $user_link->error . "</span><br>";
    }
    
    $user_link->close();
}

echo "<br><strong>Migration finished!</strong>";
?>
