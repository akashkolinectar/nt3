<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['munciple']) != '') {
$sql = "select * from ntsitemunciple where province_id='".$rowid['province_id']."' AND munciple='" . $rowid['munciple'] . "'";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode("-1");
} else {

    if (isset($rowid['munciple']) != '') {
        $query1 = "INSERT INTO ntsitemunciple(province_id,munciple)VALUES('".$rowid['province_id']."','" . $rowid['munciple'] . "')";
      //  $result1 = mysqli_query($conf, $query1);
        if ($conf->query($query1) === TRUE) {
            $jmuncipleid = $conf->insert_id;
        }
      
if (isset($rowid['province_id']) != '') {
$sql = "select * from ntsiteprovince where province_id='" . $rowid['province_id'] . "'";
$result = $conf->query($sql);
$followingdata = $result->fetch_assoc();

if ($result->num_rows > 0) {
 echo json_encode(array("jmuncipleid"=>$jmuncipleid,"jprovinceid" =>$followingdata['province_id'], "jprovince" =>$followingdata['province'])); 
}
}
      
    }
}
}
if (isset($rowid['emunciple']) != '' && isset($rowid['e_provinceid']) != '' && isset($rowid['e_muncipleid']) != '') {
    $sql = "update ntsitemunciple set munciple='".$rowid['emunciple']."' where munciple_id='" . $rowid['e_muncipleid'] . "' AND province_id='".$rowid['e_provinceid']."'";
    $result = $conf->query($sql); 
    echo json_encode(array("jmuncipleid"=>$rowid['e_muncipleid']));
}
if (isset($rowid['dmuncipleid']) != '') {
    $query1 = "update ntsitemunciple set is_active='0' where munciple_id='" . $rowid['dmuncipleid'] . "'";
   
    $result1 = mysqli_query($conf, $query1);
    echo json_encode($rowid['dmuncipleid']);
}

?>