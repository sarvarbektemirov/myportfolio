<?php
$master_link = new mysqli('mysql-8.4', 'root', '', 'portfolio');
$res = $master_link->query("DESCRIBE users");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
