<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require_once('../webservices/wbdb.php');


/******************************************** Top 15 Site Down ********************************************************/

$siteDown = CMDBSource::QueryToArray("SELECT st.site_name,count(ts.ticket_id) AS total FROM ntticketsites ts LEFT JOIN ntsites st ON st.site_id=ts.site_id LEFT JOIN ntticket tk ON tk.id=ts.ticket_id WHERE tk.finalclass='Incident' AND ts.is_active=1 AND DATE(tk.start_date)>='".date('Y-m-d',strtotime('-3 months'))."' GROUP BY ts.site_id ORDER BY total DESC LIMIT 15");

//LEFT JOIN ntticket_incident inc ON inc.id=tk.id LEFT JOIN ntreason rs ON rs.reason_id=tk.reason_id LEFT JOIN ntsiteprovince pr ON pr.province_id = tk.province_id LEFT JOIN ntticketsites ts ON ts.ticket_id=tk.id

require_once('../webservices/wbdb.php');
require_once('../webservices/PHPExcel/Classes/PHPExcel.php');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="siteDownOcRep.xlsx"');
header('Cache-Control: max-age=0');
ob_end_clean(); 
ob_start();

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();
PHPExcel_Calculation::getInstance()->writeDebugLog = true;

$sheet->setTitle("Top_15");           
              
$sheet->SetCellValue('A1', "Nº");
$sheet->SetCellValue('B1', "Site");
$sheet->SetCellValue('C1', "Número de Quedas");
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
$sheet->getStyle('A1:C1')->applyFromArray($styleArr);
$sheet->getRowDimension('1')->setRowHeight(-1);

$rowCount = 2; $i = 1;

$xAxisTickValues = array();
$dataseriesLabels = array();
$dataSeriesValues = array();
if(!empty($siteDown)){
  arsort($siteDown);
  foreach ($siteDown as $rows) {
      
      $sheet->SetCellValue('A'.$rowCount, $i);
      $sheet->SetCellValue('B'.$rowCount, $rows['site_name']);
      $sheet->SetCellValue('C'.$rowCount, $rows['total']);

      $rowCount++; $i++;
  }

}

/*$dataseriesLabels = array(
    new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', NULL, 1),   //  2010
    new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', NULL, 1),   //  2011
    new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$D$1', NULL, 1),   //  2012
);*/

array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top_15!$C$1', NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top_15!$C$2:$C$'.($rowCount-1), NULL, 1));
array_push($xAxisTickValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top_15!$A$2:$B$'.($rowCount-1), NULL, ($rowCount-1)));
/*$xAxisTickValues = array(
    new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$A$2:$A$5', NULL, 4),
    new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$A$2:$A$5', NULL, 4),
    new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$A$2:$A$5', NULL, 4),
    new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$A$2:$A$5', NULL, 4)
);*/

/*$dataSeriesValues = array(
    new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$5', NULL, ($rowCount-1)),
    new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$C$5', NULL, ($rowCount-1))
);*/

$series = new PHPExcel_Chart_DataSeries(
//PHPExcel_Chart_DataSeries::TYPE_AREACHART,       // plotType
//PHPExcel_Chart_DataSeries::TYPE_LINECHART,       // plotType
//PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
//PHPExcel_Chart_DataSeries::GROUPING_STACKED,  // plotGrouping
//PHPExcel_Chart_DataSeries::GROUPING_STANDARD,  // plotGrouping
PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
//range(0, count($dataSeriesValues)-1), // plotOrder
//array(0,1,2,3,4,5,6,7,8,9),
range(0, count($dataSeriesValues)-1),
$dataseriesLabels,
$xAxisTickValues,
$dataSeriesValues
);
$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_VERTICAL);  // DIRECTION_BAR

$layout = new PHPExcel_Chart_Layout();
$layout->setShowVal(TRUE);
$layout->setShowPercent(TRUE);
$plotarea = new PHPExcel_Chart_PlotArea($layout, array($series));

$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

$title = new PHPExcel_Chart_Title('Número de Quedas de Sites do Backbone & Agregadores de '.date("d/M Y",strtotime("-3 months")).' à '.date("d/M Y"));
$yAxisLabel = new PHPExcel_Chart_Title('Ocorrências de inatividade do site');

$chart = new PHPExcel_Chart(
    'chart1',       // name
    $title,         // title
    $legend,        // legend
    $plotarea,      // plotArea
    true,           // plotVisibleOnly
    0,              // displayBlanksAs
    NULL,           // xAxisLabel
    $yAxisLabel     // yAxisLabel
);

$chart->setTopLeftPosition('E3');
$chart->setBottomRightPosition('T18');
$sheet->addChart($chart);

/******************************************** Top 100 Site Down ********************************************************/

$siteDown = CMDBSource::QueryToArray("SELECT st.site_name,count(ts.ticket_id) AS total FROM ntticketsites ts LEFT JOIN ntsites st ON st.site_id=ts.site_id LEFT JOIN ntticket tk ON tk.id=ts.ticket_id WHERE tk.finalclass='Incident' AND ts.is_active=1 AND DATE(tk.start_date)>='".date('Y-m-d',strtotime('-3 months'))."' GROUP BY ts.site_id ORDER BY total DESC LIMIT 100");

$sheet2 = $objPHPExcel->createSheet(2);
PHPExcel_Calculation::getInstance()->writeDebugLog = true;

$sheet2->setTitle("Geral");
$sheet2->SetCellValue('A1', "Nº");
$sheet2->SetCellValue('B1', "Site");
$sheet2->SetCellValue('C1', "Número de Quedas");
$styleArr = array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '422462')
                    ),
                    'font'  => array(
                        'bold'  => true,
                        'color' => array('rgb' => 'F17422'),
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
$sheet2->getStyle('A1:C1')->applyFromArray($styleArr);
$sheet2->getRowDimension('1')->setRowHeight(-1);

$rowCount = 2; $i = 1;
$xAxisTickValues = array();
$dataseriesLabels = array();
$dataSeriesValues = array();
if(!empty($siteDown)){

  arsort($siteDown);
  foreach ($siteDown as $rows) {
      
      $sheet2->SetCellValue('A'.$rowCount, $i);
      $sheet2->SetCellValue('B'.$rowCount, $rows['site_name']);
      $sheet2->SetCellValue('C'.$rowCount, $rows['total']);

      $rowCount++; $i++;
  }

}

array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Geral!$C$1', NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Geral!$C$2:$C$'.($rowCount-1), NULL, 1));
array_push($xAxisTickValues, new PHPExcel_Chart_DataSeriesValues('String', 'Geral!$A$2:$B$'.($rowCount-1), NULL, ($rowCount-1)));

$series = new PHPExcel_Chart_DataSeries(
PHPExcel_Chart_DataSeries::TYPE_LINECHART,       // plotType
PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
range(0, count($dataSeriesValues)-1), // plotOrder
$dataseriesLabels,
$xAxisTickValues,
$dataSeriesValues
);
$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_VERTICAL);  // DIRECTION_BAR
$layout = new PHPExcel_Chart_Layout();
$layout->setShowVal(TRUE);
$layout->setShowPercent(TRUE);
$plotarea = new PHPExcel_Chart_PlotArea($layout, array($series));
$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
$title = new PHPExcel_Chart_Title('Número de Quedas de Sites do Backbone & Agregadores de '.date("d/M Y",strtotime("-3 months")).' à '.date("d/M Y"));
$yAxisLabel = new PHPExcel_Chart_Title('Ocorrências de inatividade do site');
$chart = new PHPExcel_Chart(
    'chart1',       // name
    $title,         // title
    $legend,        // legend
    $plotarea,      // plotArea
    true,           // plotVisibleOnly
    0,              // displayBlanksAs
    NULL,           // xAxisLabel
    $yAxisLabel     // yAxisLabel
);

$chart->setTopLeftPosition('E3');
$chart->setBottomRightPosition('W25');
$sheet2->addChart($chart);

/******************************************** Top 15 Site Down Along With Reason *************************************************/


$siteDownRes = CMDBSource::QueryToArray("SELECT st.site_name,st.site_id,count(ts.ticket_id) AS total FROM ntticketsites ts LEFT JOIN ntsites st ON st.site_id=ts.site_id LEFT JOIN ntticket tk ON tk.id=ts.ticket_id WHERE tk.finalclass='Incident' AND ts.is_active=1 AND DATE(tk.start_date)>='".date('Y-m-d',strtotime('-3 months'))."' GROUP BY ts.site_id ORDER BY total DESC LIMIT 15");

if(!empty($siteDownRes)){

  $sheet2 = $objPHPExcel->createSheet(2);
  PHPExcel_Calculation::getInstance()->writeDebugLog = true;

  $sheet2->setTitle("Top15Reason");
  $sheet2->SetCellValue('A1', "Nº");
  $sheet2->SetCellValue('B1', "Site");
  $sheet2->SetCellValue('C1', "Número de Quedas");
  $styleArr = array(
                      'fill' => array(
                          'type' => PHPExcel_Style_Fill::FILL_SOLID,
                          'color' => array('rgb' => '422462')
                      ),
                      'font'  => array(
                          'bold'  => true,
                          'color' => array('rgb' => 'F17422'),
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

  $rowCount = 2; $i = 1; $xAxisTickValues = array(); $dataseriesLabels = array(); $dataSeriesValues = array();
  arsort($siteDown);

  $row = 2;  
  foreach ($siteDownRes as $rows) {
      $col = 0;
      $reasonData = array();
      $reasonArr = CMDBSource::QueryToArray("SELECT tk.reason_id,rs.reason,COUNT(tk.reason_id) as reasCount FROM ntticket tk LEFT JOIN ntreason rs ON rs.reason_id=tk.reason_id LEFT JOIN ntticketsites ts ON ts.ticket_id=tk.id WHERE ts.site_id=".$rows['site_id']." AND DATE(tk.start_date)>='".date('Y-m-d',strtotime('-3 months'))."' AND ts.is_active=1 GROUP BY tk.reason_id");

      if(!empty($reasonArr)){
          foreach ($reasonArr as $reason) {
              $reasonData[$reason['reason_id']] = $reason['reasCount'];
          }

      }

      $reasonList = CMDBSource::QueryToArray("SELECT rs.reason,rs.reason_id FROM ntreason rs");
      if(!empty($reasonList)){
        $ReasonCol = 3;
        foreach ($reasonList as $rwRs) {
            $sheet2->setCellValueByColumnAndRow($ReasonCol,1,$rwRs['reason']);
            $sheet2->setCellValueByColumnAndRow($ReasonCol,$row,(isset($reasonData[$rwRs['reason_id']])? $reasonData[$rwRs['reason_id']]:'-'));
            $ReasonCol++;
        }
      }


      $sheet2->setCellValueByColumnAndRow($ReasonCol,1,"Without Reason");
      $sheet2->setCellValueByColumnAndRow($ReasonCol,$row,(isset($reasonData[0])? $reasonData[0]:'-'));

      $sheet2->getStyleByColumnAndRow(0,1,$ReasonCol,1)->applyFromArray($styleArr);
      $sheet2->getRowDimension('1')->setRowHeight(-1);

      $sheet2->setCellValueByColumnAndRow($col,$row,$i);
      $sheet2->setCellValueByColumnAndRow($col+1,$row,$rows['site_name']);
      $sheet2->setCellValueByColumnAndRow($col+2,$row,$rows['total']);
      /*$sheet2->SetCellValue('A'.$row, $i);
      $sheet2->SetCellValue('B'.$row, $rows['site_name']);
      $sheet2->SetCellValue('C'.$row, $rows['total']);*/
      
      $row++;

      $i++;
  }

}


array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$D$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$E$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$F$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$G$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$H$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$I$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$J$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$K$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$L$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$M$1', NULL, 1));

array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$D$2:$D$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$E$2:$E$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$F$2:$F$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$G$2:$G$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$H$2:$H$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$I$2:$I$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$J$2:$J$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$K$2:$K$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$L$2:$L$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$M$2:$M$'.($row-1), NULL, 1));

array_push($xAxisTickValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top15Reason!$A$2:$B$'.($row-1), NULL, ($row-1)));

$series = new PHPExcel_Chart_DataSeries(
PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
range(0, count($dataSeriesValues)-1), // plotOrder
$dataseriesLabels,
$xAxisTickValues,
$dataSeriesValues
);
$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_VERTICAL);  // DIRECTION_BAR
$layout = new PHPExcel_Chart_Layout();
$layout->setShowVal(TRUE);
$layout->setShowPercent(TRUE);
$plotarea = new PHPExcel_Chart_PlotArea($layout, array($series));
$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
$title = new PHPExcel_Chart_Title('Quedas Nos Sites Por Energia e Transmissao '.date("d/M Y",strtotime("-3 months")).' à '.date("d/M Y"));
$yAxisLabel = new PHPExcel_Chart_Title('Motivo para ocorrências de inatividade do site');
$chart = new PHPExcel_Chart(
    'chart1',       // name
    $title,         // title
    $legend,        // legend
    $plotarea,      // plotArea
    true,           // plotVisibleOnly
    0,              // displayBlanksAs
    NULL,           // xAxisLabel
    $yAxisLabel     // yAxisLabel
);

$chart->setTopLeftPosition('A20');
/*$chart->setBottomRightPosition('BJ30');*/
$chart->setBottomRightPosition('P40');
$sheet2->addChart($chart);


/******************************************** Top 100 Site Down Along With Reason *************************************************/


$siteDownRes = CMDBSource::QueryToArray("SELECT st.site_name,st.site_id,count(ts.ticket_id) AS total FROM ntticketsites ts LEFT JOIN ntsites st ON st.site_id=ts.site_id LEFT JOIN ntticket tk ON tk.id=ts.ticket_id WHERE tk.finalclass='Incident' AND ts.is_active=1 AND DATE(tk.start_date)>='".date('Y-m-d',strtotime('-3 months'))."' GROUP BY ts.site_id ORDER BY total DESC LIMIT 100");

if(!empty($siteDownRes)){

  $sheet2 = $objPHPExcel->createSheet(2);
  PHPExcel_Calculation::getInstance()->writeDebugLog = true;

  $sheet2->setTitle("Top100Reason");
  $sheet2->SetCellValue('A1', "Nº");
  $sheet2->SetCellValue('B1', "Site");
  $sheet2->SetCellValue('C1', "Número de Quedas");
  $styleArr = array(
                      'fill' => array(
                          'type' => PHPExcel_Style_Fill::FILL_SOLID,
                          'color' => array('rgb' => '422462')
                      ),
                      'font'  => array(
                          'bold'  => true,
                          'color' => array('rgb' => 'F17422'),
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

  $rowCount = 2; $i = 1; $xAxisTickValues = array(); $dataseriesLabels = array(); $dataSeriesValues = array();
  arsort($siteDown);

  $row = 2;  
  foreach ($siteDownRes as $rows) {
      $col = 0;
      $reasonData = array();
      $reasonArr = CMDBSource::QueryToArray("SELECT tk.reason_id,rs.reason,COUNT(tk.reason_id) as reasCount FROM ntticket tk LEFT JOIN ntreason rs ON rs.reason_id=tk.reason_id LEFT JOIN ntticketsites ts ON ts.ticket_id=tk.id WHERE ts.site_id=".$rows['site_id']." AND DATE(tk.start_date)>='".date('Y-m-d',strtotime('-3 months'))."' AND ts.is_active=1 GROUP BY tk.reason_id");

      if(!empty($reasonArr)){
          foreach ($reasonArr as $reason) {
              $reasonData[$reason['reason_id']] = $reason['reasCount'];
          }

      }

      $reasonList = CMDBSource::QueryToArray("SELECT rs.reason,rs.reason_id FROM ntreason rs");
      if(!empty($reasonList)){
        $ReasonCol = 3;
        foreach ($reasonList as $rwRs) {
            $sheet2->setCellValueByColumnAndRow($ReasonCol,1,$rwRs['reason']);
            $sheet2->setCellValueByColumnAndRow($ReasonCol,$row,(isset($reasonData[$rwRs['reason_id']])? $reasonData[$rwRs['reason_id']]:'-'));
            $ReasonCol++;
        }
      }


      $sheet2->setCellValueByColumnAndRow($ReasonCol,1,"Without Reason");
      $sheet2->setCellValueByColumnAndRow($ReasonCol,$row,(isset($reasonData[0])? $reasonData[0]:'-'));

      $sheet2->getStyleByColumnAndRow(0,1,$ReasonCol,1)->applyFromArray($styleArr);
      $sheet2->getRowDimension('1')->setRowHeight(-1);

      $sheet2->setCellValueByColumnAndRow($col,$row,$i);
      $sheet2->setCellValueByColumnAndRow($col+1,$row,$rows['site_name']);
      $sheet2->setCellValueByColumnAndRow($col+2,$row,$rows['total']);
      /*$sheet2->SetCellValue('A'.$row, $i);
      $sheet2->SetCellValue('B'.$row, $rows['site_name']);
      $sheet2->SetCellValue('C'.$row, $rows['total']);*/
      
      $row++;

      $i++;
  }

}


array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$D$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$E$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$F$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$G$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$H$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$I$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$J$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$K$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$L$1', NULL, 1));
array_push($dataseriesLabels, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$M$1', NULL, 1));

array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$D$2:$D$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$E$2:$E$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$F$2:$F$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$G$2:$G$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$H$2:$H$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$I$2:$I$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$J$2:$J$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$K$2:$K$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$L$2:$L$'.($row-1), NULL, 1));
array_push($dataSeriesValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$M$2:$M$'.($row-1), NULL, 1));

array_push($xAxisTickValues, new PHPExcel_Chart_DataSeriesValues('String', 'Top100Reason!$A$2:$B$'.($row-1), NULL, ($row-1)));

$series = new PHPExcel_Chart_DataSeries(
PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
range(0, count($dataSeriesValues)-1), // plotOrder
$dataseriesLabels,
$xAxisTickValues,
$dataSeriesValues
);
$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_VERTICAL);  // DIRECTION_BAR
$layout = new PHPExcel_Chart_Layout();
$layout->setShowVal(TRUE);
$layout->setShowPercent(TRUE);
$plotarea = new PHPExcel_Chart_PlotArea($layout, array($series));
$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
$title = new PHPExcel_Chart_Title('Quedas Nos Sites Por Energia e Transmissao '.date("d/M Y",strtotime("-3 months")).' à '.date("d/M Y"));
$yAxisLabel = new PHPExcel_Chart_Title('Motivo para ocorrências de inatividade do site');
$chart = new PHPExcel_Chart(
    'chart1',       // name
    $title,         // title
    $legend,        // legend
    $plotarea,      // plotArea
    true,           // plotVisibleOnly
    0,              // displayBlanksAs
    NULL,           // xAxisLabel
    $yAxisLabel     // yAxisLabel
);

$chart->setTopLeftPosition('P3');
/*$chart->setBottomRightPosition('BJ30');*/
$chart->setBottomRightPosition('BK30');
$sheet2->addChart($chart);


$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$writer->setIncludeCharts(TRUE);

$writer->save('php://output');

?>