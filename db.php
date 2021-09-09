<?php
// define mysql credentials
$db_user = 'root';
$db_pass = '';
$db_database = 'mydatabase';
$db_table = 'test';
$db_host = 'localhost';

// connect to mysql database
$db = mysqli_connect($db_host, $db_user, $db_pass);

// check for mysql connection
if (!$db) {
    die('Could not connect to database.');
}
