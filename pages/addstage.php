<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['stage']) != '') {
$sql = "select * from ntsitestage where stage='" . $rowid['stage'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(TRUE);
} else {

    if ($rowid['stage'] != '') {
        $query1 = "INSERT INTO ntsitestage(stage)VALUES('" . $rowid['stage'] . "')";

        $result1 = mysqli_query($conf, $query1);
        echo json_encode(FALSE);
        //echo json_encode("Movicel Area Added Successfully");
    }
}
}
if (isset($rowid['estage']) != '') {
    $query1 = "update ntsitestage set stage='".$rowid['estage']."' where stage='" . $rowid['fstage'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['fstage']);
}
if (isset($rowid['dstage']) != '') {
    $query1 = "update ntsitestage set is_active='0' where stage='" . $rowid['dstage'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['dstage']);
}
?>