<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['substage']) != '') {
$sql = "select * from ntsitesubstage where sub_stage='" . $rowid['substage'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(TRUE);
} else {

    if ($rowid['substage'] != '') {
        $query1 = "INSERT INTO ntsitesubstage(sub_stage)VALUES('" . $rowid['substage'] . "')";

        $result1 = mysqli_query($conf, $query1);
        echo json_encode(FALSE);
        //echo json_encode("Movicel Area Added Successfully");
    }
}
}
if (isset($rowid['esubstage']) != '') {
    $query1 = "update ntsitesubstage set sub_stage='".$rowid['esubstage']."' where sub_stage='" . $rowid['fsubstage'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['fsubstage']);
}
if (isset($rowid['dsubstage']) != '') {
    $query1 = "delete from ntsitesubstage where sub_stage='" . $rowid['dsubstage'] . "'";
    $result1 = mysqli_query($conf, $query1);
    echo json_encode($rowid['dsubstage']);
}
?>