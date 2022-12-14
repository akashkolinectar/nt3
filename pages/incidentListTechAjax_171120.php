<?php 
require_once('../webservices/wbdb.php');

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$tech = isset($_POST['tech'])? $_POST['tech']:array(); // Tech value

$ajax = array();
//$ajax = array(array('ticket_number'=>'lavasa','title'=>'pune','province'=>'hill','site_principal'=>'2541','dependent'=>'test1','reason'=>'delete','2g'=>'delete','3g'=>'delete','4g'=>'delete','duration'=>'delete',));

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (st.site_name LIKE '%".$searchValue."%' OR 
        tk.title LIKE '%".$searchValue."%' OR 
        tk.ref LIKE '%".$searchValue."%' OR 
        rs.reason LIKE '%".$searchValue."%' OR 
        tk.start_date LIKE '%".$searchValue."%' OR 
        pr.province LIKE'%".$searchValue."%' ) ";
}

if(!empty($tech)){
    $techStr = "'" . implode ( "', '", $tech ) . "'";
    $searchQuery .= " AND tknw.network IN ($techStr)";
}

$openTickets = CMDBSource::QueryToArray("SELECT tk.id,tk.ref,tk.title,tk.start_date,rs.reason,pr.province,st.site_name,inc.priority,inc.status as incstatus FROM ntticket tk LEFT JOIN ntticket_incident inc ON inc.id=tk.id LEFT JOIN ntreason rs ON rs.reason_id=tk.reason_id LEFT JOIN ntsiteprovince pr ON pr.province_id = tk.province_id LEFT JOIN ntticketsites ts ON ts.ticket_id=tk.id LEFT JOIN ntsites st ON st.site_id=ts.site_id LEFT JOIN ntticketnetworks tknw ON tknw.ticket_id=tk.id WHERE tk.operational_status = 'ongoing' AND tk.finalclass='Incident'".$searchQuery." GROUP BY tk.id ORDER BY tk.id DESC LIMIT ".$row.",".$rowperpage); 

$i = 0;
$ticketIds = array();
if(!empty($openTickets)){
  foreach ($openTickets as $aDBInfo) { 

    if(!in_array($aDBInfo['id'], $ticketIds)){

        array_push($ticketIds, $aDBInfo['id']);
        $TWG = FALSE; $TRG = FALSE; $FRG = FALSE;
        $tech = CMDBSource::QueryToArray("SELECT * FROM ntticketnetworks WHERE ticket_id=".$aDBInfo['id']);
        if(!empty($tech)){
          foreach ($tech as $rows) {
            switch ($rows['network']) {
              case '2G': $TWG = TRUE; break;
              case '3G': $TRG = TRUE; break;
              case '4G': $FRG = TRUE; break;
            } // EOF Tech (2g,3g,4g) Switch
          } // EOF Tech (2g,3g,4g) Foreach
        }// EOF Empty Tech Check

        $temp['ticket_number'] = "<a href='https://nt3.nectarinfotel.com/pages/UI.php?operation=details&class=Incident&id=".$aDBInfo['id']."&c[menu]=Incident%3AOpenIncidents' class='siteDetails'>".$aDBInfo['ref']."</a>";
        $temp['title'] = $aDBInfo['title'];
        $temp['province'] = $aDBInfo['province'];

        /***************** Site Pricipal Details **********************/
        $allSites = array();
        $dependantSites = array();
        $site_principal = "";
        $sites = CMDBSource::QueryToArray("SELECT st.site_name,st.site_id,st.parent_site FROM ntticketsites ts LEFT JOIN ntsites st ON st.site_id=ts.site_id WHERE ts.ticket_id=".$aDBInfo['id']." AND ts.is_active = 1");
        if(!empty($sites)){
          $allSites = array_column($sites, 'site_id');
          foreach ($sites as $rows) {                 
            if($rows['parent_site']!=0){
              if(!in_array($rows['parent_site'], $allSites)){
                $site_principal .= $rows['site_name']."<br/>";
              }else{
                array_push($dependantSites, $rows['site_name']);
              }
            }else{
              $site_principal .= $rows['site_name']."<br/>";
            }

            /************** Check Site Network is Available Or Not ****************/
              $availableNw = array();
              $siteNetwork = CMDBSource::QueryToArray("SELECT sn.network,sn.site_id FROM ntsitenetwork sn WHERE sn.site_id=".$rows['site_id']." AND sn.is_active = 1");
              if(!empty($siteNetwork)){
                foreach ($siteNetwork as $nRows) {
                  array_push($availableNw, $nRows['network']);
                }
              }
            /************** EOF Check Site Network is Available Or Not ****************/
          }
        }
        $temp['site_principal'] = $site_principal;

        /***************** Site Dependant Details **********************/
        $dependent = "";
        if(!empty($dependantSites)){
            foreach ($dependantSites as $key => $val) {
              $dependent .= $val."<br/>";
            }
          }

        $temp['dependent'] = $dependent;  

        $temp['reason'] = $aDBInfo['reason'];
        
        if(!isset($availableNw)){
          $availableNw = array();
        }

        $temp['2g'] = ($TWG==TRUE && in_array('2G', $availableNw))? "<span class='inactive'>DOWN</span>":(in_array('2G', $availableNw)? "<span class='active'>UP</span>":"<span class='notavailable'>NA</span>");
        $temp['3g'] = ($TRG==TRUE && in_array('3G', $availableNw))? "<span class='inactive'>DOWN</span>":(in_array('3G', $availableNw)? "<span class='active'>UP</span>":"<span class='notavailable'>NA</span>");
        $temp['4g'] = ($FRG==TRUE && in_array('4G', $availableNw))? "<span class='inactive'>DOWN</span>":(in_array('4G', $availableNw)? "<span class='active'>UP</span>":"<span class='notavailable'>NA</span>");

        $date = new DateTime($aDBInfo['start_date']);
        $now = new DateTime();

        $ageData = $now->diff($date);

        if($ageData->format('%y')!=0){
          //$age = $ageData->format('%y Year %m Month %d Day %h Hr %i Min %s Sec');
          $age = $ageData->format('%a Day %h Hr');
        }else if($ageData->format('%d')!=0){
          //$age = $ageData->format('%m Month %d Day %h Hr %i Min %s Sec');
          $age = $ageData->format('%a Day %h Hr %i Min');
        }else if($ageData->format('%d')!=0){
          $age = $ageData->format('%a Day %h Hr %i Min');
        }else if($ageData->format('%d')==0){
          if($ageData->format('%h')==0){
            $age = $ageData->format('%i Min');
          }else{
            $age = $ageData->format('%h Hr %i Min');
          }
        } 
        $temp['duration'] = $age;

        $status = ucwords($aDBInfo['incstatus']);
        $slt = CMDBSource::QueryToArray("SELECT * FROM ntslt slt LEFT JOIN ntlnkslatoslt slaslt ON slaslt.slt_id=slt.id WHERE slaslt.sla_id=1");
        if(!empty($slt)){
            foreach ($slt as $rows) {
              switch (TRUE) {
                case (($aDBInfo['priority']==1 || $aDBInfo['priority']==2) && $rows['priority']==1 && $rows['metric']=='tto'):
                  if(($aDBInfo['incstatus']=='escalated_tto' || $aDBInfo['incstatus']=='new') && date('Y-m-d H:i:s')>=date('Y-m-d H:i:s',strtotime($aDBInfo['start_date']." +".$rows['value']." ".$rows['unit']))){
                    $status = 'Fim do SLA para tempo de atribui????o';
                  }
                  break;
                case (($aDBInfo['priority']==1 || $aDBInfo['priority']==2) && $rows['priority']==1 && $rows['metric']=='ttr'):
                  if(($aDBInfo['incstatus']!='escalated_tto' || $aDBInfo['incstatus']!='new') && date('Y-m-d H:i:s')>=date('Y-m-d H:i:s',strtotime($aDBInfo['start_date']." +".$rows['value']." ".$rows['unit']))){
                    $status = 'T??rmino do SLA por tempo de resolu????o';
                  }
                  break;

                case ($aDBInfo['priority']==3 && $rows['priority']==3 && $rows['metric']=='tto'): 
                  if(($aDBInfo['incstatus']=='escalated_tto' || $aDBInfo['incstatus']=='new') && date('Y-m-d H:i:s')>=date('Y-m-d H:i:s',strtotime($aDBInfo['start_date']." +".$rows['value']." ".$rows['unit']))){
                    $status = 'Fim do SLA para tempo de atribui????o';
                  }
                  break;

                case ($aDBInfo['priority']==3 && $rows['priority']==3 && $rows['metric']=='ttr'): 
                  if(($aDBInfo['incstatus']!='escalated_tto' || $aDBInfo['incstatus']!='new') && date('Y-m-d H:i:s')>=date('Y-m-d H:i:s',strtotime($aDBInfo['start_date']." +".$rows['value']." ".$rows['unit']))){
                    $status = 'T??rmino do SLA por tempo de resolu????o';
                  }
                break;

                case ($aDBInfo['priority']==4 && $rows['priority']==4 && $rows['metric']=='tto'): 
                  if(($aDBInfo['incstatus']=='escalated_tto' || $aDBInfo['incstatus']=='new') && date('Y-m-d H:i:s')>=date('Y-m-d H:i:s',strtotime($aDBInfo['start_date']." +".$rows['value']." ".$rows['unit']))){
                    $status = 'Fim do SLA para tempo de atribui????o';
                  }
                break;

                case ($aDBInfo['priority']==4 && $rows['priority']==4 && $rows['metric']=='ttr'): 
                if(($aDBInfo['incstatus']!='escalated_tto' || $aDBInfo['incstatus']!='new') && date('Y-m-d H:i:s')>=date('Y-m-d H:i:s',strtotime($aDBInfo['start_date']." +".$rows['value']." ".$rows['unit']))){
                    $status = 'T??rmino do SLA por tempo de resolu????o';
                  }
                break;
              }
            }
        }

        $temp['status'] = $status;

        if(!empty($tech)){
           foreach ($tech as $tkey=>$tval) {
              switch($tval){
                case "2G": 
                if(in_array('2G', $availableNw) && !in_array('3G', $availableNw) && !in_array('4G', $availableNw)){
                  array_push($ajax, $temp);
                }
                break;
                case "3G": 
                if(in_array('3G', $availableNw) && !in_array('2G', $availableNw) && !in_array('4G', $availableNw)){
                  array_push($ajax, $temp);
                }
                break;
                case "4G": 
                if(in_array('4G', $availableNw) && !in_array('2G', $availableNw) && !in_array('3G', $availableNw)){
                  array_push($ajax, $temp);
                }
                break;
              }
           }
        }else{
          array_push($ajax, $temp);
        }

        $i++;
    }// EOF Check ticket id in array or not
    

  }// EOF Foreach Open Tickets
}// EOF Empty Check Open Tickets


$openTicketsCount = "SELECT COUNT(*) as records FROM ntticket tk WHERE tk.operational_status = 'ongoing' AND tk.finalclass='Incident'";
$result = mysqli_query($conf, $openTicketsCount);
$cntTkt = $result->fetch_all(MYSQLI_ASSOC);
$totalRecords = $cntTkt[0]['records'];


$openTicketsCount = "SELECT COUNT(*) as records FROM ntticket tk WHERE tk.operational_status = 'ongoing' AND tk.finalclass='Incident'";
/*$openTicketsCount = "SELECT COUNT(*) as records FROM ntticket tk WHERE tk.operational_status = 'ongoing' AND tk.finalclass='Incident'".$searchQuery;*/
$result = mysqli_query($conf, $openTicketsCount);
$cntTkt = $result->fetch_all(MYSQLI_ASSOC);
$totalRecordwithFilter = $cntTkt[0]['records'];


/*
echo "<pre>";
print_r($cntTkt);
echo 'totalRecords'.$totalRecords;
echo 'totalRecordwithFilter: '.$totalRecordwithFilter;*/

$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $ajax
);

echo json_encode($response);

?>