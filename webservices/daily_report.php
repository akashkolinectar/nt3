<?php

//include('../webservices/wbdb.php');
include('/home/nt3/webservices/wbdb.php');
//require_once('PHPExcel/Classes/PHPExcel.php');
require_once('/home/nt3/webservices/PHPExcel/Classes/PHPExcel.php');

$query = "SELECT `time` FROM ntreport_conf_time WHERE is_active=1";
$result = mysqli_query($conf,$query);

if($result){
	
	if($result->num_rows>0){
		
		while($rows = mysqli_fetch_array($result,MYSQLI_ASSOC)){
			
			if(date('H:i',strtotime('+5 minutes')) == date('H:i',strtotime($rows['time']))){

				$tquery = "SELECT created_date FROM ntreport_history WHERE 1 ORDER BY id DESC LIMIT 1";
				$tresult = $conf->query($tquery);
				if($tresult->num_rows>0){
					$history = mysqli_fetch_all($tresult,MYSQLI_ASSOC);
					$fromDate = date('Y-m-d H:i:s',strtotime($history[0]['created_date']));
				}else{
					$fromDate = date('Y-m-d H:i:s',strtotime('-1 days'));
				}

				/** No service and priority for change management **/
				$query1 = "SELECT tk.id,tk.finalclass,tk.ref,tk.title,tk.description,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'') as caller,IF(inc.service_id!='',inc.service_id,prob.service_id) as service_id,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'') as agent,tk.start_date,prov.province,IF(inc.urgency!='',inc.urgency,prob.urgency) as priority,rsn.reason,IF(inc.ttr_100_deadline!='',inc.ttr_100_deadline,'') as ttr,tk.operational_status,tk.close_date FROM ntticket tk LEFT JOIN ntticket_incident inc ON (inc.id = tk.id AND tk.finalclass='Incident') LEFT JOIN ntticket_problem prob ON (prob.id = tk.id AND tk.finalclass='Problem') LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') LEFT JOIN ntperson callerper ON callerper.id=callercnt.id LEFT JOIN ntorganization org ON org.id=tk.org_id LEFT JOIN ntsiteprovince prov ON prov.province_id=tk.province_id LEFT JOIN ntreason rsn ON rsn.reason_id=tk.reason_id WHERE tk.start_date BETWEEN '".$fromDate."' AND '".date('Y-m-d H:i:s')."' AND tk.operational_status='ongoing'";

				//echo $query1;
				$result1 = mysqli_query($conf,$query1);
				$reportHtml = "";

				if($result1){

					if($result1->num_rows>0){

						$reportHtml .= "<table border='1' cellspacing='0' cellpadding='0' style='background:white;border-collapse:collapse'><thead>";

						$objPHPExcel = new PHPExcel();
						$objPHPExcel->setActiveSheetIndex(0);
						$objPHPExcel->getActiveSheet()->setTitle("NT3Report");				
						$objPHPExcel->getActiveSheet()->SetCellValue('A1', "Tarefa");
						$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Título");
						$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Servicos Afectados');
						$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Tecnologias Afectados');
						$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Província');
						$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Site Afectados');
						$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Motivo');
						$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Duração');
						$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Tempo acima do SLA');
						$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Técnico do NOC');
						$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Técnico Responsável');
						$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Data de Criação');
						$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true);

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
						$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($styleArr);
						$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(-1);

						$reportHtml .= "<tr style='background-color:#422462;text-align:center;'>
											<td width='60' style='font-family: Calibri;font: normal Calibri, sans-serif;color:#f17422;font-weight:bold;'>Tarefa</td>
											<td width='100' style='font-family: Calibri;font: normal Calibri, sans-serif;color:#f17422;font-weight:bold;'>Título</td>
											<td width='100' style='font-family: Calibri;font: normal Calibri, sans-serif;color:#f17422;font-weight:bold;'>Servicos Afectados</td>
											<td width='100' style='font-family: Calibri;font: normal Calibri, sans-serif;color:#f17422;font-weight:bold;'>Tecnologias Afectados</td>
											<td width='100' style='font-family: Calibri;font: normal Calibri, sans-serif;color:#f17422;font-weight:bold;'>Província</td>
											<td width='100' style='font-family: Calibri;font: normal Calibri, sans-serif;color:#f17422;font-weight:bold;'>Sites Afectados</td>
											<td width='100' style='font-family: Calibri;font: normal Calibri, sans-serif;color:#f17422;font-weight:bold;'>Motivo</td>
											<td width='100' style='font-family: Calibri;font: normal Calibri, sans-serif;color:#f17422;font-weight:bold;'>Duração</td>
											<td width='100' style='font-family: Calibri;font: normal Calibri, sans-serif;color:#f17422;font-weight:bold;'>Tempo acima do SLA</td>
											<td width='100' style='font-family: Calibri;font: normal Calibri, sans-serif;color:#f17422;font-weight:bold;'>Técnico do NOC</td>
											<td width='100' style='font-family: Calibri;font: normal Calibri, sans-serif;color:#f17422;font-weight:bold;'>Técnico Responsável</td>
											<td width='100' style='font-family: Calibri;font: normal Calibri, sans-serif;color:#f17422;font-weight:bold;'>Data de Criação</td>
										</tr></thead><tbody>";

						$rowCount = 2;
						while ($rows = mysqli_fetch_array($result1,MYSQLI_ASSOC)) {
							
							$reportHtml .= "<tr>";
							$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $rows['ref']);

							$styleIdArr = array(
									        'borders' => array(
											    'allborders' => array(
											      'style' => PHPExcel_Style_Border::BORDER_THIN,
											      'color' => array('rgb' => '000000')
											    )
										  	)
								    	);
							$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleIdArr);

							$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$rowCount)->getHyperlink()->setUrl('https://nt3.nectarinfotel.com/pages/UI.php?operation=details&class='.$rows['finalclass'].'&id='.$rows['id'].'&c[menu]=New'.$rows['finalclass']);

							$service = ""; $HtmlService = "";
							// Affected Service
							$query2 = "SELECT SA.service_aftd FROM ntticketserviceaffected TS LEFT JOIN ntserviceaftd SA ON SA.service_aftd_id=TS.service_aftd_id WHERE TS.ticket_id = ".$rows['id']." AND TS.is_active = 1";
							$result2 = mysqli_query($conf,$query2);
							if($result2){
								$numResultsSA = mysqli_num_rows($result2);
								$counterSA = 0;
								if($numResultsSA>0){
									while($rows2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)){
										if(++$counterSA == $numResultsSA) {
									        $service .= utf8_encode($rows2['service_aftd']);
									        $HtmlService .= utf8_encode($rows2['service_aftd']);
									    }else {
									        $service .= utf8_encode($rows2['service_aftd'])."\n";
									        $HtmlService .= utf8_encode($rows2['service_aftd'])." , ";
									    }
									}
									if($numResultsSA>1){
										$objPHPExcel->getActiveSheet()->getRowDimension($rowCount)->setRowHeight(40);
									}
								}
							}

							// Networks
							$technology = "";
							$query3 = "SELECT * FROM ntticketnetworks WHERE ticket_id = ".$rows['id'];
							$result3 = mysqli_query($conf,$query3);
							if($result3){
								$numResults = mysqli_num_rows($result3);
								$counter = 0;
								if($numResults>0){
									while($row = mysqli_fetch_array($result3,MYSQLI_ASSOC)) {
									    if(++$counter == $numResults) {
									        $technology .= utf8_encode($row['network']);
									    }else {
									        $technology .= utf8_encode($row['network']).",";
									    }
									}
								}
								
							}

							// Affected Sites
							$aSites = ''; $aHtmlSites = '';
							$query4 = "SELECT SI.site_name FROM ntticketsites TS LEFT JOIN ntsites SI ON SI.site_id=TS.site_id WHERE TS.ticket_id = ".$rows['id']." AND TS.is_active = 1";
							$result4 = mysqli_query($conf,$query4);
							if($result4){
								$numResultsAS = mysqli_num_rows($result4);
								$counterAS = 0;
								if($numResultsAS>0){
									while($rows4 = mysqli_fetch_array($result4, MYSQLI_ASSOC)){
										if(++$counterAS == $numResultsAS) {
									        $aSites .= utf8_encode($rows4['site_name']);
									        $aHtmlSites .= utf8_encode($rows4['site_name']);
									    }else {
									        $aSites .= utf8_encode($rows4['site_name'])."\n";
									        $aHtmlSites .= utf8_encode($rows4['site_name'])." *** ";
									    }
									}
									if($numResultsAS>1){
										$objPHPExcel->getActiveSheet()->getRowDimension($rowCount)->setRowHeight(40);
									}
								}
							}

							$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, utf8_encode($rows['title']));
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $service);
							$objPHPExcel->getActiveSheet()->getStyle('C'.$rowCount)->getAlignment()->setWrapText(true);

							$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $technology);
					    	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, utf8_encode($rows['province']));
					    	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $aSites);
					    	$objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->getAlignment()->setWrapText(true);

					    	$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, utf8_encode($rows['caller']));
					    	$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, utf8_encode($rows['agent']));

							/*$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, utf8_encode($rows['title']));
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $service);
							$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $technology);
					    	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $aSites);
					    	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, utf8_encode($rows['province']));
					    	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, utf8_encode($rows['caller']));
					    	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, utf8_encode($rows['agent']));*/
					    	

					    	switch ($rows['priority']) {
					    		case 1: $color = 'FF0000';break;
					    		case 2: $color = 'E7782A';break;
					    		case 3: $color = 'E6E92F';break;
					    		case 4: $color = '77CC29';break;
					    		default: $color = 'FFFFFF'; break;
					    	}
					    	$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray(
							    array(
							        'fill' => array(
							            'type' => PHPExcel_Style_Fill::FILL_SOLID,
							            'color' => array('rgb' => $color)
							        )
							    )
							);

					    	$now = date('Y-m-d H:i:s');
					    	$date = new DateTime($rows['start_date']);
							$now = new DateTime();
							$age = $now->diff($date);
							$ageData = $age->format('%d Day %h Hr %i Min %s Sec');
							$ageData = strpos($ageData, '0 Day')!==FALSE? (strpos($age->format('%h Hr %i Min'), '0 Hr')!==FALSE? $age->format('%i Min'):$age->format('%h Hr %i Min')):$age->format('%d Day %h Hr %i Min');

					       // echo $ageData."<br>";
					        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $ageData);
					    	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, date('d-m-Y h:i a',strtotime($rows['start_date'])));
					    	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, utf8_encode($rows['reason']));

					    	/*if($rows['ttr']!=NULL && $rows['ttr']!=''){
				    			$now = new DateTime($rows['ttr']);
						    	$date = new DateTime($rows['start_date']);
								$age = $now->diff($date);
								$ttrData = $age->format('%d Day %h Hr %i Min %s Sec');
								$ttrData = strpos($ttrData, '0 Day')!==FALSE? (strpos($age->format('%h Hr %i Min'), '0 Hr')!==FALSE? $age->format('%i Min'):$age->format('%h Hr %i Min')):$age->format('%d Day %h Hr %i Min');
					    	}else{
					    		$ttrData = "";
					    	}*/

					    	$slaStatus = "";

					    	$prio = $rows['priority'];
					    	if($rows['priority']==2){
					    		$prio = 1;
					    	}

			    			$query5 = "SELECT SL.value,SL.unit FROM ntslt SL WHERE SL.priority = ".$prio." AND metric = 'ttr'";
			    			$result5 = mysqli_query($conf,$query5);
							$slaArr = mysqli_fetch_all($result5, MYSQLI_ASSOC);
							$sla = "+".$slaArr[0]['value']." ".$slaArr[0]['unit'];
							$SLADate = date('Y-m-d H:i',strtotime($rows['start_date'].$sla));

							if(date('Y-m-d H:i') > $SLADate && $rows['operational_status']!='closed'){
								$now = new DateTime(date('Y-m-d H:i'));
						    	$date = new DateTime($SLADate);
								$age = $now->diff($date);
								$ttrData = $age->format('%d Day %h Hr %i Min %s Sec');
								$ttrData = strpos($ttrData, '0 Day')!==FALSE? (strpos($age->format('%h Hr %i Min'), '0 Hr')!==FALSE? $age->format('%i Min'):$age->format('%h Hr %i Min')):$age->format('%d Day %h Hr %i Min');
								$slaStatus = "SLA Missed By ".$ttrData;
							}else if(date('Y-m-d H:i') <= $SLADate && $rows['operational_status']!='closed'){
								$now = new DateTime($SLADate);
						    	$date = new DateTime(date('Y-m-d H:i'));
								$age = $now->diff($date);
								$ttrData = $age->format('%d Day %h Hr %i Min %s Sec');
								$ttrData = strpos($ttrData, '0 Day')!==FALSE? (strpos($age->format('%h Hr %i Min'), '0 Hr')!==FALSE? $age->format('%i Min'):$age->format('%h Hr %i Min')):$age->format('%d Day %h Hr %i Min');
								$slaStatus = $ttrData." Time Remaining";
							}else if($rows['operational_status']=='closed' && $SLADate>=date("Y-m-d H:i",strtotime($rows['close_date']))){

								$now = new DateTime($SLADate);
						    	$date = new DateTime($rows['close_date']);
								$age = $now->diff($date);
								$ttrData = $age->format('%d Day %h Hr %i Min %s Sec');
								$ttrData = strpos($ttrData, '0 Day')!==FALSE? (strpos($age->format('%h Hr %i Min'), '0 Hr')!==FALSE? $age->format('%i Min'):$age->format('%h Hr %i Min')):$age->format('%d Day %h Hr %i Min');

								$slaStatus = "Ticket Closed Within SLA. Remaining Time: ".$ttrData ;
							}else if($rows['operational_status']=='closed' && $SLADate<date("Y-m-d H:i",strtotime($rows['close_date']))){

								$now = new DateTime($rows['close_date']);
						    	$date = new DateTime($SLADate);
								$age = $now->diff($date);
								$ttrData = $age->format('%d Day %h Hr %i Min %s Sec');
								$ttrData = strpos($ttrData, '0 Day')!==FALSE? (strpos($age->format('%h Hr %i Min'), '0 Hr')!==FALSE? $age->format('%i Min'):$age->format('%h Hr %i Min')):$age->format('%d Day %h Hr %i Min');

								$slaStatus = "Ticket Closed After SLA End. Extended Time: ".$ttrData ;
							}

							echo "Dur : ".$sla."**** Start Date: ".date('Y-m-d H:i',strtotime($rows['start_date']))." **** SLA Date : ".date('Y-m-d H:i',strtotime($rows['start_date'].$sla))." **** Status: ".$slaStatus." **** Close Date:  ".date("Y-m-d H:i",strtotime($rows['close_date']))."<br>";
					    	
					    	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $slaStatus);
					    	/*$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $ageData);
					    	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, date('d-m-Y h:i a',strtotime($rows['start_date'])));
					    	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, utf8_encode($rows['reason']));
					    	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $rows['ttr']);*/

					    	$url = 'https://nt3.nectarinfotel.com/pages/UI.php?operation=details&class='.$rows['finalclass'].'&id='.$rows['id'].'&c[menu]=New'.$rows['finalclass'];
							

							$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;background-color:#".$color."'><a href='".$url."' target='_change'>".$rows['ref']."</a></td>";
							$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".utf8_encode($rows['title'])."</td>";
					    	//$reportHtml .= "<td>".$service."</td>";
					    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".$HtmlService."</td>";
					    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".$technology."</td>";
					    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".utf8_encode($rows['province'])."</td>";
					    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".$aHtmlSites."</td>";
					    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".utf8_encode($rows['reason'])."</td>";
					    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".$ageData."</td>";
					    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".$slaStatus."</td>";
					    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".utf8_encode($rows['caller'])."</td>";
					    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".utf8_encode($rows['agent'])."</td>";
					    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".date('d-m-Y h:i a',strtotime($rows['start_date']))."</td>";
					    	$reportHtml .= "</tr>";

					    	$rowCount++;

						} // EOF While Loop

						$reportHtml .= "</tbody></table>";
						$reportHtml .= "<br/><div><label style='float:left;'>Legends: </label>&nbsp;&nbsp;&nbsp;
							<span style='float:left;text-align:center;background-color:#FF0000;width:100px;height:25px;'>Critical</span>&nbsp;&nbsp;&nbsp;
							<span style='float:left;text-align:center;background-color:#E7782A;width:100px;height:25px;'>High</span>&nbsp;&nbsp;&nbsp;
							<span style='float:left;text-align:center;background-color:#E6E92F;width:100px;height:25px;'>Medium</span>&nbsp;&nbsp;&nbsp;
							<span style='float:left;text-align:center;background-color:#77CC29;width:100px;height:25px;'>Low</span>&nbsp;&nbsp;&nbsp; </div><br/>";

						$rowCount++;
						//$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Movicel. Direcção de Operações - Criado: ".date('js F Y h:i a'));
						//$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount)->getFont()->setBold(true);
						//$rowCount++;
						$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Legenda:");
						$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, "Critical");
						$objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray(
							    array(
							        'fill' => array(
							            'type' => PHPExcel_Style_Fill::FILL_SOLID,
							            'color' => array('rgb' => 'FF0000')
							        )
							    )
							);

						$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, "High");
						$objPHPExcel->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray(
							    array(
							        'fill' => array(
							            'type' => PHPExcel_Style_Fill::FILL_SOLID,
							            'color' => array('rgb' => 'E7782A')
							        )
							    )
							);

						$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "Medium");
						$objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray(
							    array(
							        'fill' => array(
							            'type' => PHPExcel_Style_Fill::FILL_SOLID,
							            'color' => array('rgb' => 'E6E92F')
							        )
							    )
							);

						$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, "Low");
						$objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray(
							    array(
							        'fill' => array(
							            'type' => PHPExcel_Style_Fill::FILL_SOLID,
							            'color' => array('rgb' => '77CC29')
							        )
							    )
							);
						/*$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->getStyle('C1:C'.$objPHPExcel->getActiveSheet()->getHighestRow())
					    ->getAlignment()->setWrapText(true); */
						$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
						//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
						$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
						//$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
						$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
						$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
						//$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
						$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
						$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
						//$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
						//$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
						$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);

						$objPHPExcel->getActiveSheet()->freezePane('B2');

						/*$style = array(
					        'alignment' => array(
					            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					        )
					    );
						$objPHPExcel->getActiveSheet()->getDefaultStyle()->applyFromArray($style);
						$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);*/

						/*ob_end_clean();
						header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
						header('Content-Disposition: attachment;filename="Report-'.date("mdY").'.xlsx"');
						header('Cache-Control: max-age=0');
						$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
						$objWriter->save('some_excel_file.xlsx');*/
						$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
						//$objWriter->save('nt3DailyReport.xlsx');
						$objWriter->save('nt3DailyReport.xlsx');
						$isExcel = TRUE;
					} // EOF If Ticket Is Created Or Not
					else{
						$isExcel = FALSE;
					}
				}else{
					echo mysqli_error($conf);
				}

				/************************* Mail Part ***************************/

				$query2 = "SELECT CNT.email as mailid, CONCAT(PER.first_name,' ',CNT.name) as receivername FROM ntreport_conf_contact RC LEFT JOIN ntpriv_user USR ON USR.contactid=RC.contact_id LEFT JOIN ntperson PER ON PER.id=RC.contact_id LEFT JOIN ntcontact CNT ON CNT.id=RC.contact_id WHERE RC.is_active=1";
				$result2 = $conf->query($query2);

				if($result2){

					if($result2->num_rows>0){

						while($rows = mysqli_fetch_array($result2,MYSQLI_ASSOC)){

							$mailMsg = ""; $status = 0;
							if($rows['mailid']==''){
								$mailMsg = "User do not have mail id";
								$status = 2;
							}else{

								$to  = $rows['mailid'];
								$subject = 'NT3 - Relatorio diario de incidentes ['.date('jS F Y').'] ['.date('h:i a').']';
								//$subject = 'NT3 - Relatório diário de incidentes ['.date('jS F Y').'] ['.date('h:i a').']';
								/*$headers = 'From: rashmi.chaudhari@nectarinfotel.com' . "\r\n" .
								    'Reply-To: rashmi.chaudhari@nectarinfotel.com' . "\r\n" .
								    'X-Mailer: PHP/' . phpversion();*/

								$headers = 'From: nt3system@movicel.co.ao' . "\r\n" .
								    'Reply-To: nt3system@movicel.co.ao' . "\r\n" .
								    'X-Mailer: PHP/' . phpversion();

							    /*$headers = 'From: nilesh.vishwakarma@movicel.co.ao' . "\r\n" .
								    'Reply-To: nilesh.vishwakarma@movicel.co.ao' . "\r\n" .
								    'X-Mailer: PHP/' . phpversion();*/

								$headers .= "MIME-Version: 1.0\r\n"
								  ."Content-Type: multipart/mixed; boundary=\"1a2a3a\"";
								 
								$message .= "If you can see this MIME than your client doesn't accept MIME types!\r\n"
								  ."--1a2a3a\r\n";
								 
								if($isExcel){
									
									/*$message .= "Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
										  ."Content-Transfer-Encoding: 7bit\r\n\r\n"
										  ."Hello, <br/>NT3 Report is generated for ".date('jS F Y')." at ".date('h:i a').". <br/><br/>".$reportHtml
										  ."<p>Please find attached copy of NT3 Report</span></p> <br/><p><b>Thank You!</b></p> \r\n"
										  ."--1a2a3a\r\n";*/

									$message .= "Content-Type: text/html; charset=UTF-8"
											  ."Content-Transfer-Encoding: base64\r\n\r\n"
											  ."Hello, <br/>NT3 Report is generated for ".date('jS F Y')." at ".date('h:i a').". <br/><br/>".$reportHtml
											  ."<p>Please find attached copy of NT3 Report</span></p> <br/><p><b>Thank You!</b></p> \r\n"
											  ."--1a2a3a\r\n";

									$file = file_get_contents("nt3DailyReport.xlsx");
									
									$message .= "Content-Type: image/jpg; name=\"nt3DailyReport.xlsx\"\r\n"
										  ."Content-Transfer-Encoding: base64\r\n"
										  ."Content-disposition: attachment; file=\"nt3DailyReport.xlsx\"\r\n"
										  ."\r\n"
										  .chunk_split(base64_encode($file))
										  ."--1a2a3a--";
								}else{
									$message .= "Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
										  ."Content-Transfer-Encoding: 7bit\r\n\r\n"
										  ."Hello, <br/> Tickets are not generated from ".date('jS F Y h:i a',strtotime($fromDate))." to ".date('jS F Y h:i a')."."
										  ."<br/><p><b>Thank You!</b></p> \r\n"
										  ."--1a2a3a\r\n";
								}

								$success = mail($to, $subject, $message, $headers);
								if (!$success) {
									echo "Mail to " . $to . " failed .";
									$mailMsg = "Unable to send mail";
									$status = 2;
								}else {
									echo "Success : Mail was send to " . $to . " **** Time : ".date("h:i a");
									$mailMsg = "Mail Sent";
									$status = 1;
								}

							} // EOF Else mail id is available

							$query3 = "INSERT INTO ntreport_history (name,email,status,reason,created_date) VALUES ('".$rows['receivername']."','".$rows['mailid']."',".$status.",'".$mailMsg."','".date('Y-m-d H:i:s')."')";
							$result3 = $conf->query($query3);
							if($result3){
								echo "History Created";
							}else{
								echo "History Failed";
							}

						} // EOF While Loop Contact

					} // EOF Check Has Records In Contact Or Not
					else{
						echo "Contact Empty";
					}
				}// EOF Check Query Executed For Contact
				else{
					echo "Query Failed Contact";
				}

			} // EOF Check Has Records In Time Or Not
			else{
				echo "Wait ForTime : ".date('H:i a',strtotime($rows['time']))." Now : ".date('H:i a',strtotime('+5 minutes'))."<br/> ";
			}
		} // EOF While Loop Time

	}// EOF Check Time Empty Or Not
	else{
		echo "Time Empty";
	}
} // EOF Check Query Executed For Time
else{
	echo "Time Query Failed bored \n";
	print_r($conf);
	echo  $conf->error;
}

?>