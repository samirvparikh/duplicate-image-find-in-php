<?php
// define mysql credentials
$db_user = 'admin';
$db_pass = 'HKNG@H1hwRS@#';
$db_database = 'testdb';
$db_table = 'testdb';
$db_host = '192.168.1.12';

// connect to mysql database
$db = mysqli_connect($db_host, $db_user, $db_pass);

// check for mysql connection
if (!$db) {
    die('Could not connect to database.');
}
