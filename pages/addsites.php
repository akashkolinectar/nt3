<?php 
//Under Admin Tool Add Site
require_once('../webservices/wbdb.php');
$data = array('flag'=>FALSE,'msg'=>"");

$sql = "select * from ntsites where is_active=1 AND site_name='" .$_POST['site_name']. "' OR site_code='".$_POST['site_id']."' ";
$result = $conf->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(2);
}else{

if($_POST['site_name']!=''){
	
	$query1 = "INSERT INTO `ntsites` (site_name,province,munciple,locality,lat,lng,site_code,vendor,responsible_area,priority,priority_comment,element_type,model,msc,mgw,bsc,rnc,phase,service_date,stage,sub_stage,start_date,end_date,parent_site,created_date,is_active) VALUES ('".$_POST['site_name']."','".$_POST['site_province']."','".$_POST['site_munciple']."','".$_POST['site_locality']."','".$_POST['site_lat']."','".$_POST['site_lng']."','".$_POST['site_id']."','".$_POST['site_vendor']."','".$_POST['site_responsible']."','".$_POST['site_priority']."','".$_POST['site_priority_comment']."','".$_POST['site_element_type']."','".$_POST['site_model']."','".$_POST['site_msc']."','".$_POST['site_mgw']."','".$_POST['site_bsc']."','".$_POST['site_rnc']."','".$_POST['site_phase']."','".date('Y-m-d',strtotime($_POST['site_service_date']))."','".$_POST['site_stage']."','".$_POST['site_sub_stage']."','".date('Y-m-d',strtotime($_POST['site_start_date']))."','".date('Y-m-d',strtotime($_POST['site_end_date']))."',0,'".date('Y-m-d H:i:s')."',1)";

	/*$query1 = "INSERT INTO `ntsites` VALUES ('','".$_POST['site_name']."','".$_POST['site_province']."','".$_POST['site_munciple']."','".$_POST['site_locality']."','".$_POST['site_lat']."','".$_POST['site_lng']."','".$_POST['site_id']."','".$_POST['site_vendor']."','".$_POST['site_responsible']."','".$_POST['site_priority']."','".$_POST['site_priority_comment']."','".$_POST['site_element_type']."','".$_POST['site_model']."','".$_POST['site_msc']."','".$_POST['site_mgw']."','".$_POST['site_bsc']."','".$_POST['site_rnc']."','".$_POST['site_phase']."','".$_POST['site_service_date']."','".$_POST['site_stage']."','".$_POST['site_sub_stage']."','".$_POST['site_start_date']."','".$_POST['site_end_date']."',0,'".date('Y-m-d H:i:s')."',1)";*/

	$result1 = mysqli_query($conf, $query1);

	if($result1){
		
		$site_id = mysqli_insert_id($conf);
		if(isset($_POST['site_network'])){
			foreach ($_POST['site_network'] as $key=>$value) {
				/*$query2 = "INSERT INTO `ntsitenetwork` VALUES ('','".$siteId."','".$value."','".date('Y-m-d H:i:s')."',1)";*/
				$query2 = "INSERT INTO `ntsitenetwork` (site_id,network,created_date,is_active) VALUES ('".$site_id."','".$value."','".date('Y-m-d H:i:s')."',1)";
				$result2 = mysqli_query($conf, $query2);
			}
		}

		if(!empty($_POST['sites'])){

			foreach ($_POST['sites'] as $key => $value) {
				$query3 = "UPDATE ntsites SET `parent_site`=".$site_id." WHERE `site_id`=".$value;
				$result3 = mysqli_query($conf, $query3);
				if(empty($allRec)){
					$query4 = "INSERT INTO `ntsitehistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES (".$value.",'AssignedParent','".$_SESSION['auth_user']."','Site','".$_POST['site_id']."','','','".date('Y-m-d H:i:s')."')";
					$result4 = mysqli_query($conf, $query4);
				}
			}
		}
		$queryhistory="INSERT INTO `ntsitehistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`)
		 VALUES ('".$site_id."','created','".$_SESSION['auth_user']."','".$sClass."','0','','','".date('Y-m-d H:i:s')."')";
		$result5 = mysqli_query($conf, $queryhistory);

	    $data = array('flag'=>TRUE,'msg'=>"Data Inserted");
	  
	}else{
		echo $conf->error;
	}
echo json_encode(1);
	//echo json_encode($site_id);
	//echo TRUE;
//header("Location: https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite"); 
}else{
	echo json_encode(-1);
    //header("Location: https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite");   
}
}

?>