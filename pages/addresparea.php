<?php

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
if(isset($rowid['resparea'])!=''){
$sql = "select * from ntsiteresponsible where responsible_area='" . $rowid['resparea'] . "' AND is_active = 1";

$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode("Responsible Area Already exist");
} else {

    if ($rowid['resparea'] != '') {
        $query1 = "INSERT INTO ntsiteresponsible(responsible_area)VALUES('" . $rowid['resparea'] . "')";

        $result1 = mysqli_query($conf, $query1);

        //echo json_encode("Movicel Area Added Successfully");
    }
}
}
//Update Responsible Area

        if(isset($rowid['eresparea']) != '') {
            $query1 = "update ntsiteresponsible set responsible_area='".$rowid['eresparea']."' where responsible_area='" . $rowid['fresparea'] . "'";
    
            $result1 = mysqli_query($conf, $query1);
    
            echo json_encode($rowid['fresparea']);
        
    }
    
   
        if (isset($rowid['dresparea']) != '') {
            $query1 = "update ntsiteresponsible set is_active='0' where responsible_area='" . $rowid['dresparea'] . "'";
    
            $result1 = mysqli_query($conf, $query1);
    
            echo json_encode($rowid['dresparea']);
        
    }
    


?>