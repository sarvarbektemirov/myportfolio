<?php
$master_link = new mysqli('mysql-8.4', 'root', '', 'portfolio');
$master_link->query("ALTER TABLE users ADD UNIQUE INDEX IF NOT EXISTS (username)");
$master_link->query("ALTER TABLE users ADD UNIQUE INDEX IF NOT EXISTS (email)");
echo "Indexes ensured.";
