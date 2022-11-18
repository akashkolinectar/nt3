<?php 

require_once('../webservices/wbdb.php');
date_default_timezone_set('Africa/Luanda');
$data = array('flag'=>FALSE,'msg'=>"");
$query1 = "INSERT INTO `ntsites` VALUES ('','".$_POST['site_name']."','".$_POST['site_province']."','".$_POST['site_munciple']."','".$_POST['site_locality']."','".$_POST['site_lat']."','".$_POST['site_lng']."','".$_POST['site_id']."','".$_POST['site_vendor']."','".$_POST['site_responsible']."','".$_POST['site_priority']."','".$_POST['site_priority_comment']."','".$_POST['site_element_type']."','".$_POST['site_model']."','".$_POST['site_msc']."','".$_POST['site_mgw']."','".$_POST['site_bsc']."','".$_POST['site_rnc']."','".$_POST['site_phase']."','".$_POST['site_service_date']."','".$_POST['site_stage']."','".$_POST['site_sub_stage']."','".$_POST['site_start_date']."','".$_POST['site_end_date']."',0,'".date('Y-m-d H:i:s')."',1)";
//echo $query1;
$result1 = mysqli_query($conf, $query1);

if($result1){
	if(isset($_POST['site_network'])){
		$siteId = mysqli_insert_id($conf);
		foreach ($_POST['site_network'] as $key=>$value) {
			$query2 = "INSERT INTO `ntsitenetwork` VALUES ('','".$siteId."','".$value."','".date('Y-m-d H:i:s')."',1)";
			$result2 = mysqli_query($conf, $query2);
		}
	}
	$data = array('flag'=>TRUE,'msg'=>"Data Inserted");
}
//$data = $_POST;
echo json_encode($data);

?>