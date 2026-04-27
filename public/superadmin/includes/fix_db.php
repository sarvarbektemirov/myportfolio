<?php
include_once('db.php');
$master_link->query("ALTER TABLE system_messages MODIFY subject VARCHAR(255) DEFAULT ''");
echo "Table updated successfully";
?>
