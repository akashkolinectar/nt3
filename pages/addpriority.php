<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['priority']) != '') {
$sql = "select * from ntsitepriority where priority='" . $rowid['priority'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(TRUE);
} else {

    if ($rowid['priority'] != '') {
        $query1 = "INSERT INTO ntsitepriority(priority)VALUES('" . $rowid['priority'] . "')";
        $result1 = mysqli_query($conf, $query1);
        echo json_encode(FALSE);
    }
}
}
if (isset($rowid['epriority']) != '') {
    $query1 = "update ntsitepriority set priority='".$rowid['epriority'] ."' where priority='" . $rowid['fpriority'] . "'";
    $result1 = mysqli_query($conf, $query1);  
    echo json_encode($rowid['fpriority']);
}
if (isset($rowid['dpriority']) != '') {
    $query1 = "update ntsitepriority set is_active='0' where priority='" . $rowid['dpriority'] . "'";

    $result1 = mysqli_query($conf, $query1);  
    echo json_encode($rowid['dpriority']);
}
?>