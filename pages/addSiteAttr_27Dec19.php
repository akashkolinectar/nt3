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
	case 'phase': $obj::addSiteAttributes('ntsitephase','phase'); break;
	//case 'commission': $obj::addSiteAttributes('ntsitecommission','commission'); break;
	case 'stage': $obj::addSiteAttributes('ntsitestage','stage'); break;
	case 'sub_stage': $obj::addSiteAttributes('ntsitesubstage','sub_stage'); break;
	case 'typology': $obj::modifyTypology('nttypology'); break;
	case 'overview': $obj::getOverview(); break;
	
	default:
		# code...
		break;
}

class addSiteAttr
{
	
	public function addSiteAttributes($tbl,$col1){
		
		require_once('../webservices/wbdb.php');
		$data = array('flag'=>FALSE,'dropdd'=>"");
		$attr = $_POST['attr_val'];
		$query1 = "INSERT INTO $tbl (`".$col1."`,`is_active`,`created_date`) VALUES ('$attr',1,'".date('Y-m-d H:i:s')."')";
		$result1 = mysqli_query($conf, $query1);
		if($result1){
			$dropdd = "<option value=''> -- Select One --</option>";
			$query2 = "SELECT * FROM $tbl WHERE `is_active` = 1 ORDER BY $col1 DESC";
			$result2 = mysqli_query($conf, $query2);
			while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
				$selected = ($row[$col1]==$attr)? "selected='selected'":"";
				$dropdd .= "<option value='".$row[$col1]."' ".$selected.">".$row[$col1]."</option>";
			}
			$data = array('flag'=>TRUE,'dropdd'=>$dropdd,'attr'=>'site_'.$_POST['attr']);
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
}

?>