<?php 
require_once('wbdb.php');

//$postData = json_decode(file_get_contents("php://input"),true);
$postData = $_REQUEST;
$data = array('flag'=>false,'msg'=>'No data found','info'=>array());

//$query = "SELECT Incident WHERE DATE_SUB(NOW(), INTERVAL 20 DAY) < start_date AND login='".$postData['userid']."'";
//$query = "SELECT * FROM ntticket_incident WHERE DATE_SUB(NOW(), INTERVAL 20 DAY) < tto_started ";
if(isset($postData['category']) && isset($postData['subcategory'])){
	$query='';
	switch ($postData['category']) {
		case 1: 
		// Incident Management 
		/*$query = "SELECT count(inc.status) as total,inc.status FROM ntticket tk LEFT JOIN ntticket_incident inc ON (inc.id = tk.id AND tk.finalclass='Incident')  WHERE DATE_SUB(NOW(), INTERVAL 20 DAY) < tk.start_date AND inc.status != 'closed' GROUP BY inc.status
HAVING COUNT(inc.status) > 1"; break;*/
		if($postData['subcategory']=='status'){
			/*$query = "SELECT count(inc.status) as total,inc.status FROM ntticket tk LEFT JOIN ntticket_incident inc ON (inc.id = tk.id AND tk.finalclass='Incident')  WHERE inc.status != 'closed' AND inc.status IN ('resolved','new','pending','assigned') GROUP BY inc.status HAVING COUNT(inc.status) >= 1";*/
			$query = "SELECT count(inc.status) as total,inc.status FROM ntticket tk LEFT JOIN ntticket_incident inc ON (inc.id = tk.id AND tk.finalclass='Incident')  WHERE inc.status != 'closed' GROUP BY inc.status HAVING COUNT(inc.status) >= 1";
			$number = 'total'; $name = 'status';
		}
		else if($postData['subcategory']=='agent'){
			$query = "SELECT IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,count(tk.id) as agent_tickets,tk.agent_id FROM ntticket tk LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id WHERE tk.operational_status!='closed' AND tk.finalclass='Incident' GROUP BY tk.agent_id HAVING COUNT(tk.agent_id) >= 1";
			$number = 'tickets'; $name = 'agent'; $id = 'agent_id';
			//echo $query;
		}
		else if($postData['subcategory']=='department'){
			$query = "SELECT dept.name as department,count(tk.id) as tickets,tk.org_id FROM ntticket tk LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE tk.operational_status!='closed' AND tk.finalclass='Incident' GROUP BY tk.org_id HAVING COUNT(tk.org_id) >= 1";
			$number = 'tickets'; $name = 'department'; $id = 'org_id';
		}
		break;
/**/
		case 2:
		// Problem Management 
		if($postData['subcategory']=='status'){
			$query = "SELECT count(prob.status) as total,prob.status FROM ntticket tk LEFT JOIN ntticket_problem prob ON (prob.id = tk.id AND tk.finalclass='Problem')  WHERE prob.status != 'closed' GROUP BY prob.status HAVING COUNT(prob.status) >= 1";
			$number = 'total'; $name = 'status';
		}
		else if($postData['subcategory']=='agent'){
			$query = "SELECT IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,count(tk.id) as agent_tickets,tk.agent_id FROM ntticket tk LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id WHERE tk.operational_status!='closed' AND tk.finalclass='Problem' GROUP BY tk.agent_id HAVING COUNT(tk.agent_id) >= 1";
			$number = 'tickets'; $name = 'agent'; $id = 'agent_id';
		}
		else if($postData['subcategory']=='department'){
			$query = "SELECT dept.name as department,count(tk.id) as tickets,tk.org_id FROM ntticket tk LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE tk.operational_status!='closed' AND tk.finalclass='Problem' GROUP BY tk.org_id HAVING COUNT(tk.org_id) >= 1";
			$number = 'tickets'; $name = 'department'; $id = 'org_id';
		}
		break;

		case 3:
		// Change Management 
		if($postData['subcategory']=='status'){
			$query = "SELECT count(cng.status) as total,cng.status FROM ntticket tk LEFT JOIN ntchange cng ON (cng.id = tk.id AND (tk.finalclass='NormalChange' OR tk.finalclass='RoutineChange' OR tk.finalclass='EmergencyChange'))  WHERE cng.status != 'closed' GROUP BY cng.status HAVING COUNT(cng.status) >= 1";
			$number = 'total'; $name = 'status';
		}
		else if($postData['subcategory']=='agent'){
			$query = "SELECT IF(per.first_name!='',CONCAT(CONVERT(per.first_name USING utf8),' ',CONVERT(cnt.name USING utf8)),'Undefined') as agent,count(tk.id) as agent_tickets,tk.agent_id FROM ntticket tk LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id WHERE tk.operational_status!='closed' AND (tk.finalclass='NormalChange' OR tk.finalclass='RoutineChange' OR tk.finalclass='EmergencyChange') GROUP BY tk.agent_id HAVING COUNT(tk.agent_id) >= 1";
			$number = 'tickets'; $name = 'agent'; $id = 'agent_id';
		}
		else if($postData['subcategory']=='department'){
			$query = "SELECT dept.name as department,count(tk.id) as tickets,tk.org_id FROM ntticket tk LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE tk.operational_status!='closed' AND (tk.finalclass='NormalChange' OR tk.finalclass='RoutineChange' OR tk.finalclass='EmergencyChange')  GROUP BY tk.org_id HAVING COUNT(tk.org_id) >= 1";
			$number = 'tickets'; $name = 'department'; $id = 'org_id';
		}
		break;

		default: break;
	}
	//echo $query;
	if($query!=''){
		$result = mysqli_query($conf, $query);
		if ($result) {
			if(mysqli_num_rows($result)>0){
				// print_r(mysqli_fetch_all($result));
				$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_all($result, MYSQLI_ASSOC));
				//$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_array($result, MYSQLI_ASSOC));
				/*
					
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
						if($name=='agent' || $name=='department'){
							$temp = array($row[$name],$row[$number],$row[$id]);
							array_push($data['info'], $temp);
						}else{
							$data['info'][$row[$name]] = $row[$number];
					  	}
					}	*/				
					//array_push($data['info'], $row);					
					$data['flag'] = true;
					$data['msg'] = 'Data found';					
			}else{
				$data['msg'] = 'No records available';
			}
		}
	}else{
		$data = array('flag'=>true,'msg'=>'Invalid parameter');
	}
	
}
echo json_encode($data);
?>