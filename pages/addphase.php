<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['phase']) != '') {
$sql = "select * from ntsitephase where phase='" . $rowid['phase'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(TRUE);
} else {

    if ($rowid['phase'] != '') {
        $query1 = "INSERT INTO ntsitephase(phase)VALUES('" . $rowid['phase'] . "')";

        $result1 = mysqli_query($conf, $query1);
        echo json_encode(FALSE);
        //echo json_encode("Movicel Area Added Successfully");
    }
}
}
if (isset($rowid['ephase']) != '') {
    $query1 = "update ntsitephase set phase='".$rowid['ephase']."' where phase='" . $rowid['fphase'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['fphase']);
}
if (isset($rowid['dphase']) != '') {
    $query1 = "update ntsitephase set is_active='0' where phase='" . $rowid['dphase'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['dphase']);
}
?>