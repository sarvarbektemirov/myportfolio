<?php
include_once('includes/db.php');
$res = $master_link->query("SELECT id, message, reply_to_id FROM system_messages ORDER BY id DESC LIMIT 5");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Msg: " . $row['message'] . " | ReplyTo: " . ($row['reply_to_id'] ?? 'NULL') . "\n";
}
?>
