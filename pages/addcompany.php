<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
$sql = "select * from npcompany where companyname='" . $rowid['companyp'] . "'";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode("Company Already exist");
} else {

    if ($rowid['companyp'] != '') {
        $query1 = "INSERT INTO npcompany(companyname)VALUES('" . $rowid['companyp'] . "')";

        $result1 = mysqli_query($conf, $query1);

        //echo json_encode("Company Added Successfully");
    }
}
?>