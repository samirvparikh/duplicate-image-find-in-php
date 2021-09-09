<?php
//////////////////////////////////////////////////
// DATABASE SETUP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('db.php');

setup_database();

//////////////////////////////////////////////////
// PROCESSING IMAGES

// specify path to images
$images_path = 'images';

// ensure directory exists
if (!is_dir($images_path)) {
    die('Directory does not exist.');
}

// change directory
//chdir($images_path);
// get a list of files
//$files = `find . -type f | sed 's/^\.\///'`;
// explode files list on newline
//$files = explode("\n", trim($files));

// define a list of file extensions to process
$file_extensions = array('jpg', 'jpeg', 'png', 'bmp', 'gif', 'tiff',);

if (file_exists($images_path)) {
    $dh1 = opendir($images_path);
    while (($file_path = readdir($dh1)) !== false) {
        if ($file_path != "." && $file_path != "..") {
            $path_info = pathinfo($file_path);
            $file_name = $path_info['basename'];
            $file_extension = strtolower($path_info['extension']);
            if (!in_array($file_extension, $file_extensions)) {
                continue;
            }
            // get md5 hash of file
            $file_md5 = md5_file($images_path . "/" . $file_path);
            echo $file_md5 . "=" . $file_name . "<br/>";

            // get file modified time
            //$file_modified = date('Y-m-d H:i:s', filemtime($file_path));

            // create sql to insert record
            $sql = sprintf(
                "insert into `%s` (file_path, file_name, file_extension, file_md5) values ('%s','%s','%s','%s')",
                mysqli_real_escape_string($db, $db_table),
                mysqli_real_escape_string($db, $images_path . '/' . $file_path),
                mysqli_real_escape_string($db, $file_name),
                mysqli_real_escape_string($db, $file_extension),
                mysqli_real_escape_string($db, $file_md5)
                //mysqli_real_escape_string($db, $file_modified)
            );

            // execute sql
            $result = mysqli_query($db, $sql);
        }
    }
}

// loop through files
/*foreach ($files as $file_path) {
    // get path info
    $path_info = pathinfo($file_path);
    $file_name = $path_info['basename'];
    $file_extension = strtolower($path_info['extension']);

    // check file extension
    if (!in_array($file_extension, $file_extensions)) {
        continue;
    }

    // get md5 hash of file
    $file_md5 = md5_file($file_path);

    // get file modified time
    $file_modified = date('Y-m-d H:i:s', filemtime($file_path));

    // create sql to insert record
    $sql = sprintf(
        "insert into `%s` (file_path, file_name, file_extension, file_md5, file_modified) values ('%s','%s','%s','%s','%s')",
        mysqli_real_escape_string($db_table),
        mysqli_real_escape_string($images_path . '/' . $file_path),
        mysqli_real_escape_string($file_name),
        mysqli_real_escape_string($file_extension),
        mysqli_real_escape_string($file_md5),
        mysqli_real_escape_string($file_modified)
    );

    // execute sql
    $result = mysqli_query($sql, $db);
}*/

//////////////////////////////////////////////////
// FUNCTIONS

function setup_database()
{

    global $db;
    global $db_database;
    global $db_table;

    // create database if it is does not exist
    $sql = sprintf(
        "create database if not exists `%s`",
        mysqli_real_escape_string($db, $db_database)
    );
    $result = mysqli_query($db, $sql);

    // check for error
    if (!$result) {
        die(mysqli_error());
    }

    // select database
    $result = mysqli_select_db($db, $db_database);

    // check for error
    if (!$result) {
        die(mysqli_error());
    }

    // create table if it does not exist
    $sql = sprintf(
        "
    CREATE TABLE IF NOT EXISTS `%s` (
      `fid` int(11) NOT NULL AUTO_INCREMENT,
      `file_path` varchar(255) NOT NULL,
      `file_name` varchar(255) NOT NULL,
      `file_extension` varchar(10) NOT NULL,
      `file_md5` varchar(32) NOT NULL,
      `file_modified` datetime NOT NULL,
      PRIMARY KEY (`fid`),
      KEY `idx_file_md5` (`file_md5`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1",
        mysqli_real_escape_string($db, $db_table)
    );

    $result = mysqli_query($db, $sql);

    // check for error
    if (!$result) {
        die(mysqli_error());
    }

    // drop existing records from table
    $sql = sprintf(
        "truncate table `%s`",
        mysqli_real_escape_string($db, $db_table)
    );
    $result = mysqli_query($db, $sql);

    // check for error
    if (!$result) {
        die(mysqli_error());
    }
}
