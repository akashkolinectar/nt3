<?php

require_once('../webservices/wbdb.php');
require_once('../webservices/PHPExcel/Classes/PHPExcel.php');

         //header('Content-Type: text/csv; charset=utf-8');  
         //header('Content-Disposition: attachment; filename=OpenIncidents_'.date('d-m-Y').'.csv');  
         //$output = fopen("php://output", "w"); 
         //fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); 
         //fputcsv($output, array('Número do bilhete', 'Título', 'Província', 'Site Principal','Dependente ','Razão ','2G','3G','4G','Duração'));  
        
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle("OpenIncidents");              
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Número do Ticket");
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', "Título");
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', "Província");
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', "Site Principal");
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', "Dependente");
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', "Motivo");
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', "Sub Reason");
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', "2G");
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', "3G");
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', "4G");
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', "Duração");

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
        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleArr);
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(-1);

         $openTickets = CMDBSource::QueryToArray("SELECT tk.id,tk.ref,tk.title,pr.province,tk.start_date,rs.reason,sbrs.sub_reason FROM ntticket tk LEFT JOIN ntticket_incident inc ON inc.id=tk.id LEFT JOIN ntreason rs ON rs.reason_id=tk.reason_id LEFT JOIN ntsubreason sbrs ON sbrs.sub_reason_id=tk.sub_reason_id LEFT JOIN ntsiteprovince pr ON pr.province_id = tk.province_id WHERE tk.operational_status = 'ongoing' AND tk.finalclass='Incident' ORDER BY tk.id DESC");
        $rowCount = 2;
        if(!empty($openTickets)){

            foreach ($openTickets as $aDBInfo) { 

            $date = new DateTime($aDBInfo['start_date']);
            $now = new DateTime();

            $ageData = $now->diff($date);

            if($ageData->format('%y')!=0){
                //$age = $ageData->format('%y Year %m Month %d Day %h Hr %i Min %s Sec');
                $age = $ageData->format('%a Dia %h Hr');
            }else if($ageData->format('%d')!=0){
                //$age = $ageData->format('%m Month %d Day %h Hr %i Min %s Sec');
                $age = $ageData->format('%a Dia %h Hr %i Min');
            }else if($ageData->format('%d')!=0){
                $age = $ageData->format('%a Dia %h Hr %i Min');
            }else if($ageData->format('%d')==0){
                if($ageData->format('%h')==0){
                    $age = $ageData->format('%i Min');
                }else{
                    $age = $ageData->format('%h Hr %i Min');
                }
            } 

            $allSites = array();
            $dependantSites = ""; $sitePricipal = "";
            
            $sites = CMDBSource::QueryToArray("SELECT st.site_name,st.site_id,st.parent_site FROM ntticketsites ts LEFT JOIN ntsites st ON st.site_id=ts.site_id WHERE ts.ticket_id=".$aDBInfo['id']." AND ts.is_active = 1");

            if(!empty($sites)){
                $allSites = array_column($sites, 'site_id');
                foreach ($sites as $rows) {                                  
                    if($rows['parent_site']!=0){
                        if(!in_array($rows['parent_site'], $allSites)){
                            $sitePricipal .= $rows['site_name']."\n";
                        }else{
                            $dependantSites .= $rows['site_name']."\n";
                        }
                    }else{
                        $sitePricipal .= $rows['site_name']."\n";
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


            $TWG = (in_array('2G', $availableNw))? "UP":"NA"; $TWGCLR = (in_array('2G', $availableNw))? "006400":"C0C0C0";
            $TRG = (in_array('3G', $availableNw))? "UP":"NA"; $TRGCLR = (in_array('3G', $availableNw))? "006400":"C0C0C0";
            $FRG = (in_array('4G', $availableNw))? "UP":"NA"; $FRGCLR = (in_array('4G', $availableNw))? "006400":"C0C0C0";
            $tech = CMDBSource::QueryToArray("SELECT * FROM ntticketnetworks WHERE ticket_id=".$aDBInfo['id']);
            if(!empty($tech)){
                foreach ($tech as $rows) {
                    switch (true) {
                        case ($rows['network']=='2G' && in_array('2G', $availableNw)): $TWG = "DOWN"; $TWGCLR = "FF0000"; break;
                        case ($rows['network']=='3G' && in_array('3G', $availableNw)): $TRG = "DOWN"; $TRGCLR = "FF0000"; break;
                        case ($rows['network']=='4G' && in_array('4G', $availableNw)): $FRG = "DOWN"; $FRGCLR = "FF0000"; break;
                    }
                }
            }

            $objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray(array( 'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => $TWGCLR) ), 'font'  => array('color' => array('rgb' => 'FFFFFF') ),
                                        'alignment' => array(
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                        ),
                                        'borders' => array(
                                            'allborders' => array(
                                              'style' => PHPExcel_Style_Border::BORDER_THIN,
                                              'color' => array('rgb' => 'FFFFFF')
                                            )
                                        ) ));
            $objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray(array( 'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => $TRGCLR) ), 'font'  => array('color' => array('rgb' => 'FFFFFF') ),
                                        'alignment' => array(
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                        ),
                                        'borders' => array(
                                            'allborders' => array(
                                              'style' => PHPExcel_Style_Border::BORDER_THIN,
                                              'color' => array('rgb' => 'FFFFFF')
                                            )
                                        ) ));
            $objPHPExcel->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray(array( 'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => $FRGCLR) ), 'font'  => array('color' => array('rgb' => 'FFFFFF') ),
                                        'alignment' => array(
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                        ),
                                        'borders' => array(
                                            'allborders' => array(
                                              'style' => PHPExcel_Style_Border::BORDER_THIN,
                                              'color' => array('rgb' => 'FFFFFF')
                                            )
                                        ) ));

            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $aDBInfo['ref']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $aDBInfo['title']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $aDBInfo['province']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $sitePricipal);
            $objPHPExcel->getActiveSheet()->getRowDimension($rowCount)->setRowHeight(-1);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $dependantSites);
            $objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $aDBInfo['reason']);
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $aDBInfo['sub_reason']);
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $TWG);
            $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $TRG);
            $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $FRG);
            $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $age);
            $rowCount++;
            //$incArr = array($aDBInfo['ref'],$aDBInfo['title'],$aDBInfo['province'],$sitePricipal,$dependantSites,$aDBInfo['reason'],$TWG,$TRG,$FRG,$age);
            //fputcsv($output, $incArr);
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
    
    $objPHPExcel->getActiveSheet()->freezePane('B2');

    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="OpenIncidentReport.xls"');
    $objWriter->save('php://output');

  //fclose($output);
?>