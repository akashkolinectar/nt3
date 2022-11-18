<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
$sql = "select * from nplocation where locationname='" . $rowid['locationp'] . "'";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode("Location Already exist");
} else {

    if ($rowid['locationp'] != '') {
        $query1 = "INSERT INTO nplocation(locationname)VALUES('" . $rowid['locationp'] . "')";

        $result1 = mysqli_query($conf, $query1);

        //echo json_encode("Location Added Successfully");
    }
}
?>