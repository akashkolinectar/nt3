<?php 
date_default_timezone_set('Africa/Luanda');
require_once('../webservices/wbdb.php');

$activity_arr['province']='';
$activity_arr = $_POST;
$activity_arr['site_munciple']='';
$activity_arr['location']='';
$repto='';
if($activity_arr['extreportedto']!=''){
  $repto=$activity_arr['extreportedto'];
}
if($activity_arr['reportedto']!=''){
  $repto=$activity_arr['reportedto'];
}
$emp='';
if($activity_arr['extemployee']!=''){
  $emp=$activity_arr['extemployee'];
}
if($activity_arr['employee']!=''){
  $emp=$activity_arr['employee'];
}

$fuel_filled = 0;
if($activity_arr['fuel_filled']!=''){
  $fuel_filled=$activity_arr['fuel_filled'];
}
$fuel_found = 0;
if($activity_arr['fuel_found']!=''){
  $fuel_found=$activity_arr['fuel_found'];
}

$sql = "select * from npactivity where description='" . $activity_arr['description'] . "' AND status=1";
$result = $conf->query($sql);

if (false) {
//if ($result->num_rows > 0) {
    echo json_encode(2);//already exist
    
}else{

  if($activity_arr['description']!=''){

    $query1 = "INSERT INTO npactivity(description,province,munciple,selectedreason,reason,fuel_found,fuel_filled,location,accesstype,provider,service,movicelarea,employee,extemployee,reportedto,extreportedto,created_date,result)VALUES('".$activity_arr['description']."','".$activity_arr['province']."','".$activity_arr['site_munciple']."','".$activity_arr['selectedreason']."','".$activity_arr['reason']."','".$fuel_found."','".$fuel_filled."','".$activity_arr['location']."','".$activity_arr['accesstype']."','".$activity_arr['provider']."','".$activity_arr['service']."','".$activity_arr['movicelarea']."','".$activity_arr['employee']."','".$activity_arr['extemployee']."','".$activity_arr['reportedto']."','".$activity_arr['extreportedto']."','".date('Y-m-d H:i:s')."','Completed')";

    if(mysqli_query($conf, $query1)){
      $activityid = $conf->insert_id;
    }
    
    //$pad = (strlen($activityid)>3)? "LPAD('".$activityid."', 1, '0')":(strlen($activityid)>2)? "LPAD('".$activityid."', 2, '0')":(strlen($activityid)>1)? "LPAD('".$activityid."', 3, '0')":"LPAD('".$activityid."', 4, '0')";
    //$pad = (strlen($activityid)>3)? str_pad($activityid, 1, '0', STR_PAD_LEFT):(strlen($activityid)>2)? str_pad($activityid, 2, '0', STR_PAD_LEFT):(strlen($activityid)>1)? str_pad($activityid, 3, '0', STR_PAD_LEFT):str_pad($activityid, 4, '0', STR_PAD_LEFT);

    $queryu = "Update npactivity set actvitycode=CONCAT('NDR-',LPAD('".$activityid."', 6, '0')) where activityid='".$activityid."'";
    //$queryu = "Update npactivity set actvitycode='".$pad."' where activityid='".$activityid."'";
    $resultu = mysqli_query($conf, $queryu);
    if(!empty($activity_arr['sites'])){

        foreach ($activity_arr['sites'] as $key => $value) {
          $query3 = "INSERT INTO `ntactivitysite`(`activity_id`,`site_id`,`is_active`)VALUES('".$activityid."','".$value."','1')";
          $result3 = mysqli_query($conf, $query3);
          if(empty($allRec)){
            $query4 = "INSERT INTO `npctivityhistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES ('".$value."','AssignedParent','".$_SESSION['auth_user']."','Activity','".$activityid."','','','".date('Y-m-d H:i:s')."')";
            $result4 = mysqli_query($conf, $query4);
          }
        }
    }

    $queryhistory="INSERT INTO `npctivityhistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`)
     VALUES ('".$activityid."','created','".$_SESSION['auth_user']."','Activity','".$activityid."','','','".date('Y-m-d H:i:s')."')";

    $result5 = mysqli_query($conf, $queryhistory);
    $query2 = "INSERT INTO acthistory(activityid,Date,user,changes)VALUES('".$activityid."','".date('Y-m-d H:i:s')."','Admin','Add NDR')";

    $result2 = mysqli_query($conf, $query2);
    if($result2){

        /*********************** Send Mail Employee ********************************/
        $sqlemail = "select * from ntcontact join ntperson on ntcontact.id=ntperson.id where ntcontact.id='" .$emp. "'";
        $results = $conf->query($sqlemail);

       /* if ($results->num_rows > 0) {
          while($row = mysqli_fetch_array($results)){
            $to = "".$row['email']."";
            $subject = "NDR [".date('d-M-Y H:i:s')."]";

            $headers = 'From: nt3system@movicel.co.ao' . "\r\n" .
                          'Reply-To: nt3system@movicel.co.ao' . "\r\n" .
                          'X-Mailer: PHP/' . phpversion();
            $headers .= "MIME-Version: 1.0\r\n"
                          ."Content-Type: multipart/mixed; boundary=\"1a2a3a\"";
            $message .= "If you can see this MIME than your client doesn't accept MIME types!\r\n"
                          ."--1a2a3a\r\n";

            $message .= "Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
                              ."Content-Transfer-Encoding: 7bit\r\n\r\n"
                              ."Olá, <br/> Novo NDR ".$activity_arr['description']." é criado agora. Aguarde a aprovação para prosseguir."
                              ."<br/><p><b>Obrigado!</b></p> \r\n"
                              ."--1a2a3a\r\n";
            mail($to, $subject, $message, $headers);
          }
        }*/

        /*********************** Send Mail Manager ********************************/
        $sqlemailr = "select * from ntcontact join ntperson on ntcontact.id=ntperson.id where ntcontact.id='" .$repto. "'";
        $resultr = $conf->query($sqlemailr);

        /*if ($resultr->num_rows > 0) {
          while($rowr = mysqli_fetch_array($resultr)){
            $to = "".$rowr['email']."";
            $subject = "NDR [".date('d-M-Y H:i:s')."]";
            
            $headers = 'From: nt3system@movicel.co.ao' . "\r\n" .
                          'Reply-To: nt3system@movicel.co.ao' . "\r\n" .
                          'X-Mailer: PHP/' . phpversion();
            $headers .= "MIME-Version: 1.0\r\n"
                          ."Content-Type: multipart/mixed; boundary=\"1a2a3a\"";
            $message .= "If you can see this MIME than your client doesn't accept MIME types!\r\n"
                          ."--1a2a3a\r\n";

            $message .= "Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
                              ."Content-Transfer-Encoding: 7bit\r\n\r\n"
                              ."Olá, <br/> Novo NDR ".$activity_arr['description']." é criado agora. valide a notificação de falha na entrega para continuar a tarefa.<br/> Link: https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity"
                              ."<br/><p><b>Obrigado!</b></p> \r\n"
                              ."--1a2a3a\r\n";
            mail($to,$subject,$message,$headers);
          }
        }*/

      echo json_encode(1);
    }else{
      echo json_encode(0);
    }
  }else{
    echo json_encode(-1);
  }

}

?>
