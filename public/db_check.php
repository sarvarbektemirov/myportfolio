<?php
include_once(__DIR__ . "/adminpanel/db.php");
$res = $link->query("SHOW TABLES");
echo "<h2>Database Tables:</h2><ul>";
while($row = $res->fetch_array()) {
    echo "<li>" . $row[0] . "</li>";
}
echo "</ul>";
?>
