<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
  if (isset($rowid['movicelp']) != '') {
$sql = "select * from npmovicelarea where areaname='" . $rowid['movicelp'] . "'";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode("Movicel Area Already exist");
} else {

    if (isset($rowid['movicelp']) != '') {
        $query1 = "INSERT INTO npmovicelarea(areaname)VALUES('" . $rowid['movicelp'] . "')";

        $result1 = mysqli_query($conf, $query1);

        //echo json_encode("Movicel Area Added Successfully");
    }
}
}
  
?>