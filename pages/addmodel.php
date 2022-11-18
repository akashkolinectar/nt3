<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['model']) != '') {
$sql = "select * from ntsitemodel where model='" . $rowid['model'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(TRUE);
} else {

    if ($rowid['model'] != '') {
        $query1 = "INSERT INTO ntsitemodel(model)VALUES('" . $rowid['model'] . "')";

        $result1 = mysqli_query($conf, $query1);
        echo json_encode(FALSE);
        //echo json_encode("Movicel Area Added Successfully");
    }
}
}
if (isset($rowid['emodel']) != '') {
    $query1 = "update ntsitemodel set model='".$rowid['emodel']."' where model='" . $rowid['fmodel'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['fmodel']);
}
if (isset($rowid['dmodel']) != '') {
    $query1 = "update ntsitemodel set is_active='0' where model='" . $rowid['dmodel'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['dmodel']);
}
?>