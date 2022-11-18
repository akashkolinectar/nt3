<?php
//Include database configuration file
require_once('../webservices/wbdb.php');
$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
//EDIT ProvinCE
if(isset($rowid['province_edit_id'])!=''){
	$query = $conf->query("SELECT * FROM ntsiteprovince WHERE province_id = '".$rowid['province_edit_id']."' AND is_active=1 ");
	$rowCount = $query->num_rows;	
	if($rowCount > 0){		
		while($row = $query->fetch_assoc()){			
			echo json_encode(array("jprovince"=>$row['province']));			
		}
	}
	/*else{
		echo '<option value="-1">---NA---</option>';
	}*/
}
//*****Edit Munciple****
if(isset($rowid['munciple_edit_id'])!=''){
	$query = $conf->query("SELECT * FROM ntsitemunciple WHERE munciple_id = ".$rowid['munciple_edit_id']." AND is_active=1 ");
	$rowCount = $query->num_rows;	
	if($rowCount > 0){		
		while($row = $query->fetch_assoc()){			
			echo json_encode(array("jmunciple"=>$row['munciple']));			
		}
	}
	/*else{
		echo '<option value="-1">---NA---</option>';
	}*/
}
//*****Edit Locality*** */
if(isset($rowid['locality_edit_id'])!=''){
	$query = $conf->query("SELECT * FROM nplocation WHERE locationid = ".$rowid['locality_edit_id']." AND is_active=1 ");
	$rowCount = $query->num_rows;	
	if($rowCount > 0){		
		while($row = $query->fetch_assoc()){			
			echo json_encode(array("jlocality"=>$row['locationname']));			
		}
	}
	/*else{
		echo '<option value="-1">---NA---</option>';
	}*/
}



if(isset($rowid['province_id'])!=''){

	//Get all state data
	//$ss = $rowid['province_id'];
	$query = $conf->query("SELECT * FROM ntsitemunciple WHERE province_id = '".$rowid['province_id']."' AND is_active=1");

	//Count total number of rows
	$rowCount = $query->num_rows;

	//Display states list
	$provDd = "<option value=''> -- Select One -- </option>";
	if($rowCount > 0){
		//echo '<option value="">Select Munciple </option>';
		while($row = $query->fetch_assoc()){
			$provDd .= '<option value="'.$row['munciple_id'].'">'.$row['munciple'].'</option>';
		}
	}
	/*else{
		echo '<option value="-1">---NA---</option>';
	}*/
		echo $provDd;

}
if(isset($rowid['munciple_id'])!=''){
    //echo  $rowid['munciple_id'];
	//Get all state data
	//$ss = $rowid['munciple_id'];
	$query = $conf->query("SELECT * FROM nplocation WHERE munciple_id = ".$rowid['munciple_id']." AND is_active=1");

	//Count total number of rows
	$rowCount = $query->num_rows;

	$munDd = "<option value=''> -- Select One -- </option>";
	//Display states list
	if($rowCount > 0){
		while($row = $query->fetch_assoc()){
			$munDd .= '<option value="'.$row['locationid'].'">'.$row['locationname'].'</option>';
		}
	}
	echo $munDd;

}
if(!empty($rowid['provider_id'])){

	//Get all state data
	
	$query = $conf->query("SELECT (ntservice.id) as id,name FROM ntservice join ntlnkprovidercontracttoservice on ntservice.id=ntlnkprovidercontracttoservice.service_id  WHERE ntlnkprovidercontracttoservice.providercontract_id= ".$rowid['provider_id']."");

	//Count total number of rows
	$rowCount = $query->num_rows;
	
	$provDd = "<option value=''> -- Select One -- </option>";
	//Display states list
	if($rowCount > 0){
		//echo '<option value="">Select Munciple </option>';
		while($row = $query->fetch_assoc()){
			$provDd .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
		}
	}
	echo $provDd;
	/*else{
			echo '<option value="-1">---NA---</option>';
	}*/

}
if(!empty($rowid['orgid'])){

	//Get all state data
	
	$query = $conf->query("SELECT (ntcontact.id) as id,(ntcontact.name) as name,(ntperson.first_name) as first_name FROM ntcontact join ntperson on ntcontact.id=ntperson.id WHERE ntcontact.finalclass ='Person' AND ntcontact.org_id= ".$rowid['orgid']."");

	//Count total number of rows
	$rowCount = $query->num_rows;
	
	$orgemp = "<option value=''> -- Select One -- </option>";
	//Display states list
	if($rowCount > 0){
		//echo '<option value="">Select Munciple </option>';
		while($row = $query->fetch_assoc()){
			//$orgemp .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
			$orgemp .= '<option value="'.$row['id'].'">'.$row['first_name'].' '.$row['name'].'</option>';
		}
	}
	echo $orgemp;
	/*else{
			echo '<option value="-1">---NA---</option>';
	}*/

}
?>