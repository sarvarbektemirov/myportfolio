<?php
$link = new mysqli('mysql-8.4', 'root', '', 'portfolio');
$res = $link->query("SHOW TABLES");
$schema = [];
while ($row = $res->fetch_array()) {
    $table = $row[0];
    if ($table == 'users') continue;
    $create = $link->query("SHOW CREATE TABLE `$table`")->fetch_assoc();
    $schema[$table] = $create['Create Table'];
}
file_put_contents('schema_dump.json', json_encode($schema, JSON_PRETTY_PRINT));
echo "Schema dumped successfully.";
?>
