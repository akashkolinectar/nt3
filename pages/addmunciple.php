<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['munciple']) != '') {
$sql = "select * from ntsitemunciple where province_id='".$rowid['province_id']."' AND munciple='" . $rowid['munciple'] . "' AND is_active = 1";

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

    $muncipleQuery = "SELECT MN.munciple_id,MN.munciple from ntsitemunciple MN LEFT JOIN ntsiteprovince PR ON PR.province_id=MN.province_id WHERE MN.province_id='" . $rowid['e_provinceid'] . "' AND MN.is_active = 1";
    $result = $conf->query($muncipleQuery);

    $muncipleDD = '<option value=""> -- Select One -- </option>';
    //if($result->num_rows>0){
         while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $selected = ($rowid['e_muncipleid']==$row["munciple_id"])? "selected='selected'":"";
            $muncipleDD .= '<option '.$selected.' value="'.$row["munciple_id"].'">'.$row["munciple"].'</option>';
        }
    //}
   
    $localityQuery = "SELECT LC.locationid,LC.locationname from nplocation LC LEFT JOIN ntsitemunciple MN ON MN.munciple_id=LC.munciple_id WHERE LC.munciple_id='" . $rowid['e_muncipleid'] . "' AND LC.is_active = 1";
    $result = $conf->query($localityQuery);

    $locationDD = '<option value=""> -- Select One -- </option>';
    if($result->num_rows>0){
         while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $locationDD .= '<option '.$selected.' value="'.$row["locationid"].'">'.$row["locationname"].'</option>';
        }
    }

    echo json_encode(array("jmuncipleid"=>$rowid['e_muncipleid'],'location'=>$locationDD,'munciple'=>$muncipleDD,"province"=>$rowid['e_provinceid']));
}
if (isset($rowid['dmuncipleid']) != '') {
    $query1 = "update ntsitemunciple set is_active='0' where munciple_id='" . $rowid['dmuncipleid'] . "'";
   
    $result1 = mysqli_query($conf, $query1);
    echo json_encode($rowid['dmuncipleid']);
}

?>