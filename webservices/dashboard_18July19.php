<?php 
require_once('wbdb.php');

//$postData = json_decode(file_get_contents("php://input"),true);
$postData = $_REQUEST;
$data = array('flag'=>false,'msg'=>'No data found','info'=>array());

if(isset($postData['category']) && isset($postData['subcategory']) && isset($postData['userid'])){
	$query='';
	switch ($postData['category']) {
		case 1:
		// Incident Management 
		if($postData['subcategory']=='status'){
			$query = "SELECT count(inc.status) as total,inc.status FROM ntticket tk LEFT JOIN ntticket_incident inc ON (inc.id = tk.id AND tk.finalclass='Incident')  WHERE inc.status != 'closed' GROUP BY inc.status HAVING COUNT(inc.status) > 1";
			$number = 'total'; $name = 'status';
		}
		else if($postData['subcategory']=='agent'){
			$query = "SELECT IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,count(tk.id) as tickets FROM ntticket tk LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id WHERE tk.operational_status!='closed' AND tk.finalclass='Incident' GROUP BY tk.agent_id HAVING COUNT(tk.agent_id) > 1";
			$number = 'tickets'; $name = 'agent';
		}
		else if($postData['subcategory']=='department'){
			$query = "SELECT dept.name as department,count(tk.id) as tickets FROM ntticket tk LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE tk.operational_status!='closed' AND tk.finalclass='Incident' GROUP BY tk.org_id HAVING COUNT(tk.org_id) > 1";
			$number = 'tickets'; $name = 'department';
		}
		break;

		case 2:
		// Problem Management 
		if($postData['subcategory']=='status'){
			$query = "SELECT count(prob.status) as total,prob.status FROM ntticket tk LEFT JOIN ntticket_problem prob ON (prob.id = tk.id AND tk.finalclass='Problem')  WHERE prob.status != 'closed' GROUP BY prob.status HAVING COUNT(prob.status) > 1";
			$number = 'total'; $name = 'status';
		}
		else if($postData['subcategory']=='agent'){
			$query = "SELECT IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,count(tk.id) as tickets FROM ntticket tk LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id WHERE tk.operational_status!='closed' AND tk.finalclass='Problem' GROUP BY tk.agent_id HAVING COUNT(tk.agent_id) > 1";
			$number = 'tickets'; $name = 'agent';
		}
		else if($postData['subcategory']=='department'){
			$query = "SELECT dept.name as department,count(tk.id) as tickets FROM ntticket tk LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE tk.operational_status!='closed' AND tk.finalclass='Problem' GROUP BY tk.org_id HAVING COUNT(tk.org_id) > 1";
			$number = 'tickets'; $name = 'department';
		}
		break;

		default: break;
	}
	if($query!=''){
		$result = mysqli_query($conf, $query);
		if ($result) {
			if(mysqli_num_rows($result)>0){				
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
						$data['info'][$row[$name]] = $row[$number];
					}
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