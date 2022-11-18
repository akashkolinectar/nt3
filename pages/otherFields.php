<?php 
$obj = new otherFields;

switch ($_POST['field']) {
	case 'serviceAffectedDD': $obj::getServiceAffectedDropD();break;
	case 'getComponent': $obj::getComponent();break;
	case 'getSubReasons': $obj::getSubReasons();break;
	case 'currencyAdd': $obj::addCurrency();break;
	case 'getChildSites': $obj::getChildSites();break;
	case 'addTicketAttr': $obj::addTicketAttr();break;
	case 'editTicketAttr': $obj::editTicketAttr();break;
	case 'getAllSubReason': $obj::getAllSubReason();break;
	case 'removeReportDetail': $obj::removeReportDetail();break;
	case 'removeSites': $obj::removeSites();break;
	case 'exportSites': $obj::exportSites();break;
	case 'nextUpdate': $obj::nextUpdate();break;
	default: break;
}

class otherFields
{

	public function getServiceAffectedDropD(){

		$data = array("comptype"=>"<option value=''> -- Select One -- </option>",
					  "aftdnetwork"=>"<option value=''> -- Select One -- </option>");
		require_once('../webservices/wbdb.php');
		$affectedServices = "";

		if(!empty($_POST['affectedServices'])){
			$affectedServices = implode(",", $_POST['affectedServices']);
			
			/******* Affected Component Type *********/
			$query1 = "SELECT CT.aftd_comp_type,CT.aftd_comp_type_id FROM `ntserviceaftdcomptype` CTSA LEFT JOIN `ntaftd_comp_type` CT ON CT.`aftd_comp_type_id` = CTSA.`aftd_comp_type_id` WHERE CTSA.`is_active` = 1 AND CTSA.`service_aftd_id` IN (".$affectedServices.") GROUP BY CT.aftd_comp_type_id";
			$result1 = mysqli_query($conf, $query1);
			if($result1){
				$comptype = "<option value=''> -- Select One -- </option>";
				STATIC $comptypearr = array();
				while ($row = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
					if(!in_array($row['aftd_comp_type_id'], $comptypearr)){
					   $comptype .= "<option value='".$row['aftd_comp_type_id']."'>".$row['aftd_comp_type']."</option>";
					   array_push($comptypearr, $row['aftd_comp_type_id']);
					}
				}
				$data['comptype'] = $comptype;	
			}

			/******* Affected Network *********/
			$query2 = "SELECT AN.aftd_network_id,AN.aftd_network FROM `ntserviceaftdnetwork` NSA LEFT JOIN `ntaftd_network` AN ON AN.`aftd_network_id` = NSA.`aftd_network_id` WHERE NSA.`is_active` = 1 AND NSA.`service_aftd_id` IN (".$affectedServices.") GROUP BY AN.aftd_network_id";
			$result2 = mysqli_query($conf, $query2);		
			if($result2){
				$affectednetwork = "<option value=''> -- Select One -- </option>";
				STATIC $affectednetworkarr = array();
				while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
					if(!in_array($row['aftd_network_id'], $affectednetworkarr)){
						$affectednetwork .= "<option value='".$row['aftd_network_id']."'>".$row['aftd_network']."</option>";
						array_push($affectednetworkarr, $row['aftd_network_id']);
					}
				}
				$data['aftdnetwork'] = $affectednetwork;	
			}
		}
		echo json_encode($data);
	}

	public function getComponent(){

		/*$query1 = "SELECT CM.component,AN.aftd_network,CT.aftd_comp_type,PR.province FROM `ntcomponents` CM LEFT JOIN `ntaftd_network` AN ON AN.`aftd_network_id` = CM.`aftd_network_id` LEFT JOIN `ntaftd_comp_type` CT ON CT.`aftd_comp_type_id` = CM.`aftd_component_type_id` LEFT JOIN `ntsiteprovince` PR ON PR.province_id = CM.province_id LEFT JOIN  WHERE NSA.`is_active` = 1 AND NSA.`service_aftd_id` IN (".$affectedServices.")";*/
		$data = array('flag'=>FALSE,'components'=>'');
		require_once('../webservices/wbdb.php');

		$query1 = "SELECT CM.component,CM.component_id FROM `ntcomponents` CM WHERE CM.`is_active` = 1";

		if(isset($_POST['province']) && $_POST['province']!=''){
			$query1 .= " AND `province_id`='".$_POST['province']."'";
		}if(isset($_POST['provider']) && $_POST['provider']!=''){
			$query1 .= " AND `provider_id`='".$_POST['provider']."'";
		}if(isset($_POST['aftdnetwork']) && $_POST['aftdnetwork']!=''){
			$query1 .= " AND `aftd_network_id`='".$_POST['aftdnetwork']."'";
		}if(isset($_POST['affeced_component_type']) && $_POST['affeced_component_type']!=''){
			$query1 .= " AND `aftd_component_type_id`='".$_POST['affeced_component_type']."'";
		}
		$query1 .= " ORDER BY component ASC";

		$result1 = mysqli_query($conf, $query1);

		if($result1){
			$components = "";
			while ($row = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
				$components .= "<li style='list-style-type: none;'><input type='checkbox' name='components[]' id='component-".$row['component_id']."' value='".$row['component_id']."'><label for='component-".$row['component_id']."'>".$row['component']."</label></li>";
			}
			$data = array('flag'=>TRUE,'components'=>$components);
		}
		echo json_encode($data);
	}

	public function getSubReasons(){

		$data = array('flag'=>FALSE,'dropData'=>'');
		require_once('../webservices/wbdb.php');
		$dropData = "<option value=''>-- Select One --</option>";

		$query1 = "SELECT * FROM `ntsubreason` WHERE `is_active` = 1 AND `reason_id`='".$_POST['reason']."' ORDER BY sub_reason ASC";
		$result1 = mysqli_query($conf, $query1);
		header("Content-Type: text/html; charset=ISO-8859-1");
		if($result1){			
			while ($srData = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
				$dropData .= "<option value='".$srData['sub_reason_id']."'>".$srData['sub_reason']."</option>";
			}
		}
		//echo $dropData;
		$data = array('flag'=>TRUE,'dropData'=>$dropData);
		echo $dropData;
		//echo json_encode($data); // Working Nilesh 20 jan 2020
	}

	public function addCurrency(){

		$data = array('flag'=>FALSE,'dd'=>'');
		require_once('../webservices/wbdb.php');
		$enumData = '';

		// Get Pre Enum Values
		$query = "SHOW COLUMNS FROM ntcontract WHERE Field = 'cost_currency'";
		$result = mysqli_query($conf, $query);
		$ccEnum = mysqli_fetch_all($result, MYSQLI_ASSOC);
		preg_match("/^enum\(\'(.*)\'\)$/", $ccEnum[0]['Type'], $ccMatch);
	    $ccArr = explode("','", $ccMatch[1]);
	   	foreach ($ccArr as $key=>$val) {
	   		$enumData .= "'$val',";
	   	}
   		$enumData .= "'".$_POST['currency']."'";

	   	// Add Pre and New Enum Values
		$query1 = "ALTER TABLE `ntcontract` MODIFY COLUMN `cost_currency` enum($enumData) NOT NULL AFTER `cost`";
		$result1 = mysqli_query($conf, $query1);
		if($result1){

			// Get DropDown Enum Values
			$query2 = "SHOW COLUMNS FROM ntcontract WHERE Field = 'cost_currency'";
			$result2 = mysqli_query($conf, $query2);
			$dropData = '<option value="">-- select one --</option>';
			$costCurrencyEnum = mysqli_fetch_all($result2, MYSQLI_ASSOC);
			preg_match("/^enum\(\'(.*)\'\)$/", $costCurrencyEnum[0]['Type'], $costCurrencyMatch);
		    $costCurrencyArr = explode("','", $costCurrencyMatch[1]);
		   	foreach ($costCurrencyArr as $key=>$val) {
		   		$dropData .= "<option value='$val'>".ucfirst($val)."</option>";
		   	}
		   	$data = array('flag'=>TRUE,'dd'=>$dropData);
		}else{
			$data['dd'] = $conf->error;
		}
		echo json_encode($data);
	}

	public function getChildSites(){

		$data = array('flag'=>FALSE,'sites'=>array());
		require_once('../webservices/wbdb.php');
		$query1 = "SELECT site_id FROM `ntsites` WHERE `parent_site`=".$_POST['site_id']." AND `is_active`=1";
		//echo $query1;
		$result1 = mysqli_query($conf, $query1);
		if($result1){
			while($rows = mysqli_fetch_array($result1,MYSQLI_ASSOC)){
				array_push($data['sites'], $rows['site_id']);
			}
			$data['flag'] = TRUE;
		}
		echo json_encode($data);
	}

	public function addTicketAttr(){

		$data = array('flag'=>FALSE,'msg'=>'No data found','dd'=>'');
		require_once('../webservices/wbdb.php');

		if(isset($_POST['attr'])){

			$attr = $_POST['attr'];
			$table = ($attr=='sub_reason')? 'ntsubreason':"nt$attr";

			$query1 = "SELECT * FROM $table WHERE $attr='".$_POST['attrval']."' AND `is_active`=1";
			$result1 = $conf->query($query1);

			if(!$result1){
				$data['msg'] = $conf->error;
			}else{
				/*if($result1->num_rows>0){
					$data['msg'] = 'Duplicate '.ucwords($attr).' name';
				}else{*/
					if($attr=='sub_reason'){
						$query2 = "INSERT INTO $table (reason_id,$attr) VALUES ('".$_POST['subattrval']."','".$_POST['attrval']."')";
					}else{
						$query2 = "INSERT INTO $table ($attr) VALUES ('".$_POST['attrval']."')";
					}
					$result2 = $conf->query($query2);
					if($result2){
						/*$query3 = "SELECT * FROM $table WHERE `is_active`=1";
						$result3 = $conf->query($query3);
						$dd = "<option value=''> -- Select One -- </option>";
						while ($rows = mysqli_fetch_array($result3, MYSQLI_ASSOC)) {
							$str = $attr."_id";
							$dd .= "<option value='".$rows[$str]."'>".$rows[$attr]."</option>";
						}*/
						//echo $dd;
						$data = array('flag'=>TRUE,'msg'=>ucwords($attr).' Added Successfully');
					}
				//}
			}
		}
		echo json_encode($data);
	}

	public function editTicketAttr(){

		$data = array('flag'=>$_POST,'msg'=>'No data found','dd'=>'');
		require_once('../webservices/wbdb.php');

		if(isset($_POST['attr'])){

			$attr = $_POST['attr'];
			$table = ($attr=='sub_reason')? 'ntsubreason':"nt$attr";

			$query1 = "SELECT * FROM $table WHERE $attr='".$_POST['attrval']."' AND `is_active`=1";
			$result1 = $conf->query($query1);

			if(!$result1){
				$data['msg'] = $conf->error;
			}else{
				if($result1->num_rows>0){
					$data['msg'] = 'Duplicate '.ucwords($attr).' name';
				}else{
					if($attr=='sub_reason'){
						$query2 = "UPDATE $table SET sub_reason='".$_POST['attrval']."',reason_id = ".$_POST['subattrval']." WHERE sub_reason_id = ".$_POST['id'];
					}else{
						$idname = $attr.'_id';
						$query2 = "UPDATE $table SET $attr = '".$_POST['attrval']."' WHERE $idname = ".$_POST['id'];
					}
					$result2 = $conf->query($query2);
					if($result2){
						/*$query3 = "SELECT * FROM $table WHERE `is_active`=1";
						$result3 = $conf->query($query3);
						$dd = "<option value=''> -- Select One -- </option>";
						while ($rows = mysqli_fetch_array($result3, MYSQLI_ASSOC)) {
							$str = $attr."_id";
							$dd .= "<option value='".$rows[$str]."'>".$rows[$attr]."</option>";
						}*/
						//echo $dd;
						$data = array('flag'=>TRUE,'msg'=>ucwords($attr).' updated Successfully');
					}
				}
			}
		}
		echo json_encode($data);
	}

	public function getAllSubReason(){

		$DropDown = "";
		require_once('../webservices/wbdb.php');

		$query3 = "SELECT * FROM ntsubreason WHERE `is_active`=1 ORDER BY `sub_reason` ASC";
		$result3 = $conf->query($query3);
		$DropDown = "<select name='sub_reason_edt' id='sub_reason_edt' style='width:70%'><option value=''> -- Select One -- </option>";
		while ($rows = mysqli_fetch_array($result3, MYSQLI_ASSOC)) {			
			$DropDown .= "<option value='".$rows['sub_reason_id']."'>".$rows['sub_reason']."</option>";
		}
		$DropDown .= "</select>";
		echo $DropDown;
	}

	public function removeReportDetail(){

		$names = '';
		require_once('../webservices/wbdb.php');

		switch ($_POST['title']) {
			case 'contact': $query1 = "UPDATE ntreport_conf_contact SET is_active = 0 WHERE contact_id = ".$_POST['id']; 
				$query2 = "SELECT CONCAT(per.first_name,' ',cnt.name) as name,cnt.id FROM ntcontact cnt LEFT JOIN ntperson per ON cnt.id=per.id LEFT JOIN ntpriv_user_local usr ON usr.id=cnt.id LEFT JOIN ntreport_conf_contact rcc ON rcc.contact_id=cnt.id WHERE cnt.finalclass='Person' AND rcc.is_active=1 ORDER BY per.first_name ASC";
				break;
			case 'time': 
				$query1 = "UPDATE ntreport_conf_time SET is_active = 0 WHERE id = ".$_POST['id']; 
				$query2 = "SELECT `time` as name,`id` FROM ntreport_conf_time  WHERE is_active=1";
			break;
		}
		
		$result1 = $conf->query($query1);
		if($result1){
			$result2 = $conf->query($query2);
			while($aDBInfo = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
				//$names .= $aDBInfo['name']." <span onclick='removeContact(".$aDBInfo['id'].",\"".$_POST['title']."\")'><a href='javascript:void(0)'> Remove</a></span><br/><br/>";
				$names .= $aDBInfo['name']." <span class='".$aDBInfo['id']." ".$_POST['title']." reportDataRmove'><a href='javascript:void(0)'> Remove</a></span><br/><br/>";
			}
		}
		echo $names;
	}

	public function removeSites(){

		$flag = FALSE;
		if(isset($_POST['siteList'])){
			require_once('../webservices/wbdb.php');
			$sites = implode(",", $_POST['siteList']);
			$query1 = "UPDATE ntsites SET is_active = 0 WHERE site_id IN (".$sites.")";
			$result1 = $conf->query($query1);
			if($result1){
				$flag = TRUE;
			}
		}
		echo $flag;
	}

	public function exportSites(){

		$flag = FALSE;
		if(isset($_POST['siteList'])){
			require_once('../webservices/wbdb.php');
			$sites = implode(",", $_POST['siteList']);

	         $outputPath = '../webservices/SiteExport.csv';
			 $output = fopen($outputPath, "w");

	         fputcsv($output, array('Site ID', 'Site Name', 'Province', 'Munciple', 'Locality','Latitude ','Longitude ','Site Code','Vendor','Responsible Area','Priority','Priority Comment','Element Type','Model','MSC','MGW','BSC','RNC','Phase','Service Date','Stage','Sub Stage','Start Date','End_Date','Created Date'));  
	         $query = "SELECT ntsites.site_id,ntsites.site_name,ntsiteprovince.province,ntsitemunciple.munciple,nplocation.locationname,ntsites.lat,ntsites.lng,ntsites.site_code,ntsites.vendor,ntsites.responsible_area,ntsites.priority,ntsites.priority_comment,ntsites.element_type,ntsites.model,ntsites.msc,ntsites.mgw,ntsites.bsc,ntsites.rnc,ntsites.phase,ntsites.service_date,ntsites.stage,ntsites.sub_stage,ntsites.start_date,ntsites.end_date,ntsites.created_date FROM ntsites join ntsiteprovince on ntsites.province=ntsiteprovince.province_id left join ntsitemunciple on ntsites.munciple=ntsitemunciple.munciple_id left join nplocation on ntsites.locality=nplocation.locationid WHERE ntsites.is_active=1 AND site_id IN (".$sites.")";
	         $result = $conf->query($query); 
	         while($row =mysqli_fetch_array($result, MYSQLI_ASSOC))  
	         {  
	              fputcsv($output, $row);  
	         }  
	         fclose($output);  
	         if($result){
					$flag = TRUE;
				}
			}
			header('Content-Type: application/json');
		echo json_encode(
		    [
		        "filename" => basename($outputPath),
		    ]
		);

	}

	public function nextUpdate(){

		$flag = FALSE;
		if(isset($_POST['date']) && isset($_POST['id']) && isset($_POST['time'])){
			require_once('../webservices/wbdb.php');;
			$dateTime = date("Y-m-d H:i:s",strtotime($_POST['date'].' '.$_POST['time']));
			$query1 = "UPDATE ntticket SET next_update = '".$dateTime."' WHERE id = ".$_POST['id'];
			$result1 = $conf->query($query1);
			if($result1){
				$flag = TRUE;
			}
		}
		echo $flag;
	}
}
?>