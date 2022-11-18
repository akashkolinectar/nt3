<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['vendor'])!= '') {
$sql = "select * from ntsitevendor where vendor='" . $rowid['vendor'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(TRUE);
} else {

    if ($rowid['vendor'] != '') {
        $query1 = "INSERT INTO ntsitevendor(vendor)VALUES('" . $rowid['vendor'] . "')";

        $result1 = mysqli_query($conf, $query1);
        echo json_encode(FALSE);
        //echo json_encode("Movicel Area Added Successfully");
    }
}
}
if (isset($rowid['evendor']) != '') {
    $query1 = "update ntsitevendor set vendor='".$rowid['evendor']."' where vendor='" . $rowid['fvendor'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['fvendor']);
}
if (isset($rowid['dvendor']) != '') {
    $query1 = "update ntsitevendor set is_active='0' where vendor='" . $rowid['dvendor'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['dvendor']);
}
?>