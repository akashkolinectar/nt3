<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['mgw'] )!= '') {
$sql = "select * from ntsitemgw where mgw='" . $rowid['mgw'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(TRUE);
} else {

    if ($rowid['mgw'] != '') {
        $query1 = "INSERT INTO ntsitemgw(mgw)VALUES('" . $rowid['mgw'] . "')";

        $result1 = mysqli_query($conf, $query1);
        echo json_encode(FALSE);
        //echo json_encode("Movicel Area Added Successfully");
    }
}
}
    if (isset($rowid['emgw']) != '') {
        $query1 = "update ntsitemgw set mgw='".$rowid['emgw']."' where mgw='" . $rowid['fmgw'] . "'";

        $result1 = mysqli_query($conf, $query1);

        echo json_encode($rowid['fmgw']);
    }
    if (isset($rowid['dmgw']) != '') {
        $query1 = "update ntsitemgw set is_active='0' where mgw='" . $rowid['dmgw'] . "'";

        $result1 = mysqli_query($conf, $query1);

        echo json_encode($rowid['dmgw']);
    }

?>