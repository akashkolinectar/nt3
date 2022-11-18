<?php 
$obj = new addSiteAttr;

switch ($_POST['attr']) {
	case 'responsible': $obj::addSiteAttributes('ntsiteresponsible','responsible_area'); break;
	case 'priority': $obj::addSiteAttributes('ntsitepriority','priority'); break;
	case 'munciple': $obj::addSiteAttributes('ntsitemunciple','munciple'); break;
	case 'element_type': $obj::addSiteAttributes('ntsiteelementtype','element_type'); break;
	case 'vendor': $obj::addSiteAttributes('ntsitevendor','vendor'); break;
	case 'model': $obj::addSiteAttributes('ntsitemodel','model'); break;
	case 'msc': $obj::addSiteAttributes('ntsitemsc','msc'); break;
	case 'mgw': $obj::addSiteAttributes('ntsitemgw','mgw'); break;
	case 'bsc': $obj::addSiteAttributes('ntsitebsc','bsc'); break;
	case 'rnc': $obj::addSiteAttributes('ntsiternc','rnc'); break;
	case 'phase': $obj::addSiteAttributes('ntsitephase','phase'); break;
	case 'province': $obj::addSiteAttributes('ntsiteprovince','province'); break;
	//case 'commission': $obj::addSiteAttributes('ntsitecommission','commission'); break;
	case 'stage': $obj::addSiteAttributes('ntsitestage','stage'); break;
	case 'sub_stage': $obj::addSiteAttributes('ntsitesubstage','sub_stage'); break;
	case 'typology': $obj::modifyTypology('nttypology'); break;
	case 'overview': $obj::getOverview(); break;
	case 'FunctionalCIAdd': $obj::addFunctionalCI(); break;
	case 'FunctionalCIEdit': $obj::editFunctionalCI(); break;
	case 'siteInfo': $obj::getSiteInfo(); break;
	case 'search_site': $obj::searchSites(); break;
	case 'masterTable': $obj::getMasterTable(); break;
	case 'getMunciple': $obj::getMunciple(); break;
	case 'locality': $obj::addSiteAttributes('nplocation','locationname'); break;
	case 'getLocality': $obj::getLocality(); break;
	case 'getSites': $obj::getSites(); break;
	case 'getSingleSites': $obj::getSingleSites(); break;
	
	default:
		# code...
		break;
}

class addSiteAttr
{
	
	public function addSiteAttributes($tbl,$col1){
		
		require_once('../webservices/wbdb.php');
		$data = array('flag'=>FALSE,'dropdd'=>"","msg"=>'');
		$attr = $_POST['attr_val'];
		
		$queryChk = "SELECT * FROM $tbl WHERE `$col1` = '$attr' AND is_active = 1";
		$resultChk = mysqli_query($conf, $queryChk);

		if($resultChk->num_rows==0){

			if($_POST['sub_attr']!="NA" && $_POST['sub_attr_col']!="NA"){
				$query1 = "INSERT INTO $tbl (`".$_POST['sub_attr_col']."`,`".$col1."`,`is_active`,`created_date`) VALUES ('".$_POST['sub_attr']."','$attr',1,'".date('Y-m-d H:i:s')."')";
			}else{
				$query1 = "INSERT INTO $tbl (`".$col1."`,`is_active`,`created_date`) VALUES ('$attr',1,'".date('Y-m-d H:i:s')."')";
			}
			//$query1 = "INSERT INTO $tbl (`".$col1."`,`is_active`,`created_date`) VALUES ('$attr',1,'".date('Y-m-d H:i:s')."')";
			$result1 = mysqli_query($conf, $query1);
			if($result1){

				if($_POST['sub_attr']!="NA" && $_POST['sub_attr_col']!="NA"){
					$dropdd = "<option value=''> -- Select One --</option>";
					$query2 = "SELECT * FROM $tbl WHERE `is_active` = 1 AND `".$_POST['sub_attr_col']."`=".$_POST['sub_attr']." ORDER BY $col1 DESC";
					$result2 = mysqli_query($conf, $query2);
					while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
						$selected = ($row[$col1]==$attr)? "selected='selected'":"";
						$dropdd .= "<option value='".$row[$col1]."' ".$selected.">".$row[$col1]."</option>";
					}
				}else{

					$dropdd = "<option value=''> -- Select One --</option>";
					$query2 = "SELECT * FROM $tbl WHERE `is_active` = 1 ORDER BY $col1 DESC";
					$result2 = mysqli_query($conf, $query2);
					while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
						$selected = ($row[$col1]==$attr)? "selected='selected'":"";
						$dropdd .= "<option value='".$row[$col1]."' ".$selected.">".$row[$col1]."</option>";
					}
				}
				$data = array('flag'=>TRUE,'dropdd'=>$dropdd,'attr'=>'site_'.$_POST['attr']);
			}
		}else{
			$data['msg'] = "$col1 already exist";
		}
		echo json_encode($data);
	}

	public function modifyTypology($tbl){

		$data = FALSE;
		require_once('../webservices/wbdb.php');
		$query1 = "UPDATE $tbl SET `name`='".$_POST['typologyName']."' WHERE `id`=".$_POST['typology']." AND `finalclass`='".$_POST['finalclass']."'";
		$result1 = mysqli_query($conf, $query1);
		if($result1){
			$data = TRUE;
		}
		echo json_encode($data);
	}

	public function getOverview(){

		$data = array("flag"=>FALSE);
		//session_start();
		if($_POST['sessAction']=="set"){
			/*$_SESSION['pre_days'] = $_POST['preSess'];
			$_SESSION['new_days'] = 365;
			$data = array('new_days'=>$_SESSION['new_days'],'pre_days'=>$_SESSION['pre_days']);*/

			setcookie('pre_days', $_POST['preDay'], time() + (60 * 1000), "/");
			setcookie('new_days', $_POST['newDay'], time() + (60 * 1000), "/");
			$data = array("flag"=>TRUE,'new_days'=>$_COOKIE['new_days'],'pre_days'=>$_COOKIE['pre_days']);
		}else{
			unset($_COOKIE['pre_days']);
			unset($_COOKIE["new_days"]);
			$data = array("flag"=>TRUE);
		}
		
		echo json_encode($data);
	}

	public function addFunctionalCI(){

		require_once('../webservices/wbdb.php');
		$data = array('flag'=>FALSE);

		if(isset($_POST['organization']) && isset($_POST['ciname']) && isset($_POST['finalclass'])){
			if($_POST['finalclass']=="Server"){
				$query1 = "INSERT INTO ntfunctionalci (`name`,`description`,`org_id`,`business_criticity`,`finalclass`) VALUES ('".$_POST['ciname']."','',".$_POST['organization'].",'low','".$_POST['finalclass']."')";
				$result1 = mysqli_query($conf, $query1);
				if($result1){
					$data = array('flag'=>TRUE);
				}
			}
		}

		echo json_encode($data);
	}

	public function editFunctionalCI(){

		require_once('../webservices/wbdb.php');
		$data = array('flag'=>FALSE);
		if(isset($_POST['cid']) && isset($_POST['ciname'])){
			$query1 = "UPDATE `ntfunctionalci` SET `name`='".$_POST['ciname']."' WHERE `id`=".$_POST['cid'];
			$result1 = mysqli_query($conf, $query1);
			if($result1){
				$data = array('flag'=>TRUE);
			}
		}
		echo json_encode($data);
	}

	public function getSiteInfo(){

		require_once('../webservices/wbdb.php');
		$data = array('flag'=>FALSE,'info'=>array());
		
		if(isset($_POST['site_id'])){

			//setlocale(LC_ALL, 'pt_PT');
			//***MAHESH added new query join munciple,province & locality
			// $query1 = "SELECT * FROM `ntsites` WHERE `is_active` = 1 AND `site_id`=".$_POST['site_id'];
			$query1 = "SELECT * FROM ntsites join ntsiteprovince on ntsites.province=ntsiteprovince.province_id left join ntsitemunciple on ntsites.munciple=ntsitemunciple.munciple_id left join nplocation on ntsites.locality=nplocation.locationid WHERE ntsites.is_active=1 AND ntsites.site_id=".$_POST['site_id'];
			$result1 = mysqli_query($conf, $query1);
			$info = mysqli_fetch_all($result1, MYSQLI_ASSOC);

			$info[0]['province'] = iconv('UTF-8', 'ISO-8859-1//IGNORE', $info[0]['province']);

			$query2 = "SELECT * FROM `ntsitenetwork` WHERE `is_active` = 1 AND `site_id`=".$_POST['site_id'];
			$result2 = mysqli_query($conf, $query2);
			$str = "";

			foreach (mysqli_fetch_all($result2, MYSQLI_ASSOC) as $rows) {
				$str .= $rows['network']." ";
			}
			$info[0]['network'] = $str;

			//print_r($info);

			$data = array('flag'=>TRUE,'info'=>$info[0]);
		}

		echo json_encode($data);
	}

	public function searchSites(){

		require_once('../webservices/wbdb.php');
		$data = array('flag'=>FALSE,'info'=>array());
		if(isset($_POST['search'])){

			if($_POST['search']=='province'){
// Working Here
				$str = (string)$_POST['search_val'];
				$query1 = "SELECT * FROM `ntsites` WHERE `is_active` = 1 AND province LIKE \"".$str."\"";
				$allSitesList = mysqli_query($conf, $query1);

				//print_r($allSitesList);
				$siteList = "";
				//if($allSitesList){
					$i = 0;
					while ($aDBInfo = mysqli_fetch_array($allSitesList, MYSQLI_ASSOC)) {

						//print_r($aDBInfo);
						$siteList .= "<tr><td><input class=\"selection\" data-remote-id=\"".$aDBInfo['site_id']."\" data-link-id=\"\" data-unique-id=\"".$i."\" type=\"checkbox\" value=\"".$aDBInfo['site_id']."\" name=\"sites[]\" > </td><td><a href='javascript:void(0)' class='siteDetails' id='".$aDBInfo['site_id']."'>".$aDBInfo['site_code']."</td><td>".$aDBInfo['site_name']."</td><td>".$aDBInfo['province']."</td><td>".$aDBInfo['responsible_area']."</td></tr>";
							$i++;
					}
					//echo $siteList;
				/*}else{
					$siteList .= "<tr><td colspan='3' style='text-align: center;'>No sites available</td></tr>";
				}*/
			$data = array('flag'=>TRUE,'info'=>$siteList,"ss"=>$_POST);
			}			
		}
		echo json_encode($data);
	}
	public function getMasterTable(){
		require_once('../webservices/wbdb.php');		
		$attr_val = $_POST['attr_val'];
		$dropdd = "";
		switch ($attr_val) {
			case 'site_munciple': 
			$query1 = "SELECT * FROM ntsiteprovince WHERE `is_active` = 1 ORDER BY province ASC"; 
			$result1 = mysqli_query($conf, $query1);
			$dropdd .= "Province: <select name='master_province' class='province_id'><option value=''> -- Select One --</option>";
			while ($row = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
				$dropdd .= "<option value='".$row['province_id']."'>".$row['province']."</option>";
			}
			$dropdd .= "</select>";
			break;
			case 'site_locality': 
			$query1 = "SELECT * FROM ntsiteprovince WHERE `is_active` = 1 ORDER BY province ASC"; 
			$result1 = mysqli_query($conf, $query1);
			$dropdd .= "Province: <select name='master_province' class='province_id' id='province_loc'><option value=''> -- Select One --</option>";
			while ($row = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
				$dropdd .= "<option value='".$row['province_id']."'>".$row['province']."</option>";
			}
			$dropdd .= "</select>Munciple: <select name='master_munciple' class='munciple_id' id='munciple_loc'><option value=''> -- Select One --</option></select>"; 

			$dropdd .= '<script>$("#province_loc").on("change",function(){
							var pid = $(this).val();							
							$.ajax({
								url: "addSiteAttr.php",
								data: {"attr":"getMunciple","pid":pid,"subDrop":true},
								type: "POST",
								success: function(res){
									console.log(res);
									$(".munciple_id").html(res);
								}
							});	
						});</script>';
			break;
		}
		echo $dropdd;
	}

	public function getMunciple(){
		require_once('../webservices/wbdb.php');		
		$pid = $_POST['pid'];
		$dropdd = "";
		if(!isset($_POST['subDrop'])){
			$query = "SELECT * FROM ntsiteprovince WHERE `is_active` = 1 AND `province_id` = '".$pid."' ORDER BY province DESC";
			$result = mysqli_query($conf, $query);
			$info = mysqli_fetch_all($result, MYSQLI_ASSOC);
			$pid = $info[0]['province_id'];
		}
		$query1 = "SELECT * FROM ntsitemunciple WHERE `is_active` = 1 AND `province_id` = '".$pid."' ORDER BY munciple DESC"; 
		$result1 = mysqli_query($conf, $query1);
		$dropdd .= "<option value=''> -- Select One --</option>";
		while ($row = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
			if(!isset($_POST['subDrop'])){
				$muncipleVal = $row['munciple'];
			}else{
				$muncipleVal = $row['munciple_id'];
			}
			//$dropdd .= "<option value='".$muncipleVal."'>".$row['munciple']."</option>";
			$dropdd .= "<option value='".$row['munciple_id']."'>".$row['munciple']."</option>";
		}
		echo $dropdd;
	}

	public function getLocality(){
		require_once('../webservices/wbdb.php');		
		$mid = $_POST['mid'];

		$query = "SELECT * FROM ntsitemunciple WHERE `is_active` = 1 AND `munciple_id` = '".$mid."'";
		$result = mysqli_query($conf, $query);
		$info = mysqli_fetch_all($result, MYSQLI_ASSOC);
		$mid = $info[0]['munciple_id'];

		$dropdd = "";
		$query1 = "SELECT * FROM nplocation WHERE `is_active` = 1 AND `munciple_id` = '".$mid."' ORDER BY locationname DESC";
		$result1 = mysqli_query($conf, $query1);
		$dropdd .= "<option value=''> -- Select One --</option>";
		while ($row = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
			$dropdd .= "<option value='".$row['locationid']."'>".$row['locationname']."</option>";
		}
		echo $dropdd;
	}

	public function getSites(){
		require_once('../webservices/wbdb.php');
		$data = array('flag'=>FALSE,'info'=>'');
		$provinceId = "";
		
		//print_r($_POST['sites']);
		if(!empty($_POST['sites'])!=''){
			$ids = implode(',', $_POST['sites']);
			$query = "SELECT * FROM ntsites WHERE `is_active` = 1 AND `site_id` IN (".$ids.") OR `parent_site` IN (".$ids.")";
			$result = mysqli_query($conf, $query);
		
			if($result){
				if($result->num_rows>0){
					$data['flag'] = TRUE;
					//echo $result->num_rows;
					$i = 0; $sitesAdded = array();
					while ($aDBInfo = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

						if(!in_array($aDBInfo['site_id'], $sitesAdded)){
							
							array_push($sitesAdded, $aDBInfo['site_id']);
							$provinceQuery = "SELECT province FROM ntsiteprovince WHERE `province_id` = '".$aDBInfo['province']."'";
							$provinceResult = mysqli_query($conf, $provinceQuery);
							$provinceName = mysqli_fetch_all($provinceResult, MYSQLI_ASSOC);
							$data['info'] .= "<tr class='tr_".$aDBInfo['site_id']."'><td><input class=\"selection sitemaster\" data-remote-id=\"".$aDBInfo['site_id']."\" data-link-id=\"\" data-unique-id=\"".$i."\" type=\"checkbox\" value=\"".$aDBInfo['site_id']."\" name=\"removesites[]\"> <input type='hidden' name='sites[]' value='".$aDBInfo['site_id']."'> </td><td><a href='javascript:void(0)' class='siteDetails' id='".$aDBInfo['site_id']."'>".$aDBInfo['site_code']."</td><td>".$aDBInfo['site_name']."</td><td> ".$provinceName[0]['province']." </td><td>".$aDBInfo['responsible_area']."</td><td>".date('d M Y (h:i a)',strtotime($aDBInfo['created_date']))."</td></tr>";
							$provinceId = $aDBInfo['province'];
						}
						
						
						/*********** Child As Parent (If Selected site is parent site) *********************/
						$query2 = "SELECT * FROM ntsites WHERE `is_active` = 1 AND `parent_site` = ".$aDBInfo['site_id'];
						$result2 = mysqli_query($conf, $query2);
						if($result2){
							if($result2->num_rows>0){
								while ($aDBInfo2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {

									if(!in_array($aDBInfo2['site_id'], $sitesAdded)){
										
										array_push($sitesAdded, $aDBInfo2['site_id']);
										$provinceQuery = "SELECT province FROM ntsiteprovince WHERE `province_id` = '".$aDBInfo2['province']."'";
										$provinceResult = mysqli_query($conf, $provinceQuery);
										$provinceName = mysqli_fetch_all($provinceResult, MYSQLI_ASSOC);
										$data['info'] .= "<tr class='tr_".$aDBInfo2['site_id']."'><td><input class=\"selection sitemaster\" data-remote-id=\"".$aDBInfo2['site_id']."\" data-link-id=\"\" data-unique-id=\"".$i."\" type=\"checkbox\" value=\"".$aDBInfo2['site_id']."\" name=\"removesites[]\"> <input type='hidden' name='sites[]' value='".$aDBInfo2['site_id']."'> </td><td><a href='javascript:void(0)' class='siteDetails' id='".$aDBInfo2['site_id']."'>".$aDBInfo2['site_code']."</td><td>".$aDBInfo2['site_name']."</td><td> ".$provinceName[0]['province']." </td><td>".$aDBInfo2['responsible_area']."</td><td>".date('d M Y (h:i a)',strtotime($aDBInfo2['created_date']))."</td></tr>";
										$i++;
									}
								}
							}
						}
						/*********** EOF Child As Parent (If Selected site is parent site) *********************/
						$i++;
					}
				}
			}else{
				echo $conf->error;
			}
		}
		
		//echo json_encode($data);
		echo $data['info']."*****".$provinceId;
	}

	public function getSingleSites(){
		require_once('../webservices/wbdb.php');
		$data = array('flag'=>FALSE,'info'=>'');

		//
		//print_r($_POST['sites']);
		if(!empty($_POST['sites'])!=''){
			$ids = implode(',', $_POST['sites']);
			$query = "SELECT * FROM ntsites WHERE `is_active` = 1 AND `site_id` IN (".$ids.")";
			$result = mysqli_query($conf, $query);
		
			if($result){
				if($result->num_rows>0){
					$data['flag'] = TRUE;
					//echo $result->num_rows;
					$i = 0; $sitesAdded = array();
					while ($aDBInfo = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

						if(!in_array($aDBInfo['site_id'], $sitesAdded)){
							
							array_push($sitesAdded, $aDBInfo['site_id']);
							$provinceQuery = "SELECT province FROM ntsiteprovince WHERE `province_id` = '".$aDBInfo['province']."'";
							$provinceResult = mysqli_query($conf, $provinceQuery);
							$provinceName = mysqli_fetch_all($provinceResult, MYSQLI_ASSOC);
							$data['info'] .= "<tr class='tr_".$aDBInfo['site_id']."'><td><input class=\"selection sitemaster\" data-remote-id=\"".$aDBInfo['site_id']."\" data-link-id=\"\" data-unique-id=\"".$i."\" type=\"checkbox\" value=\"".$aDBInfo['site_id']."\" name=\"removesites[]\"> <input type='hidden' name='sites[]' value='".$aDBInfo['site_id']."'> </td><td><a href='https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=NewCI&c[feature]=SiteInformation&id=".$aDBInfo['site_id']."' target='_change' class='siteDetails' id='".$aDBInfo['site_id']."'>".$aDBInfo['site_code']."</td><td>".$aDBInfo['site_name']."</td><td> ".$provinceName[0]['province']." </td><td>".$aDBInfo['responsible_area']."</td><td>".date('d M Y (h:i a)',strtotime($aDBInfo['created_date']))."</td></tr>";
						}
						$i++;
					}
				}
			}else{
				echo $conf->error;
			}
		}
		
		//echo json_encode($data);
		echo $data['info'];
	}
}
?>