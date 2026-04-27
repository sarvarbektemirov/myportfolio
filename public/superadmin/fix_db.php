<?php
include_once('includes/db.php');

$sql = "ALTER TABLE system_messages ADD COLUMN IF NOT EXISTS reply_to_id INT DEFAULT NULL AFTER subject";

if ($master_link->query($sql)) {
    echo "Successfully added reply_to_id column.";
} else {
    echo "Error: " . $master_link->error;
}
?>
