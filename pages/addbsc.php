<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['bsc'])!= '') {
$sql = "select * from ntsitebsc where bsc='" . $rowid['bsc'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(TRUE);
} else {

    if ($rowid['bsc'] != '') {
        $query1 = "INSERT INTO ntsitebsc(bsc)VALUES('" . $rowid['bsc'] . "')";

        $result1 = mysqli_query($conf, $query1);
        echo json_encode(FALSE);
        //echo json_encode("Movicel Area Added Successfully");
    }
}
}
if (isset($rowid['ebsc']) != '') {
    $query1 = "update ntsitebsc  set bsc='". $rowid['ebsc']."' where bsc='" . $rowid['fbsc'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['fbsc']);
}
if (isset($rowid['dbsc']) != '') {
    $query1 = "update ntsitebsc set is_active='0' where bsc='" . $rowid['dbsc'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['dbsc']);
}
?>