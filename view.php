<?php
//////////////////////////////////////////////////
// DATABASE SETUP

require_once('db.php');

// select database
$result = mysqli_select_db($db, $db_database);

// check for error
if (!$result) {
    die(mysqli_error());
}

//////////////////////////////////////////////////
// FETCHING MD5S

// start session
session_start();

// check for session data
if (isset($_SESSION['md5s']) || (!is_array($_SESSION['md5s']) || empty($_SESSION['md5s']))) {
    fetch_md5s();
}

// determine which md5 to show
$md5_index = (isset($_GET['md5_index'])) ? intval($_GET['md5_index']) : "0";

// fetch images with md5 index
$sql = sprintf(
    "
  select *
  from `%s`
  where file_md5 = '%s'
  order by fid asc
  ",
    mysqli_real_escape_string($db, $db_table),
    mysqli_real_escape_string($db, $_SESSION['md5s'][$md5_index])
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

// create image output in a table. note the image scr is calling the view-image.php script with $_GET arguments.
$output = "";
$output .= "<table><tr>";
foreach ($rows as $index => $data) {
    $output .= "<td style='width: " . (100 / count($rows)) . "%'>";
    $output .= "<img style='width: 100%' src='view-image.php?md5=" . $data->file_md5 . "&index=" . $index . "' />";
    $output .= $data->file_name . "<br/>";
    $output .= $data->file_path . "<br/>";
    $output .= "</td>";
}
$output .= "</tr></table>";

$output .= "<a href='view.php?md5_index=" . ($md5_index + 1) . "'>Next >></a>";

print $output;

//////////////////////////////////////////////////
// FUNCTIONS

function fetch_md5s()
{

    global $db;
    global $db_table;

    // get a list of md5 hashes with dupes
    $sql = sprintf(
        "
    select file_md5
    from `%s`
    group by file_md5
    having count(*) > 1
    ",
        mysqli_real_escape_string($db, $db_table)
    );

    $result = mysqli_query($db, $sql);

    // check for error
    if (!$result) {
        die(mysqli_error());
    }

    // fetch results
    $md5s = array();
    while ($row = mysqli_fetch_object($result)) {
        $md5s[] = $row->file_md5;
    }

    // store md5s in session
    $_SESSION['md5s'] = $md5s;
}
