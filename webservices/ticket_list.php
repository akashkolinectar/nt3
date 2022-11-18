<?php 
require_once('wbdb.php');

//$postData = json_decode(file_get_contents("php://input"),true);
$postData = $_REQUEST;

$data = array('flag'=>false,'msg'=>'No data found','info'=>array());

if(isset($postData['category']) && isset($postData['subcategory']) && (isset($postData['status']) || isset($postData['agent']) || isset($postData['department']))){
	$query='';
	switch ($postData['category']) {
		case 1:
		if($postData['subcategory']=='status'){
			$query = "SELECT tk.id as ticketid,dept.name as department,tk.ref,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'Undefined') as caller,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,tk.title,tk.start_date,inc.priority,inc.status FROM ntticket tk LEFT JOIN ntticket_incident inc ON (inc.id = tk.id AND tk.finalclass='Incident') LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') LEFT JOIN ntperson callerper ON callerper.id=callercnt.id LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE inc.status != 'closed' AND inc.status = '".$postData['status']."'";
		}
		else if($postData['subcategory']=='agent'){
			$query = "SELECT tk.id as ticketid,dept.name as department,tk.ref,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'Undefined') as caller,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,tk.title,tk.start_date,inc.priority,inc.status FROM ntticket tk LEFT JOIN ntticket_incident inc ON (inc.id = tk.id AND tk.finalclass='Incident') LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') LEFT JOIN ntperson callerper ON callerper.id=callercnt.id LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE inc.status != 'closed' AND tk.agent_id = '".$postData['agent']."'";
		}
		else if($postData['subcategory']=='department'){
			$query = "SELECT tk.id as ticketid,dept.name as department,tk.ref,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'Undefined') as caller,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,tk.title,tk.start_date,inc.priority,inc.status FROM ntticket tk LEFT JOIN ntticket_incident inc ON (inc.id = tk.id AND tk.finalclass='Incident') LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') LEFT JOIN ntperson callerper ON callerper.id=callercnt.id LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE inc.status != 'closed' AND tk.org_id = '".$postData['department']."'";
		}
		break;

		case 2:
		if($postData['subcategory']=='status'){
			$query = "SELECT tk.id as ticketid,dept.name as department,tk.ref,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'Undefined') as caller,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,tk.title,tk.start_date,prob.priority,prob.status FROM ntticket tk LEFT JOIN ntticket_problem prob ON (prob.id = tk.id AND tk.finalclass='Problem') LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') LEFT JOIN ntperson callerper ON callerper.id=callercnt.id LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE prob.status != 'closed' AND prob.status = '".$postData['status']."'";
		}
		else if($postData['subcategory']=='agent'){
			$query = "SELECT tk.id as ticketid,dept.name as department,tk.ref,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'Undefined') as caller,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,tk.title,tk.start_date,prob.priority,prob.status FROM ntticket tk LEFT JOIN ntticket_problem prob ON (prob.id = tk.id AND tk.finalclass='Problem') LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') LEFT JOIN ntperson callerper ON callerper.id=callercnt.id LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE prob.status != 'closed' AND tk.agent_id = '".$postData['agent']."'";
		}
		else if($postData['subcategory']=='department'){
			$query = "SELECT tk.id as ticketid,dept.name as department,tk.ref,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'Undefined') as caller,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,tk.title,tk.start_date,prob.priority,prob.status FROM ntticket tk LEFT JOIN ntticket_problem prob ON (prob.id = tk.id AND tk.finalclass='Problem') LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') LEFT JOIN ntperson callerper ON callerper.id=callercnt.id LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE prob.status != 'closed' AND tk.org_id = '".$postData['department']."'";
		}
		break;

		
		case 3: // No Priority for change management
		if($postData['subcategory']=='status'){
			$query = "SELECT tk.id as ticketid,dept.name as department,tk.ref,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'Undefined') as caller,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,tk.title,tk.start_date,chn.status,tk.finalclass as subchangecat FROM ntticket tk LEFT JOIN ntchange chn ON (chn.id = tk.id AND (tk.finalclass='NormalChange' OR tk.finalclass='RoutineChange' OR tk.finalclass='EmergencyChange')) LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') LEFT JOIN ntperson callerper ON callerper.id=callercnt.id LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE chn.status != 'closed' AND chn.status = '".$postData['status']."'";
		}
		else if($postData['subcategory']=='agent'){
			$query = "SELECT tk.id as ticketid,dept.name as department,tk.ref,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'Undefined') as caller,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,tk.title,tk.start_date,chn.status,tk.finalclass as subchangecat FROM ntticket tk LEFT JOIN ntchange chn ON (chn.id = tk.id AND (tk.finalclass='NormalChange' OR tk.finalclass='RoutineChange' OR tk.finalclass='EmergencyChange')) LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') LEFT JOIN ntperson callerper ON callerper.id=callercnt.id LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE chn.status != 'closed' AND tk.agent_id = '".$postData['agent']."'";
		}
		else if($postData['subcategory']=='department'){
			$query = "SELECT tk.id as ticketid,dept.name as department,tk.ref,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'Undefined') as caller,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'Undefined') as agent,tk.title,tk.start_date,chn.status,tk.finalclass as subchangecat FROM ntticket tk LEFT JOIN ntchange chn ON (chn.id = tk.id AND (tk.finalclass='NormalChange' OR tk.finalclass='RoutineChange' OR tk.finalclass='EmergencyChange')) LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') LEFT JOIN ntperson callerper ON callerper.id=callercnt.id LEFT JOIN ntorganization dept ON dept.id=tk.org_id WHERE chn.status != 'closed' AND tk.org_id = '".$postData['department']."'";
		}
		break;

		default: break;
	}
	if($query!=''){
		//echo $query; exit();
		$result = mysqli_query($conf, $query);		
		if ($result) {
			if(mysqli_num_rows($result)>0){
				$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_all($result, MYSQLI_ASSOC));
				//$data['info'] = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($data['info']));
					/*while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
						$data['info'][$row[$name]] = $row[$number];					
					}
					$data['flag'] = true;
					$data['msg'] = 'Data found';*/
			}else{
				$data['msg'] = 'No records available';
			}
		}
	}else{
		$data = array('flag'=>true,'msg'=>'Invalid parameter');
	}
	
}
//echo "<pre>";
echo json_encode($data, JSON_INVALID_UTF8_IGNORE);
//echo json_encode($data, JSON_SUBSTITUTE);
//echo json_encode($data);
?>