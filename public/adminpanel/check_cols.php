<?php
include_once('db.php');
$res = $link->query("SHOW COLUMNS FROM talim");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
