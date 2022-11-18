<?php
require_once('../webservices/wbdb.php');
require_once('../webservices/PHPExcel/Classes/PHPExcel.php');


function datediff($interval, $datefrom, $dateto, $using_timestamps = false)
{
    /*
    $interval can be:
    yyyy - Number of full years
    q    - Number of full quarters
    m    - Number of full months
    y    - Difference between day numbers
           (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
    d    - Number of full days
    w    - Number of full weekdays
    ww   - Number of full weeks
    h    - Number of full hours
    n    - Number of full minutes
    s    - Number of full seconds (default)
    */

    if (!$using_timestamps) {
        $datefrom = strtotime($datefrom, 0);
        $dateto   = strtotime($dateto, 0);
    }

    $difference        = $dateto - $datefrom; // Difference in seconds
    $months_difference = 0;

    switch ($interval) {
        case 'yyyy': // Number of full years
            $years_difference = floor($difference / 31536000);
            if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
                $years_difference--;
            }

            if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
                $years_difference++;
            }

            $datediff = $years_difference;
        break;

        case "q": // Number of full quarters
            $quarters_difference = floor($difference / 8035200);

            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                $months_difference++;
            }

            $quarters_difference--;
            $datediff = $quarters_difference;
        break;

        case "m": // Number of full months
            $months_difference = floor($difference / 2678400);

            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                $months_difference++;
            }

            $months_difference--;

            $datediff = $months_difference;
        break;

        case 'y': // Difference between day numbers
            $datediff = date("z", $dateto) - date("z", $datefrom);
        break;

        case "d": // Number of full days
            $datediff = floor($difference / 86400);
        break;

        case "w": // Number of full weekdays
            $days_difference  = floor($difference / 86400);
            $weeks_difference = floor($days_difference / 7); // Complete weeks
            $first_day        = date("w", $datefrom);
            $days_remainder   = floor($days_difference % 7);
            $odd_days         = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?

            if ($odd_days > 7) { // Sunday
                $days_remainder--;
            }

            if ($odd_days > 6) { // Saturday
                $days_remainder--;
            }

            $datediff = ($weeks_difference * 5) + $days_remainder;
        break;

        case "ww": // Number of full weeks
            $datediff = floor($difference / 604800);
        break;

        case "h": // Number of full hours
            $datediff = floor($difference / 3600);
        break;

        case "n": // Number of full minutes
            $datediff = floor($difference / 60);
        break;

        default: // Number of full seconds (default)
            $datediff = $difference;
        break;
    }

    return $datediff;
}

  $objPHPExcel = new PHPExcel();
  $objPHPExcel->setActiveSheetIndex(0);
  $objPHPExcel->getActiveSheet()->setTitle("Site Tickets");              
  $objPHPExcel->getActiveSheet()->SetCellValue('A1', "ID");
  $objPHPExcel->getActiveSheet()->SetCellValue('B1', "Availability Date");
  $objPHPExcel->getActiveSheet()->SetCellValue('C1', "Week Number");
  $objPHPExcel->getActiveSheet()->SetCellValue('D1', "Month");
  $objPHPExcel->getActiveSheet()->SetCellValue('E1', "Year");
  $objPHPExcel->getActiveSheet()->SetCellValue('F1', "Ticket ID");
  $objPHPExcel->getActiveSheet()->SetCellValue('G1', "Name");
  $objPHPExcel->getActiveSheet()->SetCellValue('H1', "Dependence");
  $objPHPExcel->getActiveSheet()->SetCellValue('I1', "Net Type");
  $objPHPExcel->getActiveSheet()->SetCellValue('J1', "Manufacturer");
  $objPHPExcel->getActiveSheet()->SetCellValue('K1', "Provincia");
  $objPHPExcel->getActiveSheet()->SetCellValue('L1', "Evento");
  $objPHPExcel->getActiveSheet()->SetCellValue('M1', "Serviço");
  $objPHPExcel->getActiveSheet()->SetCellValue('N1', "Network");
  $objPHPExcel->getActiveSheet()->SetCellValue('O1', "Reason");
  $objPHPExcel->getActiveSheet()->SetCellValue('P1', "Sub Reason");
  $objPHPExcel->getActiveSheet()->SetCellValue('Q1', "Time OOS");
  $objPHPExcel->getActiveSheet()->SetCellValue('R1', "Time INS");
  $objPHPExcel->getActiveSheet()->SetCellValue('S1', "Total OOS");
  $objPHPExcel->getActiveSheet()->SetCellValue('T1', "Hours OOS");
  $objPHPExcel->getActiveSheet()->SetCellValue('U1', "Entity Dependency");
  $objPHPExcel->getActiveSheet()->SetCellValue('V1', "tecnologys");

  $styleArr = array(
                  'fill' => array(
                      'type' => PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => array('rgb' => '422462')
                  ),
                  'font'  => array(
                      'bold'  => true,
                      'color' => array('rgb' => 'F17422'),
                      /*'size'  => 15,
                      'name'  => 'Verdana'*/
                  ),
                  'alignment' => array(
                      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                      'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                  ),
                  'borders' => array(
                      'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => 'FFFFFF')
                      )
                  )
              );
      $objPHPExcel->getActiveSheet()->getStyle('A1:V1')->applyFromArray($styleArr);
      $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(-1);       

       $from = "";
       if(isset($_GET['from']) && $_GET['from']!=""){
        $from = " AND DATE(tk.start_date) >= '".date('Y-m-d',strtotime($_GET['from']))."'";
       }         

       $to = "";
       if(isset($_GET['to']) && $_GET['to']!=""){
        $to = " AND DATE(tk.start_date) <= '".date('Y-m-d',strtotime($_GET['to']))."'";
       }

       $query = CMDBSource::QueryToArray("SELECT tk.id as ticketid,tk.start_date,tk.close_date,tk.ref,st.site_name,st.element_type,st.vendor,stpr.province,ev.event,res.reason,subres.sub_reason,ser.name as service,st.bsc,st.rnc,st.mgw,st.msc,st.parent_site,CASE tk.dependance
        WHEN 'bsc' THEN bsc.bsc
        WHEN 'msc' THEN msc.msc
        WHEN 'rnc' THEN rnc.rnc
        WHEN 'mgw' THEN mgw.mgw
        ELSE ''
        end dependanceName,
        st.nettype,st.network,tk.component_type,tk.rede
          FROM ntticketsites tkst
          left join ntsites st on st.site_id = tkst.site_id
          left join ntticket tk on tk.id = tkst.ticket_id
          left join ntsitebsc bsc on bsc.bsc_id = tk.dependance_id 
          left join ntsitemsc msc on msc.msc_id = tk.dependance_id 
          left join ntsiternc rnc on rnc.rnc_id = tk.dependance_id 
          left join ntsitemgw mgw on mgw.mgw_id = tk.dependance_id 
          left join ntsiteprovince stpr on st.province=stpr.province_id
          left join ntevent ev on ev.event_id=tk.event_id
          left join ntreason res on res.reason_id=tk.reason_id
          left join ntsubreason subres on subres.sub_reason_id=tk.sub_reason_id
          left join ntticket_incident inc on inc.id=tk.id 
          left join ntservice ser on ser.id=inc.service_id 
          where (st.is_active=1 ".$from." ".$to." AND tk.operational_status='closed' AND tk.finalclass='Incident') OR (tk.operational_status='ongoing' ".$to." AND tk.finalclass='Incident') ORDER BY tk.id DESC");

       /*echo '<pre>';
       print_r($query);*/
       
       
       if(!empty($query)){
          $rowCount = 2; $i = 1;
          foreach ($query as $aDBInfo) { 

              $pSite = CMDBSource::QueryToArray("SELECT site_name FROM ntsites WHERE site_id=".$aDBInfo['parent_site']);

              $today = date('Y-m-d H:i:s');
              //$week = date( 'W', strtotime($today) ) - date( 'W', strtotime($aDBInfo['start_date']) );
              //$month = date( 'm', strtotime($today) ) - date( 'm', strtotime($aDBInfo['start_date']) );
              /*$month = date( 'm', strtotime($today) ) - date( 'm', strtotime('first day of January ' . date('Y')) );*/

              //$week = date( 'W', strtotime($today) ) - date( 'W', strtotime('first day of January ' . date('Y')) );
              $week = datediff('ww', date( 'Y-m-d H:i:s', strtotime('first day of January ' . date('Y')) ), $today, false)+1;


              $year = date( 'Y', strtotime($aDBInfo['start_date']) ); 
              
              $timeOOS = date('Y-m-d H:i',strtotime($aDBInfo['start_date'])); 
              $timeINS = '';

              if($aDBInfo['close_date']!=''){
                $timeINS = date('Y-m-d H:i',strtotime($aDBInfo['close_date']));
                $hourOOS = round((strtotime($aDBInfo['close_date']) - strtotime($aDBInfo['start_date']))/3600, 1);
                $totalOOS = round(((strtotime($aDBInfo['close_date']) - strtotime($aDBInfo['start_date']))/3600)*60, 1);

              }else{
                /*$hourOOS = round((strtotime(date('Y-m-d H:i:s')) - strtotime($aDBInfo['start_date']))/3600, 1);
                $totalOOS = round(((strtotime(date('Y-m-d H:i:s')) - strtotime($aDBInfo['start_date']))/3600)*60, 1);*/
                /*if($_GET['to']==$_GET['from']){
                    if(date('Y-m-d',strtotime($aDBInfo['start_date']))==date('Y-m-d')){
                        $hourOOS = round((strtotime(date('Y-m-d H:i:s')) - strtotime($aDBInfo['start_date']))/3600, 1);
                        $totalOOS = round(((strtotime(date('Y-m-d H:i:s')) - strtotime($aDBInfo['start_date']))/3600)*60, 1);
                    }else{
                      $hourOOS = 24;
                      $totalOOS = 1440;
                    }
                }else{*/
                  $to = $_GET['to']." 23:59:59";
                  if($_GET['to']==$_GET['from']){
                    $to = date('Y-m-d H:i:s',strtotime($_GET['to'].' +1 day'));
                  }
                  if(date('Y-m-d',strtotime($aDBInfo['start_date']))==date('Y-m-d') || date('Y-m-d',strtotime($aDBInfo['start_date']))==date('Y-m-d',strtotime('-1 day'))){
                    $hourOOS = round((strtotime(date('Y-m-d H:i:s')) - strtotime($aDBInfo['start_date']))/3600, 1);
                    $totalOOS = round(((strtotime(date('Y-m-d H:i:s')) - strtotime($aDBInfo['start_date']))/3600)*60, 1);
                  }else{
                    /*$hourOOS = round((strtotime($to) - strtotime($_GET['from']))/3600, 1);
                    $totalOOS = round(((strtotime($to) - strtotime($_GET['from']))/3600)*60, 1);*/
                    $hourOOS = round((strtotime($to) - strtotime($aDBInfo['start_date']))/3600, 1);
                    $totalOOS = round(((strtotime($to) - strtotime($aDBInfo['start_date']))/3600)*60, 1);
                  }
                    //echo $aDBInfo['site_name']."******".$aDBInfo['start_date']."******".$hourOOS."<br/>";
                /*}*/
              }


              if(date('Y-m-d',strtotime($aDBInfo['start_date']))>=date('Y-m-d',strtotime($_GET['from']))){
                  $startDate = date('Y-m-d',strtotime($aDBInfo['start_date']));
              }else{
                  $startDate = date('Y-m-d',strtotime($_GET['from']));
              }



              $entityDependancy = $aDBInfo['site_name'];
              if(!empty($pSite)){
                if(isset($pSite[0]['site_name'])){
                    $entityDependancy = $pSite[0]['site_name'];
                }
              }
              
              $tech = ''; $dependanceSite=$aDBInfo['bsc'];
              $q2 = CMDBSource::QueryToArray("SELECT network FROM ntticketnetworks WHERE ticket_id=".$aDBInfo['ticketid']." AND is_active=1");
              if(!empty($q2)){
                foreach ($q2 as $rows) {
                  $tech .= $rows['network'].',';
                  /********** Get Dependance According To BSC/RNC ***************/
                  switch ($rows['network']) {
                    case '2G': 
                      $dependanceSite=$aDBInfo['bsc'];
                      break;
                    case '3G':
                    case '4G': 
                      $dependanceSite=$aDBInfo['rnc'];
                      break;
                    default:
                      $dependanceSite=$aDBInfo['bsc'];
                      break;
                  }
                }
              }
              $tech = rtrim($tech,',');
              
              $service = '';
              $services = CMDBSource::QueryToArray("SELECT ser.service_aftd FROM ntticketserviceaffected sertk LEFT JOIN ntserviceaftd ser ON ser.service_aftd_id=sertk.service_aftd_id WHERE sertk.ticket_id=".$aDBInfo['ticketid']);
              if(!empty($services)){
                foreach ($services as $rows) {
                    $service .= $rows['service_aftd'].',';
                }
              }
              $service = rtrim($service,',');

               $j=0;
               if($hourOOS>24){

                while ($hourOOS>=24) { 
                  /*while ($hourOOS>=24 && date('Y-m-d',strtotime($aDBInfo['start_date']." +".$j." days"))<=date('Y-m-d',strtotime($_GET['to'])) && 
                      date('Y-m-d',strtotime($aDBInfo['start_date']." +".$j." days"))>=date('Y-m-d',strtotime($_GET['from']))) {*/
                  $hourOOS = $hourOOS-24;
                  $startDate = date('Y-m-d',strtotime($_GET['from']." +".$j." days"));
                  
                  if($startDate>date('Y-m-d',strtotime($_GET['to']))){
                    $hourOOS=0;
                    continue;
                  }

                  //echo 'Site: '.$aDBInfo['site_name'].' To Date: '.date('Y-m-d',strtotime($_GET['to'])).' Start Date: '.$startDate."<br/>";


                  if($startDate<=date('Y-m-d',strtotime($_GET['to']))){
                      
                      $totalOOS = round($hourOOS*60,2);
                      $month = date( 'm', strtotime($startDate) );

                      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $i);
                      $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $startDate);
                      $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $week);
                      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $month);
                      $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $year);
                      $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $aDBInfo['ref']);
                      $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $aDBInfo['site_name']);
                      $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $dependanceSite);
                      /*$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $aDBInfo['dependanceName']);*/
                      $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $aDBInfo['component_type']);
                      $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $aDBInfo['vendor']);
                      $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $aDBInfo['province']);
                      $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $aDBInfo['event']);
                      $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $service);
                      $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $aDBInfo['rede']);
                      $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $aDBInfo['reason']);
                      $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $aDBInfo['sub_reason']);
                      $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $timeOOS);
                      $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $timeINS);
                      $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, 1440);
                      $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, 24);
                      /*$objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $totalOOS);
                      $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $hourOOS);*/
                      $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $entityDependancy);
                      $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $tech);
                      $rowCount++; $i++; $j++;
                  }
                  
                }
              }

              if($hourOOS!=0 && date('Y-m-d',strtotime($startDate." +".$j." days"))<=date('Y-m-d',strtotime($_GET['to']))){
              //if($hourOOS!=0 && date('Y-m-d',strtotime($aDBInfo['start_date']." +".$j." days"))<=date('Y-m-d',strtotime($_GET['to']))){
                  $mStDate = date('Y-m-d',strtotime($startDate." +".$j." days"));
                  $month = date( 'm', strtotime($mStDate) );

                  $totalOOS = round($hourOOS*60,2);
                  $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $i);
                  $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, date('Y-m-d',strtotime($startDate." +".$j." days")));
                  $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $week);
                  $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $month);
                  $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $year);
                  $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $aDBInfo['ref']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $aDBInfo['site_name']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $dependanceSite);
                  $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $aDBInfo['component_type']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $aDBInfo['vendor']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $aDBInfo['province']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $aDBInfo['event']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $service);
                  $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $aDBInfo['rede']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $aDBInfo['reason']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $aDBInfo['sub_reason']);
                  $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $timeOOS);
                  $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $timeINS);
                  $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $totalOOS);
                  $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $hourOOS);
                  $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $entityDependancy);
                  $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $tech);
                  $rowCount++; $i++;
              }
          }
       }

  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
  
  $objPHPExcel->getActiveSheet()->freezePane('H2');

  $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
  header('Content-type: application/vnd.ms-excel');
  header('Content-Disposition: attachment; filename="SiteTickets.xls"');
  $objWriter->save('php://output');
?>