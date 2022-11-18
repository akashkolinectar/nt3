<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['provincep']) != '') {
$sql = "select * from ntsiteprovince where province='" . $rowid['provincep'] . "'";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode("-1");
} else {

    if (isset($rowid['provincep']) != '') {
        $query1 = "INSERT INTO ntsiteprovince(province)VALUES('" . $rowid['provincep'] . "')";

       // $result1 = mysqli_query($conf, $query1);
        if ($conf->query($query1) === TRUE) {
            $jprovinceid = $conf->insert_id;
            echo json_encode(array("jprovinceid"=>$jprovinceid)); 
       // echo json_encode($rowid['fbsc']);
    }
}
}
}
if (isset($rowid['eprovince']) != '') {
    $sql = "update ntsiteprovince set province='".$rowid['eprovince']."'  where province_id='" . $rowid['fprovince'] . "'";
    $result = $conf->query($sql); 
    echo json_encode(array("jprovinceid"=>$rowid['fprovince']));
}
if (isset($rowid['dprovince']) != '') {
    $query1 = "update ntsiteprovince set is_active='0' where province_id='" . $rowid['dprovince'] . "'";
    $result1 = mysqli_query($conf, $query1);
    echo json_encode($rowid['dprovince']);
}
?>