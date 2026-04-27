<?php
include_once("init.php");
$tables = ['talim', 'nashrlar', 'nashr_carousel', 'nashr', 'talabalar', 'teaching', 'others'];
foreach ($tables as $table) {
    echo "--- Table: $table ---\n";
    $res = $link->query("DESCRIBE $table");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            echo $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "Error or table not found.\n";
    }
    echo "\n";
}
?>
