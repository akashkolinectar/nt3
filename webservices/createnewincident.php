<?php 
require_once('wbdb.php');

//$postData = json_decode(file_get_contents("php://input"),true);
$postData = $_REQUEST;
//print_r($postData); exit;
$data = array('flag'=>false,'msg'=>'No data found','info'=>array());

//$query = "SELECT Incident WHERE DATE_SUB(NOW(), INTERVAL 20 DAY) < start_date AND login='".$postData['userid']."'";
//$query = "SELECT * FROM ntticket_incident WHERE DATE_SUB(NOW(), INTERVAL 20 DAY) < tto_started ";

//if(isset($postData['category']) && isset($postData['subcategory'])){
    $query='';
    
   // $geo_array=explode(",", $geofence_id);
           // var_dump($geo_array);



    if(isset($postData['title'])){
       
    //$query = "SELECT reason_id,reason  from ntreason is_active=1";
    //$query3 = "INSERT INTO ntticket_incident (`status`, `impact`,`priority`,`urgency`,`origin`,`service_id`,`servicesubcategory_id`,`escalation_flag`,`escalation_reason`,`tto_push_notification`,`ttr_push_notification`) VALUES ('".$postData['status']."', '".$postData['impact']."','".$postData['priority']."','".$postData['urgency']."','".$postData['origin']."','".$postData['service_id']."','".$postData['servicesubcategory_id']."','".$postData['escalation_flag']."','".$postData['escalation_reason']."','".$postData['tto_push_notification']."','".$postData['ttr_push_notification']."')";
//$query2 = "INSERT INTO ntticket (`id`, `operational_status`, `ref`, `org_id`, `caller_id`, `team_id`, `agent_id`, `title`, `description`, `description_format`, `start_date`, `end_date`, `last_update`, `close_date`, `private_log`, `private_log_index`, `finalclass`,`province_id`,`finalclass`) VALUES (".$id.", 'ongoing', 'TT-000".$refid."', '".$postData['orgnization']."', '".$postData['caller']."', '0', '0', '".$postData['title']."', '".$postData['description']."', 'html', '".date('Y-m-d H:i:s')."', NULL, NULL, NULL, NULL, NULL, 'Incident')";
    $query2 = "INSERT INTO ntticket (`operational_status`,`ref`,`org_id`,`caller_id`,`team_id`,`agent_id`,`title`,`description`,`description_format`,`start_date`,`end_date`,`finalclass`,`province_id`,`provider_id`,`aftd_network_id`,`aftd_comp_type_id`,`reason_id`,`sub_reason_id`,`event_id`,`category_id`)VALUES ('ongoing',' ','".$postData['org_id']."','".$postData['caller_id']."','0','0','".$postData['title']."','".$postData['description']."','".$postData['description_format']."','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."','Incident','".$postData['province_id']."','0','0','0','".$postData['reason_id']."','".$postData['sub_reason_id']."','".$postData['event_id']."','".$postData['category_id']."')";
   // echo $query2; exit;
    //$result2 = mysqli_query($conf, $query2);
    if(mysqli_query($conf, $query2)){
            $ticketid = $conf->insert_id;
            }
            $zeros = (string)((strlen($ticketid)>5)? '':((strlen($ticketid)==5)? '0':((strlen($ticketid)==4)? '00':((strlen($ticketid)==3)? '000': ((strlen($ticketid)==2)? '0000':'00000' ) ) ) ) );
            $refName = "TT-".$zeros."".$ticketid;
            
$queryref = "Update ntticket set ref='".$refName."' where ntticket.id='".$ticketid."'";
//echo $query1; exit;
$resultref = mysqli_query($conf, $queryref);
              $sqlemail = "select * from ntcontact join ntperson on ntcontact.id=ntperson.id where ntcontact.id='" .$postData['caller_id']. "'";
            $results = $conf->query($sqlemail);


if ($results->num_rows > 0) {
  while($row = mysqli_fetch_array($results)){
    $to = "".$row['email']."";
    $subject = "Incident [".date('d-M-Y H:i:s')."]";
    $txt = "Incident Status Changed Successfully";
    /*$headers = "From: nt3system@movicel.co.ao" . "\r\n";*/
    $headers = "From: contact@nectarinfotel.com" . "\r\n" .
    "CC: mahesh.chavhan@nectarinfotel.com";
    mail($to,$subject,$txt,$headers);
  }
}
            $str_arra=array();         
    $str_arra = explode (",", $postData['service_aftd_id']);  
foreach ($str_arra as $key=>$value) {
            $query4 = "INSERT INTO ntticketserviceaffected (`service_aftd_id`, `ticket_id`,`created_date`) VALUES ('".$value."', '".$ticketid."','".date('Y-m-d H:i:s')."')";
            $result4 = mysqli_query($conf, $query4);
        }
            //echo $activityid; exit; $query4 = "INSERT INTO ntticketserviceaffected (`service_aftd_id`, `ticket_id`,`created_date`) VALUES ('".$postData['service_aftd_id']."', '".$ticketid."','".date('Y-m-d H:i:s')."')";
    //echo $query3; exit;   
 $str_arr=array();         
    $str_arr = explode (",", $postData['site_id']);  
foreach ($str_arr as $key=>$value) {
     $query5 = "INSERT INTO ntticketsites(`ticket_id`, `site_id`,`created_date`) VALUES ('".$ticketid."','".$value."','".date('Y-m-d H:i:s')."')";
    $result5 = mysqli_query($conf, $query5);
}
    $str_arrn=array();         
    $str_arrn = explode (",", $postData['network']);  
foreach ($str_arrn as $key=>$value) {
     $query6 = "INSERT INTO ntticketnetworks(`ticket_id`, `network`,`created_date`) VALUES ('".$ticketid."','".$value."','".date('Y-m-d H:i:s')."')";          
    $result6 = mysqli_query($conf, $query6);
}
    $query3 = "INSERT INTO ntticket_incident (`status`, `impact`,`priority`,`urgency`,`origin`,`service_id`,`servicesubcategory_id`,`escalation_flag`,`escalation_reason`) VALUES ('new', '1','".$postData['urgency']."','".$postData['urgency']."','phone','".$postData['service_id']."','0','no',' ')";
    //echo $query3; exit;            
    $result3 = mysqli_query($conf, $query3);
    //echo $query;
    if($query3!=''){
        if ($result3) {
            
                //print_r(mysqli_fetch_all($result));
                $data = array('flag'=>true,'msg'=>'Data found');
                //$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_array($result, MYSQLI_ASSOC));
                /*
                    
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        if($name=='agent' || $name=='department'){
                            $temp = array($row[$name],$row[$number],$row[$id]);
                            array_push($data['info'], $temp);
                        }else{
                            $data['info'][$row[$name]] = $row[$number];
                        }
                    }   */              
                    //array_push($data['info'], $row);                  
                    $data['flag'] = true;
                    $data['msg'] = 'Data Added Successfully';                    
            }else{
                $data['msg'] = 'No records available';
            }
        
    }else{
        $data = array('flag'=>true,'msg'=>'Invalid parameter');
    }
    
}
echo json_encode($data);
?>