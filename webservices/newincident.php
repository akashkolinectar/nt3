<?php
require_once('wbdb.php');

$data = array();
$query = '';

$jsondata = file_get_contents("php://input");
$postdata = json_decode($jsondata,TRUE);

/*print_r($postdata);
exit();*/
/*echo json_encode($postdata,TRUE);

exit();*/
//ActiveAlarmID : int, AlarmID : int, PerceivedSeverityID : int, PerceivedSeverity : string

if(!empty($postdata)){


	/*print_r($postdata);
	exit();*/

	$id = 0;
	$query1 = "SELECT id FROM ntticket ORDER BY id DESC LIMIT 1";
	$result1 = mysqli_query($conf, $query1);
	if ($result1) {
		if(mysqli_num_rows($result1)>0){
			$row1 = mysqli_fetch_assoc($result1);
			$id = $row1['id'];
		}
	}

	//echo json_encode($postdata);
	//print_r($postdata);

	$descnew = "<h4>Managed Elements</h4><ul>";
	foreach ($postdata as $rows) {
		$descnew .= "<li>".$rows['ManagedElement']."</li>";
	}
	$descnew .= "</ul>";

	$unique = array();
	foreach ($postdata as $value)
	{
	   //$unique[$value['ProbableCause']] = $value;
	   $unique[$value['ManagedElement']] = $value;
	}
	
	//$unique = array_unique($postdata);
	//print_r($unique); exit();


	foreach ($postdata as $rows) {

		//print_r($rows['ProbableCause']);
		

		if((isset($rows['ManagedElement']) && $rows['ManagedElement']!='') && (isset($rows['SpecificProblem']) && $rows['SpecificProblem']!='') && (isset($rows['ProbableCause']) && $rows['ProbableCause']!='') && (isset($rows['EventTime']) && $rows['EventTime']!='') && (isset($rows['AlarmID']) && $rows['AlarmID']!='')){	

			$id = $id+1;
			//$title = $rows['ManagedElement'];
			//$desc = "Specific Problem : ".$rows['SpecificProblem']." Problem Cause : ".$rows['ProbableCause'];
			$desc = $rows['SpecificProblem']."</br>".$descnew;
			$title = $rows['ProbableCause'];
			$ids = (string)$id;

			$refid = (strlen($id)>5)? $ids:((strlen($id)==5)? str_pad($ids, 1, "0", STR_PAD_LEFT):((strlen($id)==4)? str_pad($ids, 2, "0", STR_PAD_LEFT):((strlen($id)==3)? str_pad($ids, 3, "0", STR_PAD_LEFT): ((strlen($id)==2)? str_pad($ids, 4, "0", STR_PAD_LEFT):str_pad($ids, 5, "0", STR_PAD_LEFT) ) ) ) );

			$zeros = (string)((strlen($id)>5)? '':((strlen($id)==5)? '0':((strlen($id)==4)? '00':((strlen($id)==3)? '000': ((strlen($id)==2)? '0000':'00000' ) ) ) ) );
			$refName = "TT-".$zeros."".$refid;

			// str_pad($ids, 3, "0", STR_PAD_LEFT);
			/*$query2 = "INSERT INTO ntticket (`id`, `operational_status`, `ref`, `org_id`, `caller_id`, `team_id`, `agent_id`, `title`, `description`, `description_format`, `start_date`, `end_date`, `last_update`, `close_date`, `private_log`, `private_log_index`, `finalclass`) VALUES (".$id.", 'ongoing', 'TT-000".$id."', '32', '142', '0', '0', '".$title."', '".$desc."', 'text', '".date('Y-m-d H:i:s')."', NULL, NULL, NULL, NULL, NULL, 'Incident')";*/

			$query2 = "INSERT INTO ntticket (`id`, `operational_status`, `ref`, `org_id`, `caller_id`, `team_id`, `agent_id`, `title`, `description`, `description_format`, `start_date`, `end_date`, `last_update`, `close_date`, `private_log`, `private_log_index`, `finalclass`) VALUES (".$id.", 'ongoing', '".$refName."', '32', '142', '0', '0', '".$title."', '".$desc."', 'html', '".date('Y-m-d H:i:s')."', NULL, NULL, NULL, NULL, NULL, 'Incident')";
			$result2 = mysqli_query($conf, $query2);

			if($result2){
				$lastid = mysqli_insert_id($conf);

				$query3 = "INSERT INTO ntticket_incident (`id`, `status`, `impact`, `priority`, `urgency`, `origin`, `service_id`, `servicesubcategory_id`, `escalation_flag`, `escalation_reason`, `assignment_date`, `resolution_date`, `last_pending_date`, `cumulatedpending_timespent`, `cumulatedpending_started`, `cumulatedpending_laststart`, `cumulatedpending_stopped`, `tto_timespent`, `tto_started`, `tto_laststart`, `tto_stopped`, `tto_75_deadline`, `tto_75_passed`, `tto_75_triggered`, `tto_75_overrun`, `tto_100_deadline`, `tto_100_passed`, `tto_100_triggered`, `tto_100_overrun`, `ttr_timespent`, `ttr_started`, `ttr_laststart`, `ttr_stopped`, `ttr_75_deadline`, `ttr_75_passed`, `ttr_75_triggered`, `ttr_75_overrun`, `ttr_100_deadline`, `ttr_100_passed`, `ttr_100_triggered`, `ttr_100_overrun`, `time_spent`, `resolution_code`, `solution`, `pending_reason`, `parent_incident_id`, `parent_problem_id`, `parent_change_id`, `public_log`, `public_log_index`, `user_satisfaction`, `user_commment`) VALUES ('".$lastid."', 'new', '1', '1', '1', 'phone', '0', '0', 'no', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'assistance', NULL, NULL, '0', '0', '0', NULL, NULL, '1', NULL)";

				$result3 = mysqli_query($conf, $query3);

				$query4 = "INSERT INTO nps_incident (`incident_id`, `alarm_id`, `is_active`) VALUES  ('".$lastid."','".$rows['AlarmID']."',1)";
				$result4 = mysqli_query($conf, $query4);

				if($result4){


					if(isset($rows['SiteName'])){
						
						$siteName1 = explode('-', $rows['SiteName']);
						$siteName1 = isset($siteName1[1])? $siteName1[1]:$siteName1[0];
						$siteName2 = str_replace('_', ' ', $siteName1);
						$siteName3 = str_replace(' ', '_', $siteName1);

						$siteName4 = explode('_', $rows['SiteName']);
						$siteName4 = isset($siteName4[1])? $siteName4[1]:$siteName4[0];
						$siteName5 = str_replace('_', ' ', $siteName4);
						$siteName6 = str_replace(' ', '_', $siteName4);


						$query5 = "SELECT site_id FROM ntsites WHERE site_name='".$rows['SiteName']."' OR site_name = '".$siteName1."' OR site_name = '".$siteName2."' OR site_name = '".$siteName3."' OR site_name='".$siteName4."' OR site_name='".$siteName5."' OR site_name='".$siteName6."'";
						$result5 = mysqli_query($conf, $query5);
						if($result5->num_rows>0){
							$site = mysqli_fetch_all($result5,MYSQLI_ASSOC);

							if(isset($site[0]['site_id']) && $site[0]['site_id']!=0){
								$query6 = "INSERT INTO ntticketsites (`ticket_id`, `site_id`, `created_date`, `is_active`) VALUES  ('".$lastid."','".$site[0]['site_id']."','".date('Y-m-d H:i:s')."',1)";
								$result6 = mysqli_query($conf, $query6);
								if($result6){
									$temp['SiteStatus'] = 'Site Added';
								}
							}
						}
					}

					//print_r($unique); exit(); $siteName1 = explode('-', $postdata['SiteName']);

					/*$query5 = "INSERT INTO ntticketsites (`ticket_id`,`site_id`,`created_date`) VALUES ('".$lastid."', '".$siteid."','".date('Y-m-d H:i:s')."')";
					$result5 = mysqli_query($conf, $query5);*/

					$temp['IsIncident'] = TRUE; $temp['NT3IncidentRemark'] = 'Data Inserted';
					$temp['NT3IncidentID'] = $lastid;
					$temp['ProbableCause'] = $rows['ProbableCause'];
					array_push($data, $temp);
				}

			}else{
				$temp['IsIncident'] = FALSE; $temp['NT3IncidentRemark'] = 'Unable To Insert Data';
				$temp['NT3IncidentID'] = 0;
				$temp['ProbableCause'] = $rows['ProbableCause'];
				array_push($data, $temp);
			}
		}else{
			$temp['IsIncident'] = FALSE; $temp['NT3IncidentRemark'] = 'Missing Parameters';
			$temp['NT3IncidentID'] = 0;
			$temp['ProbableCause'] = $rows['ProbableCause'];
			array_push($data, $temp);
		}
	}
}else{
	$temp['IsIncident'] = FALSE; $temp['NT3IncidentRemark'] = 'Empty Parameter';
	$temp['NT3IncidentID'] = 0;
	array_push($data, $temp);
}

echo json_encode($data,TRUE);

?>