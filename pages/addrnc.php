<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['rnc']) != '') {
$sql = "select * from ntsiternc where rnc='" . $rowid['rnc'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(TRUE);
} else {

    if ($rowid['rnc'] != '') {
        $query1 = "INSERT INTO ntsiternc(rnc)VALUES('" . $rowid['rnc'] . "')";

        $result1 = mysqli_query($conf, $query1);
        echo json_encode(FALSE);
        //echo json_encode("Movicel Area Added Successfully");
    }
}
}
if (isset($rowid['ernc']) != '') {
    $query1 = "update ntsiternc set rnc='".$rowid['ernc']."' where rnc='" . $rowid['frnc'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['frnc']);
}
if (isset($rowid['drnc']) != '') {
    $query1 = "update ntsiternc set is_active='0' where rnc='" . $rowid['drnc'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['drnc']);
}
?>