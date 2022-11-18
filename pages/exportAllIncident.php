<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require_once('../webservices/wbdb.php');

$dateFilter = "";
if(isset($_GET['from']) && isset($_GET['to']) && $_GET['to']!='' && $_GET['from']!=''){
    $dateFilter = " AND DATE(tk.start_date)>= '".$_GET['from']."' AND tk.start_date <= '".$_GET['to']."'";
}

$siteDown = CMDBSource::QueryToArray("SELECT DISTINCT tk.id,st.site_name,tk.ref,tk.title,pr.province,tk.start_date,tk.close_date,tk.operational_status,rs.reason,sbrs.sub_reason FROM ntticketsites ts LEFT JOIN ntsites st ON st.site_id=ts.site_id LEFT JOIN ntticket tk ON tk.id=ts.ticket_id LEFT JOIN ntreason rs ON rs.reason_id=tk.reason_id LEFT JOIN ntsubreason sbrs ON sbrs.sub_reason_id=tk.sub_reason_id LEFT JOIN ntsiteprovince pr ON pr.province_id = tk.province_id WHERE tk.finalclass='Incident' AND ts.is_active=1 $dateFilter ORDER BY st.site_name ASC");

require_once('../webservices/wbdb.php');
require_once('../webservices/PHPExcel/Classes/PHPExcel.php');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="IncidentReport.xlsx"');
header('Cache-Control: max-age=0');
ob_end_clean(); 
ob_start();

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();
PHPExcel_Calculation::getInstance()->writeDebugLog = true;

$sheet->setTitle("IncidentReport");           
              
$sheet->SetCellValue('A1', "Ticket ID");
$sheet->SetCellValue('B1', "Title");
$sheet->SetCellValue('C1', "Site");
$sheet->SetCellValue('D1', "Province");
$sheet->SetCellValue('E1', "Reason");
$sheet->SetCellValue('F1', "Sub Reason");
$sheet->SetCellValue('G1', "Created Date");
$sheet->SetCellValue('H1', "Close Date");
$sheet->SetCellValue('I1', "Duration");
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
$sheet->getStyle('A1:I1')->applyFromArray($styleArr);
$sheet->getRowDimension('1')->setRowHeight(-1);

$rowCount = 2;

$xAxisTickValues = array();
$dataseriesLabels = array();
$dataSeriesValues = array();
if(!empty($siteDown)){
  arsort($siteDown);
  foreach ($siteDown as $rows) {
      
      $endDate = date("Y-m-d H:i:s");
      $closeDate = "-";
      if($rows['operational_status']=='closed'){
        $endDate = date('Y-m-d H:i:s',strtotime($rows['close_date']));
        $closeDate = date('Y-m-d H:i:s',strtotime($rows['close_date']));
      }

      $date = new DateTime($rows['start_date']);
      $now = new DateTime($endDate);
      /*$age = $now->diff($date);
      $ageData = $age->format('%d Day %h Hr %i Min %s Sec');
      $ageData = strpos($ageData, '0 Day')!==FALSE? (strpos($age->format('%h Hr %i Min'), '0 Hr')!==FALSE? $age->format('%i Min'):$age->format('%h Hr %i Min')):$age->format('%d Day %h Hr %i Min');
*/
      $ageData = $now->diff($date);
      if($ageData->format('%y')!=0){
        //$age = $ageData->format('%a Day %h Hr');
        $ageSplit = ($ageData->format('%a')*24)+($ageData->format('%h'));
        $age = $ageSplit.".".$ageData->format('%i');
      }else if($ageData->format('%d')!=0){
       // $age = $ageData->format('%a Day %h Hr %i Min');
        $ageSplit = ($ageData->format('%a')*24)+($ageData->format('%h'));
        $age = $ageSplit.".".$ageData->format('%i');
      }else if($ageData->format('%d')!=0){
        $ageSplit = ($ageData->format('%a')*24)+($ageData->format('%h'));
        $age = $ageSplit.".".$ageData->format('00.%i');
      }else if($ageData->format('%d')==0){
        if($ageData->format('%h')==0){
          $age = $ageData->format('00.%i');
        }else{
          $age = $ageData->format('%h.%i');
        }
      }

      $sheet->SetCellValue('A'.$rowCount, $rows['ref']);
      $sheet->SetCellValue('B'.$rowCount, $rows['title']);
      $sheet->SetCellValue('C'.$rowCount, utf8_encode($rows['site_name']));
      $sheet->SetCellValue('D'.$rowCount, $rows['province']);
      $sheet->SetCellValue('E'.$rowCount, $rows['reason']);
      $sheet->SetCellValue('F'.$rowCount, $rows['sub_reason']);
      $sheet->SetCellValue('G'.$rowCount, $rows['start_date']);
      $sheet->SetCellValue('H'.$rowCount, $closeDate);
      $sheet->SetCellValue('I'.$rowCount, $age);
      $rowCount++;
  }

}

$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$writer->setIncludeCharts(TRUE);

$writer->save('php://output');

?>