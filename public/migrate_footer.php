<?php
include_once("adminpanel/db.php");

$columns = [
    "bio_uz TEXT",
    "bio_en TEXT",
    "status_uz VARCHAR(255)",
    "status_en VARCHAR(255)",
    "copyright_uz VARCHAR(255)",
    "copyright_en VARCHAR(255)"
];

foreach ($columns as $col) {
    $link->query("ALTER TABLE footer ADD COLUMN $col");
}

echo "Database migration completed (columns added if they didn't exist).";
?>
