<?php 

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
$sqlemail = "select * from npactivity join ntcontact nc1 on npactivity.extemployee=nc1.id OR npactivity.employee=nc1.id left JOIN ntperson np1 on npactivity.extemployee=np1.id OR npactivity.employee=np1.id where npactivity.status='1' AND npactivity.activityid='".$rowid['actid']."' GROUP BY npactivity.activityid ORDER BY npactivity.activityid DESC";

$results = $conf->query($sqlemail);
//var_dump($results); exit;

/*if ($results->num_rows > 0) {
  while($row = mysqli_fetch_array($results)){
  	$fname= "".$row['first_name']."";
 
	$to = "".$row['email']."";
    $subject = "NDR Status ".date('d-M-Y H:i:s')."";
    $headers = 'From: nt3system@movicel.co.ao' . "\r\n" .
                  'Reply-To: nt3system@movicel.co.ao' . "\r\n" .
                  'X-Mailer: PHP/' . phpversion();
    $headers .= "MIME-Version: 1.0\r\n"
                  ."Content-Type: multipart/mixed; boundary=\"1a2a3a\"";
    $message .= "If you can see this MIME than your client doesn't accept MIME types!\r\n"
                  ."--1a2a3a\r\n";

    $message .= "Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
                      ."Content-Transfer-Encoding: 7bit\r\n\r\n"
                      ."Olá, <br/> NDR ".$row['description']." is atualizado em ".date('d-M-Y H:i:s')." de ".$fname.".Verifique o link abaixo para obter mais informações :https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity"
                      ."<br/><p><b>Obrigado!</b></p> \r\n"
                      ."--1a2a3a\r\n";
    mail($to,$subject,$message,$headers);
  }
}*/
//Reporting Manager
$sqlemailr = "select * from npactivity join ntcontact nc1 on npactivity.extreportedto=nc1.id OR npactivity.reportedto=nc1.id left JOIN ntperson np1 on npactivity.extreportedto=np1.id OR npactivity.reportedto=np1.id where npactivity.status='1' AND npactivity.activityid='".$rowid['actid']."' GROUP BY npactivity.activityid ORDER BY npactivity.activityid DESC";

$resultr = $conf->query($sqlemailr);
//var_dump($results); exit;

/*if ($resultr->num_rows > 0) {
  while($row = mysqli_fetch_array($resultr)){
 
  	$to = "".$row['email']."";
    $subject = "NDR Status ".date('d-M-Y H:i:s')."";
    $headers = 'From: nt3system@movicel.co.ao' . "\r\n" .
                  'Reply-To: nt3system@movicel.co.ao' . "\r\n" .
                  'X-Mailer: PHP/' . phpversion();
    $headers .= "MIME-Version: 1.0\r\n"
                  ."Content-Type: multipart/mixed; boundary=\"1a2a3a\"";
    $message .= "If you can see this MIME than your client doesn't accept MIME types!\r\n"
                  ."--1a2a3a\r\n";

    $message .= "Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
                      ."Content-Transfer-Encoding: 7bit\r\n\r\n"
                      ."Olá, <br/> NDR ".$row['description']." is atualizado em ".date('d-M-Y H:i:s')." de ".$fname.".Verifique o link abaixo para obter mais informações :https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity"
                      ."<br/><p><b>Obrigado!</b></p> \r\n"
                      ."--1a2a3a\r\n";
    mail($to,$subject,$message,$headers);
  }
}*/

//echo $rowid['activityid'];exit;
if(isset($rowid['actid'])!=''){
$query1 = "Update npactivity set result='".$rowid['result']."' where activityid='".$rowid['actid']."'";
//echo $query1;
$result1 = mysqli_query($conf, $query1);
return;
$query2 = "INSERT INTO acthistory(activityid,Date,user,changes)VALUES('".$rowid['actid']."','".date('Y-m-d H:i:s')."','Admin','NDR Status Updated')";
$result2 = mysqli_query($conf, $query2);
return;
}
//echo "Hello";
//echo '<script>alert(Activity Updated Successfully!)</script>';
//header("Location: http://localhost/nt3/pages/UI.php?c%5Bmenu%5D=activity"); 


?>