<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['elementtype'])!= '') {
$sql = "select * from ntsiteelementtype where element_type='" . $rowid['elementtype'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode("elementtype Already exist");
} else {

    if ($rowid['elementtype'] != '') {
        $query1 = "INSERT INTO ntsiteelementtype(element_type)VALUES('" . $rowid['elementtype'] . "')";

        $result1 = mysqli_query($conf, $query1);

        //echo json_encode("Movicel Area Added Successfully");
    }
}
}
if (isset($rowid['eelementtype']) != '') {
    $query1 = "update ntsiteelementtype set element_type='".$rowid['eelementtype']."'  where element_type='" . $rowid['eelementtype'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['felementtype']);
}
if (isset($rowid['delementtype']) != '') {
    $query1 = "update ntsiteelementtype set is_active='0'  where element_type='" . $rowid['delementtype'] . "'";

    $result1 = mysqli_query($conf, $query1);

    echo json_encode($rowid['delementtype']);
}

?>