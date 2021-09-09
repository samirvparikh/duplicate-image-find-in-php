<?php
//////////////////////////////////////////////////
// DATABASE

require_once('db.php');

// select database
$result = mysqli_select_db($db, $db_database);

// check for error
if (!$result) {
  die(mysqli_error());
}

//////////////////////////////////////////////////
// PROCESS REQUEST

$md5 = $_GET['md5'];
$index = intval($_GET['index']);

// fetch images with md5 index
$sql = sprintf(
  "
  select *
  from `%s`
  where file_md5 = '%s'
  order by fid asc
  ",
  mysqli_real_escape_string($db, $db_table),
  mysqli_real_escape_string($db, $md5)
);

$result = mysqli_query($db, $sql);

// check for error
if (!$result) {
  die(mysqli_error());
}

// fetch results
$rows = array();
while ($row = mysqli_fetch_object($result)) {
  $rows[] = $row;
}

// get image data
$file_path = $rows[$index]->file_path;
$file_extension = $rows[$index]->file_extension;

header("Content-type: image/$file_extension");
readfile($file_path);
