<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['provincep']) != '') {
$sql = "select * from ntsiteprovince where province='" . $rowid['provincep'] . "' AND is_active = 1";

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

    $muncipalQuery = "SELECT MN.munciple_id,MN.munciple from ntsitemunciple MN LEFT JOIN ntsiteprovince PR ON MN.province_id=PR.province_id WHERE MN.is_active = 1 AND MN.province_id='".$rowid['fprovince']."'";
    $result = $conf->query($muncipalQuery);
    $muncipalDD = '<option value=""> -- Select One -- </option>';
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $muncipalDD .= '<option value="'.$row["munciple_id"].'">'.utf8_decode($row["munciple"]).'</option>';
    }

    echo json_encode(array("jprovinceid"=>$rowid['fprovince'],'muncipal'=>$muncipalDD));
}
if (isset($rowid['dprovince']) != '') {
    $query1 = "update ntsiteprovince set is_active='0' where province_id='" . $rowid['dprovince'] . "'";
    $result1 = mysqli_query($conf, $query1);
    echo json_encode($rowid['dprovince']);
}
?>