<?php
require_once('wbdb.php');

$data = array();
$query = '';

$jsondata = file_get_contents("php://input");
$postdata = json_decode($jsondata,TRUE);

if(!empty($postdata)){

	foreach ($postdata as $rows) {

		if((isset($rows['AlarmID']) && $rows['AlarmID']!='') && (isset($rows['IsResolved']) && $rows['IsResolved']!='')){	

			if($rows['IsResolved']==TRUE){
				$gtInc = "SELECT incident_id FROM nps_incident WHERE alarm_id = ".$rows['AlarmID'];
				$rInc = mysqli_query($conf, $gtInc);

				if($rInc){

					if($rInc->num_rows>0){
						$incident = mysqli_fetch_all($rInc,MYSQLI_ASSOC);
						$upTkt = "Update ntticket set operational_status='closed',close_date='".date('Y-m-d H:i:s')."' where id='".$incident[0]['incident_id']."'";
					    $rTkt= mysqli_query($conf, $upTkt);

					    $upInc = "Update ntticket_incident set status='closed' where id='".$incident[0]['incident_id']."'";
					    $rUpInc = mysqli_query($conf, $upInc);

					    if($rUpInc){
					    	$temp['IsCloseIncident'] = TRUE;
							$temp['AlarmID'] = $rows['AlarmID'];
							/*$temp['NT3IncidentID'] = $incident[0]['incident_id'];*/
							$temp['NT3IncidentRemark'] = 'Ticket Closed';
							array_push($data, $temp);

					    }else{
					    	$temp['IsCloseIncident'] = FALSE;
					    	$temp['AlarmID'] = $rows['AlarmID'];
					    	/*$temp['NT3IncidentID'] = $incident[0]['incident_id'];*/
							$temp['NT3IncidentRemark'] = 'Unable To Close Ticket';
							array_push($data, $temp);
					    }
					}else{
						$temp['IsCloseIncident'] = FALSE;
						$temp['AlarmID'] = $rows['AlarmID'];
						/*$temp['NT3IncidentID'] = 0;*/
						$temp['NT3IncidentRemark'] = 'Incident Not Available';
						array_push($data, $temp);
					}
					
				}
				else{
					$temp['IsCloseIncident'] = FALSE;
					$temp['AlarmID'] = $rows['AlarmID'];
					/*$temp['NT3IncidentID'] = 0;*/
					$temp['NT3IncidentRemark'] = 'Unable To Find Incident';
					array_push($data, $temp);
				}
			}
			
		}else{
			$temp['IsCloseIncident'] = FALSE;
			$temp['AlarmID'] = 0;
			/*$temp['NT3IncidentID'] = 0;*/
			$temp['NT3IncidentRemark'] = 'Unable To Find AlarmID/IsResolved';
			array_push($data, $temp);
		}
	}
}else{
	$temp['IsCloseIncident'] = FALSE;
	$temp['AlarmID'] = 0;
	/*$temp['NT3IncidentID'] = 0;*/
	$temp['NT3IncidentRemark'] = 'Empty Parameter';
	array_push($data, $temp);
}

echo json_encode($data,TRUE);

?>