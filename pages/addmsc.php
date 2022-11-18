<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['msc'])!= '') {
$sql = "select * from ntsitemsc where msc='" . $rowid['msc'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(TRUE);
} else {

    if ($rowid['msc'] != '') {
        $query1 = "INSERT INTO ntsitemsc(msc)VALUES('" . $rowid['msc'] . "')";

        $result1 = mysqli_query($conf, $query1);
        echo json_encode(FALSE);
        //echo json_encode("Movicel Area Added Successfully");
    }
}
}
if (isset($rowid['emsc']) != '') {
    $query1 = "update ntsitemsc set msc='". $rowid['emsc']."' where msc='" . $rowid['fmsc'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['fmsc']);
}
if (isset($rowid['dmsc']) != '') {
    $query1 = "update ntsitemsc set is_active='0' where msc='" . $rowid['dmsc'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['dmsc']);
}
?>