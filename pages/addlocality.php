<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if (isset($rowid['locality']) != '') {
$sql = "select * from nplocation where munciple_id='".$rowid['munciple_id']."' AND locationname='" . $rowid['locality'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode("-1");
} else {

    if (isset($rowid['locality']) != '') {
        $query1 = "INSERT INTO nplocation(munciple_id,locationname)VALUES('".$rowid['munciple_id']."','" . $rowid['locality'] . "')";

       // $result1 = mysqli_query($conf, $query1);
        
        if ($conf->query($query1) === TRUE) {
            $jlocalityid = $conf->insert_id;
            echo json_encode(array("jlocalityid"=>$jlocalityid));        
        }
        
    }
}
}
if (isset($rowid['elocality']) != '' && isset($rowid['el_muncipleid']) != '' && isset($rowid['el_localityid']) != '') {
    $sql = "update nplocation set locationname='".$rowid['elocality']."' where munciple_id='" . $rowid['el_muncipleid'] . "' AND locationid='".$rowid['el_localityid']."'";
    $result = $conf->query($sql); 

    $muncipalQuery = "SELECT MN.munciple_id,MN.munciple from ntsitemunciple MN LEFT JOIN nplocation LC ON MN.munciple_id=LC.munciple_id WHERE MN.is_active = 1";
   // echo  $muncipalQuery;
    $result = $conf->query($muncipalQuery);

    $muncipalDD = '';
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $selected = ($row['munciple_id']==$rowid['el_muncipleid'])? "selected='selected'":"";
        $muncipalDD .= '<option '.$selected.' value="'.$row["munciple_id"].'">'.utf8_decode($row["munciple"]).'</option>';
    }

    $localityQuery = "SELECT LC.locationid,LC.locationname from nplocation LC LEFT JOIN ntsitemunciple MN ON MN.munciple_id=LC.munciple_id WHERE LC.munciple_id='" . $rowid['el_muncipleid'] . "' AND LC.is_active = 1";
    $result = $conf->query($localityQuery);

    $localityDD = '<option value=""> -- Select One -- </option>';
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $selected = ($rowid['el_localityid']==$row["locationid"])? "selected='selected'":"";
        $localityDD .= '<option '.$selected.' value="'.$row["locationid"].'">'.$row["locationname"].'</option>';
    }

   // echo $muncipalDD;
    echo json_encode(array("jlocalityid"=>$rowid['el_localityid'],'location'=>$localityDD,'muncipal'=>$muncipalDD));
}
if (isset($rowid['dlocalityid']) != '') {
    $query1 = "update nplocation set is_active='0' where locationid='" . $rowid['dlocalityid'] . "'";

    $result1 = mysqli_query($conf, $query1);
    echo json_encode($rowid['dlocalityid']);
}
?>