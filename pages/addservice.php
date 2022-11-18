<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
$sql = "select * from npservice where servicename='" . $rowid['servicep'] . "'";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode("Service Already exist");
} else {

    if ($rowid['servicep'] != '') {
        $query1 = "INSERT INTO npservice(servicename)VALUES('" . $rowid['servicep'] . "')";

        $result1 = mysqli_query($conf, $query1);

        //echo json_encode("Service Added Successfully");
    }
}
?>