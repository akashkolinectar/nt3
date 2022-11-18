<?php 
require_once('wbdb.php');

$postData = $_REQUEST;
$data = array('flag'=>false,'msg'=>'No data found');
$query = '';

if(isset($postData['ticketid']) && isset($postData['userid']) && isset($postData['category'])){
	
switch($postData['category']){
	
	case 1: 
	$query = "SELECT tk.id,tk.ref,tk.title,dept.name as department,inc.status,inc.priority,serv.name as service,servsub.name as subservice,inc.origin,CASE WHEN inc.impact = 1 THEN 'A Department' WHEN inc.impact = 2 THEN 'A Service' WHEN inc.impact = 3 THEN 'A person' ELSE 'NA' END AS impact,inc.urgency,tk.description,tk.start_date,tk.last_update,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'Undefined') as caller
	FROM ntticket tk 
	LEFT JOIN ntticket_incident inc ON (inc.id = tk.id AND tk.finalclass='Incident') 
	LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') 
	LEFT JOIN ntperson per ON per.id=cnt.id 
	LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') 
	LEFT JOIN ntperson callerper ON callerper.id=callercnt.id 
	LEFT JOIN ntorganization dept ON dept.id=tk.org_id 
	LEFT JOIN ntservice serv ON serv.id=inc.service_id 
	LEFT JOIN ntservicesubcategory servsub ON servsub.id=inc.servicesubcategory_id 
	WHERE tk.id = ".$postData['ticketid'];

	break;

	case 2:
	$query = "SELECT tk.id,tk.ref,tk.title,dept.name as department,prob.status,prob.priority,serv.name as service,servsub.name as subservice,prob.product,CASE WHEN prob.impact = 1 THEN 'A Department' WHEN prob.impact = 2 THEN 'A Service' WHEN prob.impact = 3 THEN 'A person' ELSE 'NA' END AS impact,prob.urgency,tk.description,tk.start_date,tk.last_update,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'Undefined') as caller
	FROM ntticket tk 
	LEFT JOIN ntticket_problem prob ON (prob.id = tk.id AND tk.finalclass='Problem') 
	LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') 
	LEFT JOIN ntperson per ON per.id=cnt.id 
	LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person')
	LEFT JOIN ntperson callerper ON callerper.id=callercnt.id 
	LEFT JOIN ntorganization dept ON dept.id=tk.org_id 
	LEFT JOIN ntservice serv ON serv.id=prob.service_id
	LEFT JOIN ntservicesubcategory servsub ON servsub.id=prob.servicesubcategory_id
	WHERE tk.id = ".$postData['ticketid'];

	break;

	case 3:
	$query = "SELECT tk.id,tk.ref,tk.title,dept.name as department,chn.status,tk.description,tk.start_date,tk.last_update,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'Undefined') as caller,tk.agent_id as agent_id,tk.finalclass as changesubcat
	FROM ntticket tk 
	LEFT JOIN ntchange chn ON (chn.id = tk.id AND (tk.finalclass='NormalChange' OR tk.finalclass='RoutineChange' OR tk.finalclass='EmergencyChange')) 
	LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') 
	LEFT JOIN ntperson per ON per.id=cnt.id 
	LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person')
	LEFT JOIN ntperson callerper ON callerper.id=callercnt.id 
	LEFT JOIN ntdepartment dept ON dept.id=tk.org_id
	WHERE tk.id = ".$postData['ticketid'];
	
	break;
}
if($query!=''){
		$result = mysqli_query($conf, $query);
		if ($result) {
//echo $query;
			if(mysqli_num_rows($result)>0){
				//print_r(mysqli_fetch_all($result, MYSQLI_ASSOC));
				//$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_all($result, MYSQLI_ASSOC));
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
						
						if(isset($row['priority'])){
							switch ($row['priority']) {
								case 1: $row['priority'] = 'Critical'; break;
								case 2: $row['priority'] = 'High'; break;
								case 3: $row['priority'] = 'Medium'; break;
								case 4: $row['priority'] = 'Low'; break;							
								default:break;
							}
						}
						if(isset($row['urgency'])){
							switch ($row['urgency']) {
								case 1: $row['urgency'] = 'Critical'; break;
								case 2: $row['urgency'] = 'High'; break;
								case 3: $row['urgency'] = 'Medium'; break;
								case 4: $row['urgency'] = 'Low'; break;							
								default:break;
							}
						}
						
						//sites
						$row['sites'] = array();
						$agentquery = "SELECT sit.site_name FROM `ntticketsites` tic RIGHT JOIN ntsites sit ON sit.site_id=tic.site_id WHERE tic.ticket_id=".$postData['ticketid'];
						$tempSites=array();
						$agentresult = mysqli_query($conf, $agentquery);
						if(mysqli_num_rows($agentresult)>0){
							$agent = mysqli_fetch_all($agentresult, MYSQLI_ASSOC);	
							if(!empty($agent)){
								foreach($agent as $site_row ){
									array_push($row['sites'] ,$site_row['site_name']);
								}
							}
						}
						$row['description'] = rtrim(ltrim(strip_tags($row['description'])));
						$data['info'] = $row;		
					}
					$data['flag'] = true;
					$data['msg'] = 'Data found';
					//print_r($data['info']);
			}else{
				$data['msg'] = 'Information unavailable';
			}
		}
	}else{
		$data = array('flag'=>true,'msg'=>'Invalid parameters');
	}
}
//echo json_encode($data);
echo json_encode($data, JSON_INVALID_UTF8_IGNORE);
?>