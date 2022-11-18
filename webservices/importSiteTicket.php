<?php
require_once('../webservices/wbdb.php');

$data = array("flag"=>false,"msg"=>"");

if (isset($_POST["import"])) {
    
    $fileName = $_FILES["site_ticket_file"]["tmp_name"];
    if ($_FILES["site_ticket_file"]["size"] > 0) {
        
        $file = fopen($fileName, "r");
        $i = 1; $msg = '';
        $csvData = array();

        while (($columnCSV = fgetcsv($file, 10000, ",")) !== FALSE) {
          array_push($csvData,$columnCSV);
          $i++;
        }
        
        $myfile = fopen("RemainingSiteTickets.txt", "w") or die("Unable to open file!");
        $myExlfile = fopen("RemainingSiteTicketsExcel.csv", "w") or die("Unable to open file!");

        $ticketChk = array(); $ntTicketIds = array();
        foreach ($csvData as $column) {
            
            $siteName1 = explode('-', $column[6]);
            $siteName1 = isset($siteName1[1])? $siteName1[1]:$siteName1[0];
            $siteName2 = str_replace('_', ' ', $siteName1);
            $siteName3 = str_replace(' ', '_', $siteName1);
            $siteName4 = explode('_', $column[6]);
            $siteName4 = isset($siteName4[1])? $siteName4[1]:$siteName4[0];
            $siteName5 = str_replace('-', ' ', $siteName4);
            $siteName6 = str_replace(' ', '-', $siteName4);

            //$siteArr = CMDBSource::QueryToArray("SELECT site_name,site_id FROM ntsites WHERE (site_name='".utf8_encode($column[6])."' OR site_name = '".utf8_encode($siteName1)."' OR site_name = '".utf8_encode($siteName2)."' OR site_name = '".utf8_encode($siteName3)."' OR site_name='".utf8_encode($siteName4)."' OR site_name='".utf8_encode($siteName5)."' OR site_name='".utf8_encode($siteName6)."') AND site_name!=''");
            //echo $q1."<br/>";continue;
            
            $q1 = "SELECT site_name,site_id FROM ntsites WHERE (site_name='".utf8_encode($column[6])."' OR site_name = '".utf8_encode($siteName1)."' OR site_name = '".utf8_encode($siteName2)."' OR site_name = '".utf8_encode($siteName3)."' OR site_name='".utf8_encode($siteName4)."' OR site_name='".utf8_encode($siteName5)."' OR site_name='".utf8_encode($siteName6)."' OR site_name='".$column[6]."') AND site_name!='' AND is_active=1";
            //echo $q1;
            $queryResult = mysqli_query($conf,"SET NAMES 'utf8'");
            $queryResult = mysqli_query($conf,$q1);
            /*echo "<br/> Count: ".$queryResult->num_rows."<br/>";
            echo 'mysql_client_encoding: ', mysql_client_encoding($conf), "\n";
            exit();
*/
           /* if(mysqli_num_rows($result1) < 1) { echo 'index failed'; echo "<pre>"; print_r($result1); } continue;*/
            //if(TRUE){
            if($queryResult){

                //echo $queryResult->num_rows;

                if($queryResult->num_rows>0){
               // if(!empty($siteArr)){
                    
                    $siteArr = mysqli_fetch_all($queryResult,MYSQLI_ASSOC);

                    /*echo "<pre>";
                    print_r($siteArr);
                    exit();*/

                    if(!in_array($column[5], $ticketChk)){

                        array_push($ticketChk, $column[5]);
                        
                        /********** Get Incident Ticket ID ****************/
                        $id = 0;
                        $query1 = "SELECT id FROM ntticket ORDER BY id DESC LIMIT 1";
                        $result1 = mysqli_query($conf, $query1);
                        if ($result1) {
                            if(mysqli_num_rows($result1)>0){
                                $row1 = mysqli_fetch_assoc($result1);
                                $id = $row1['id'];
                            }
                        }

                        $id = $id+1;
                        $ids = (string)$id;
                        $refid = (strlen($id)>5)? $ids:((strlen($id)==5)? str_pad($ids, 1, "0", STR_PAD_LEFT):((strlen($id)==4)? str_pad($ids, 2, "0", STR_PAD_LEFT):((strlen($id)==3)? str_pad($ids, 3, "0", STR_PAD_LEFT): ((strlen($id)==2)? str_pad($ids, 4, "0", STR_PAD_LEFT):str_pad($ids, 5, "0", STR_PAD_LEFT) ) ) ) );

                        $zeros = (string)((strlen($id)>5)? '':((strlen($id)==5)? '0':((strlen($id)==4)? '00':((strlen($id)==3)? '000': ((strlen($id)==2)? '0000':'00000' ) ) ) ) );
                        $refName = "TT-".$zeros."".$refid;

                        /********** Add Incident Ticket ****************/

                        $start_date = ($column[16]!='')? "'".date('Y-m-d H:i:s',strtotime($column[16]))."'":"'".date('Y-m-d H:i:s',strtotime($column[1]))."'";
                        $close_date = ($column[17]!='')? "'".date('Y-m-d H:i:s',strtotime($column[17]))."'":"NULL";
                        $opStatus = 'ongoing';
                        $status = 'new';

                        echo $start_date."****<br/>";
                       // echo date('Y-m-d H:i:s',strtotime($column[1]))."****<br/>";
                        //continue;

                        if($column[17]!=''){
                            $opStatus = 'closed';
                            $status = 'closed';
                        }
                        
                        $query2 = "INSERT INTO ntticket (`id`, `operational_status`, `ref`, `org_id`, `caller_id`, `team_id`, `agent_id`, `title`, `description`, `description_format`, `start_date`, `end_date`, `last_update`, `close_date`, `private_log`, `private_log_index`, `finalclass`) VALUES (".$id.", '".$opStatus."', '".$refName."', '32', '142', '0', '0', 'Event: ".$column[11]."*** Cause: ".$column[15]."', '', 'html', ".$start_date.", NULL, NULL, ".$close_date.", NULL, NULL, 'Incident')";
                        echo $query2;
                        $result2 = mysqli_query($conf, $query2);
                        if($result2){

                            $ticketid = mysqli_insert_id($conf);
                            $ntTicketIds[$column[5]] = $ticketid;

                            echo "***** Ticket Created ****";

                            $query3 = "INSERT INTO ntticket_incident (`id`, `status`, `impact`, `priority`, `urgency`, `origin`, `service_id`, `servicesubcategory_id`, `escalation_flag`, `escalation_reason`, `assignment_date`, `resolution_date`, `last_pending_date`, `cumulatedpending_timespent`, `cumulatedpending_started`, `cumulatedpending_laststart`, `cumulatedpending_stopped`, `tto_timespent`, `tto_started`, `tto_laststart`, `tto_stopped`, `tto_75_deadline`, `tto_75_passed`, `tto_75_triggered`, `tto_75_overrun`, `tto_100_deadline`, `tto_100_passed`, `tto_100_triggered`, `tto_100_overrun`, `ttr_timespent`, `ttr_started`, `ttr_laststart`, `ttr_stopped`, `ttr_75_deadline`, `ttr_75_passed`, `ttr_75_triggered`, `ttr_75_overrun`, `ttr_100_deadline`, `ttr_100_passed`, `ttr_100_triggered`, `ttr_100_overrun`, `time_spent`, `resolution_code`, `solution`, `pending_reason`, `parent_incident_id`, `parent_problem_id`, `parent_change_id`, `public_log`, `public_log_index`, `user_satisfaction`, `user_commment`) VALUES ('".$ticketid."', '".$status."', '1', '1', '1', 'phone', '0', '0', 'no', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'assistance', NULL, NULL, '0', '0', '0', NULL, NULL, '1', NULL)";
                            $result3 = mysqli_query($conf, $query3);

                            if($result3){
                                echo "***** Incident Created ".$column[6]." ****";
                            }

                            /********** Add Service Affected ****************/
                            $servArr = explode(',', $column[12]);
                            foreach ($servArr as $key => $value) {

                                $serviceName = trim($value," ");
                                $query1 = "SELECT service_aftd_id FROM ntserviceaftd WHERE service_aftd='".$serviceName."' LIMIT 1";
                                $result1 = mysqli_query($conf, $query1);
                                if ($result1) {
                                    if(mysqli_num_rows($result1)>0){
                                        $row1 = mysqli_fetch_assoc($result1);
                                        $servid = $row1['service_aftd_id'];
                                        $query4 = "INSERT INTO ntticketserviceaffected (`service_aftd_id`,`ticket_id`,`created_date`) VALUES ($servid,$ticketid,'".date('Y-m-d H:i:s',strtotime($start_date))."')";
                                        $result4 = mysqli_query($conf, $query4);
                                    }
                                }
                            }
                            
                            
                            /********** Get Province ****************/
                            $province = 0;
                            $query5 = "SELECT province_id FROM ntsiteprovince WHERE province='".$column[10]."' LIMIT 1";
                            $result5 = mysqli_query($conf, $query5);
                            if ($result5) {
                                if(mysqli_num_rows($result5)>0){
                                    $row5 = mysqli_fetch_assoc($result5);
                                    $province = $row5['province_id'];
                                }
                            }

                            /********** Get Reason ****************/
                            $reason = 0;
                            $query5 = "SELECT reason_id FROM ntreason WHERE reason='".$column[14]."' LIMIT 1";
                            $result5 = mysqli_query($conf, $query5);
                            if ($result5) {
                                if(mysqli_num_rows($result5)>0){
                                    $row5 = mysqli_fetch_assoc($result5);
                                    $reason = $row5['reason_id'];
                                }else if($column[14]=='Transmissão'){
                                        $reason = 2;
                                    }
                            }

                            /********** Get Sub Reason ****************/
                            $subreason = 0;
                            $query5 = "SELECT sub_reason_id FROM ntsubreason WHERE sub_reason='".$column[15]."' LIMIT 1";
                            $result5 = mysqli_query($conf, $query5);
                            if ($result5) {
                                if(mysqli_num_rows($result5)>0){
                                    $row5 = mysqli_fetch_assoc($result5);
                                    $subreason = $row5['sub_reason_id'];
                                }
                            }

                            /********** Get Event ****************/
                            $event = 0;
                            $query5 = "SELECT event_id FROM ntevent WHERE event='".$column[11]."' LIMIT 1";
                            $result5 = mysqli_query($conf, $query5);
                            if ($result5) {
                                if(mysqli_num_rows($result5)>0){
                                    $row5 = mysqli_fetch_assoc($result5);
                                    $event = $row5['event_id'];
                                }
                            }

                            /********** Get Dependance ****************/
                            $dependanceid = 0; $dependance = '';

                            $query = "SELECT bsc_id as id FROM ntsitebsc WHERE bsc='".$column[7]."'";
                            $result = mysqli_query($conf, $query);
                            if ($result) {
                                if(mysqli_num_rows($result)>0){
                                    $row = mysqli_fetch_assoc($result);
                                    $dependanceid = $row['id'];
                                    $dependance = 'bsc';
                                }
                            }

                            $query = "SELECT msc_id as id FROM ntsitemsc WHERE msc='".$column[7]."'";
                            $result = mysqli_query($conf, $query);
                            if ($result) {
                                if(mysqli_num_rows($result)>0){
                                    $row = mysqli_fetch_assoc($result);
                                    $dependanceid = $row['id'];
                                    $dependance = 'msc';
                                }
                            }

                            $query = "SELECT rnc_id as id FROM ntsiternc WHERE rnc='".$column[7]."'";
                            $result = mysqli_query($conf, $query);
                            if ($result) {
                                if(mysqli_num_rows($result)>0){
                                    $row = mysqli_fetch_assoc($result);
                                    $dependanceid = $row['id'];
                                    $dependance = 'rnc';
                                }
                            }

                            $query = "SELECT mgw_id as id FROM ntsitebsc WHERE mgw='".$column[7]."'";
                            $result = mysqli_query($conf, $query);
                            if ($result) {
                                if(mysqli_num_rows($result)>0){
                                    $row = mysqli_fetch_assoc($result);
                                    $dependanceid = $row['id'];
                                    $dependance = 'mgw';
                                }
                            }
                            
                            //echo "**** province:$province*** Reason:$reason *** Sub Reason:$subreason*** Dependance:$dependanceid (".$column[7].") *** Event : $event";
                            //echo "<br/>";
                            //continue;

                            /************* Update Ticket With All Get Data **************/
                            $query5 = "UPDATE ntticket SET province_id=$province,reason_id=$reason,sub_reason_id=$subreason,event_id=$event,dependance_id=$dependanceid,dependance='$dependance'";
                            $result5 = mysqli_query($conf, $query5);
                            if ($result5) {
                                echo "***** Ticket Updated ****";
                            }

                            /********** Add Site Ticket ****************/
                            $query6 = "INSERT INTO ntticketsites (`ticket_id`, `site_id`, `created_date`, `is_active`) VALUES  ('".$ticketid."','".$siteArr[0]['site_id']."',".$start_date.",1)";
                            $result6 = mysqli_query($conf, $query6);
                            if ($result6) {
                                echo "***** Site Added In Ticket ****";
                            }

                            /********** Add Ticket Technology ****************/

                            $tech = explode(",", $column[21]);
                            foreach ($tech as $key => $value) {
                                switch ($value) {
                                    case 'GSM':
                                    case 'CDMA':
                                        $query6 = "INSERT INTO ntticketnetworks (`ticket_id`, `network`, `created_date`, `is_active`) VALUES  ('".$ticketid."','2G',".$start_date.",1)";
                                        $result6 = mysqli_query($conf, $query6);
                                        if ($result6) {
                                            echo "***** 2G Network Added ****";
                                        }
                                        break;

                                    case 'UMTS': 
                                    case 'EVDO':
                                        $query6 = "INSERT INTO ntticketnetworks (`ticket_id`, `network`, `created_date`, `is_active`) VALUES  ('".$ticketid."','3G',".$start_date.",1)";
                                        $result6 = mysqli_query($conf, $query6);
                                        if ($result6) {
                                            echo "***** 3G Network Added ****";
                                        }
                                        break;

                                    default: break;
                                }
                            }

                            echo "<br/>";

                        } // EOF For loop sites


                    }else{

                       $start_date = ($column[16]!='')? "'".date('Y-m-d H:i:s',strtotime($column[16]))."'":"'".date('Y-m-d H:i:s',strtotime($column[1]))."'";
                       $query6 = "INSERT INTO ntticketsites (`ticket_id`, `site_id`, `created_date`, `is_active`) VALUES  ('".$ntTicketIds[$column[5]]."','".$siteArr[0]['site_id']."',".$start_date.",1)";
                        $result6 = mysqli_query($conf, $query6);

                        if($result6){
                            echo "***** Duplicate Ticket Site Added ****".$column[6];
                        }

                        echo "<br/>";
                    }

                    /*$txt = "ID : ".$column[0]." Site: ".$column[6]." TicketId: ".$column[5]." \n";
                    fwrite($myfile, $txt);*/
                    
                }else{
                    $txt = "ID : ".$column[0]." Site: ".$column[6]." TicketId: ".$column[5]." \n";

                    fwrite($myfile, $txt);
                    fputcsv($myExlfile, $column);
                }
            }

            //echo $column[0]."\n";        
        }
        fclose($myfile);
        fclose($myExlfile);
        echo "Done";

    }
}
?>