<?php
$db = new PDO('sqlite:database/database.sqlite');
$tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);
