<?php 

require_once('../webservices/wbdb.php');

//$data = array('flag'=>FALSE,'msg'=>"");

if($_POST['description']!=''){
	$sql = "select * from npactivity where activityid!='".$_POST['activityid']."' AND description='" .$_POST['description']. "'";

$result = $conf->query($sql);
//print_r($result);
// var_dump($result);

if ($result->num_rows > 0) {
    header("Location: https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity"); 
    
} else {

/*echo "<pre>";
print_r($_POST['sites']);*/

/*$query1 = "Update npactivity set description='".$_POST['description']."',province='".$_POST['province']."',reason='".$_POST['reason']."',fuel_found='".$_POST['Encontrado']."',fuel_filled='".$_POST['Abastecido']."',munciple='".$_POST['munciple']."',location='".$_POST['location']."',accesstype='".$_POST['accesstype']."',provider='".$_POST['provider']."',service='".$_POST['service']."',movicelarea='".$_POST['movicelarea']."',employee='".$_POST['employee']."',extemployee='".$_POST['extemployee']."',reportedto='".$_POST['reportedto']."',extreportedto='".$_POST['extreportedto']."' where activityid='".$_POST['activityid']."'";*/

$query1 = "Update npactivity set description='".$_POST['description']."',province='".$_POST['province']."',reason='".$_POST['reason']."',fuel_found='".$_POST['Encontrado']."',fuel_filled='".$_POST['Abastecido']."',accesstype='".$_POST['accesstype']."',provider='".$_POST['provider']."',service='".$_POST['service']."',movicelarea='".$_POST['movicelarea']."',employee='".$_POST['employee']."',extemployee='".$_POST['extemployee']."',reportedto='".$_POST['reportedto']."',extreportedto='".$_POST['extreportedto']."' where activityid='".$_POST['activityid']."'";

  $result1 = mysqli_query($conf, $query1);
  $preSites = array();
  $delSiteList = "SELECT site_id FROM `ntactivitysite` WHERE activity_id=".$_POST['activityid']." AND is_active=1";
  $resultSiteList = mysqli_query($conf, $delSiteList);
  if($resultSiteList){
    if($resultSiteList->num_rows>0){
      while($sRows = mysqli_fetch_array($resultSiteList,MYSQLI_ASSOC)){
        array_push($preSites, $sRows['site_id']);
      }
    }
  }

  /*$interSites = array();
  if(!empty($_POST['sites'])){
    $interSites = array_intersect($_POST['sites'], $preSites);
    $diffSites = array_diff($preSites,$_POST['sites']);
  }*/

/*echo "<pre>";
print_r($preSites);
echo "<pre>";
print_r($_POST['sites']);
exit();*/
  /*if(!empty($_POST['sites']) && count($preSites)!=count($_POST['sites'])){
    foreach ($preSites as $key => $value) {
      
      $query4 = "INSERT INTO `npctivityhistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES ('".$value."','revoked','".$_SESSION['auth_user']."','Activity','".$_POST['activityid']."','','','".date('Y-m-d H:i:s')."')";
      $result4 = mysqli_query($conf, $query4);
    }
  }else if(!empty($preSites)){
    foreach ($preSites as $key => $value) {
      $delSite = "DELETE FROM `ntactivitysite` WHERE activity_id=".$_POST['activityid']." AND site_id=".$value;
      $resultSite = mysqli_query($conf, $delSite);

      $query4 = "INSERT INTO `npctivityhistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES ('".$value."','revoked','".$_SESSION['auth_user']."','Activity','".$_POST['activityid']."','','','".date('Y-m-d H:i:s')."')";
      $result4 = mysqli_query($conf, $query4);
    }
  }*/
  
$delSite = "DELETE FROM `ntactivitysite` WHERE activity_id=".$_POST['activityid'];
$resultSite = mysqli_query($conf, $delSite);

 if(!empty($_POST['sites'])){

      foreach ($_POST['sites'] as $key => $value) {
        $query3 = "INSERT INTO `ntactivitysite`(`activity_id`,`site_id`,`is_active`)VALUES('".$_POST['activityid']."','".$value."','1')";
        $result3 = mysqli_query($conf, $query3);
       
        if(empty($allRec)){
          $query4 = "INSERT INTO `npctivityhistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES ('".$value."','modified','".$_SESSION['auth_user']."','Activity','".$_POST['activityid']."','','','".date('Y-m-d H:i:s')."')";
          $result4 = mysqli_query($conf, $query4);
        }
      }
  }
// $result1 = mysqli_query($conf, $query1);
$query2 = "INSERT INTO acthistory(activityid,Date,user,changes)VALUES('".$_POST['activityid']."','".date('Y-m-d H:i:s')."','Admin','Update NDR')";
$result2 = mysqli_query($conf, $query2);
//end new code
}

echo TRUE;
//echo "Hello";
//echo '<script>alert(Activity Updated Successfully!)</script>';
//header("Location: https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity"); 
}else{
echo FALSE;
   // header("Location: https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity");   
}
//if($result1){
//	if(isset($_POST['site_network'])){
//		$siteId = mysqli_insert_id($conf);
//		foreach ($_POST['site_network'] as $key=>$value) {
//			$query2 = "INSERT INTO `ntsitenetwork` VALUES ('','".$siteId."','".$value."','".date('Y-m-d H:i:s')."',1)";
//			$result2 = mysqli_query($conf, $query2);
//		}
//	}
//	$data = array('flag'=>TRUE,'msg'=>"Data Inserted");
//}
////$data = $_POST;
//echo json_encode($data);

?>