<?php 

require_once('../webservices/wbdb.php');
require_once('../webservices/PHPExcel/Classes/PHPExcel.php');
$objPHPExcel = new PHPExcel();
$providerQuery = "SELECT ctr.name,ctr.id as provider_id,pc.sla_id FROM ntcontract ctr LEFT JOIN ntprovidercontract pc ON pc.id=ctr.id WHERE ctr.finalclass='ProviderContract'";

$providerRes = mysqli_query($conf,$providerQuery);
$reportHtml = "";

if($providerRes){
	
	if($providerRes->num_rows>0){	
		
		while ($providerRows = mysqli_fetch_array($providerRes,MYSQLI_ASSOC)) {

				$totalSla = array(); $beyondSla = array(); $beyondSlaPer = array(); // For final col data
				$daySum=0; $hourSum=0; $minSum=0; 


				$query2 = "SELECT st.site_name,st.site_id FROM ntsites st LEFT JOIN ntprovidersites ps ON ps.site_id = st.site_id RIGHT JOIN ntticketsites ts ON ts.site_id=st.site_id WHERE ps.provider_id='".$providerRows['provider_id']."' AND ps.is_active = 1 AND ts.is_active=1 GROUP BY ts.site_id";
				$result2 = mysqli_query($conf,$query2);
				
				if($result2){

					if($result2->num_rows>0){

						$i = 1; $col = 0; $row_count = 1; $site_row = 3; $maxCol = 0;
						

						$slaTotal = ''; // For final col data
						$totalSum = ''; $totalExtend = ''; $totalExtendPer = '';
						$dayExt=array(); $hourExt=array(); $minExt=array();
						$extendPerTotalSatge=0; $extendPerTotal=array();

						$objWorkSheet = $objPHPExcel->createSheet($i); //Setting index when creating

						while ($siteRows = mysqli_fetch_array($result2,MYSQLI_ASSOC)) {
							
							$avaria = 1; $avariaCol = 1;
							$style = array(
						        'alignment' => array(
						            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
						        ),
						        'font' => array(
							        'bold' => true
							    ),
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
						        'borders' => array(
								    'allborders' => array(
								      'style' => PHPExcel_Style_Border::BORDER_THIN,
								      'color' => array('rgb' => 'FFFFFF')
								    )
							  	)
						    );

						    $styleTotal = array(
									'fill' => array(
						            'type' => PHPExcel_Style_Fill::FILL_SOLID,
						            'color' => array('rgb' => 'FFFF66')
						        ),
						        'font'  => array(
							        'bold'  => true,
							        'color' => array('rgb' => 'C00000'),
							        /*'size'  => 15,
							        'name'  => 'Verdana'*/
					   			),
					   			'borders' => array(
								    'allborders' => array(
								      'style' => PHPExcel_Style_Border::BORDER_THIN,
								      'color' => array('rgb' => 'FFFFFF')
								    )
							  	)
							);

							$styleTotalFinal = array(
									'fill' => array(
						            'type' => PHPExcel_Style_Fill::FILL_SOLID,
						            'color' => array('rgb' => '8B0000')
						        ),
						        'font'  => array(
							        'bold'  => true,
							        'color' => array('rgb' => 'FFFFFF'),
							        /*'size'  => 15,
							        'name'  => 'Verdana'*/
					   			),
					   			'borders' => array(
								    'allborders' => array(
								      'style' => PHPExcel_Style_Border::BORDER_THIN,
								      'color' => array('rgb' => 'FFFFFF')
								    )
							  	)
							);

							$objWorkSheet->setCellValue('A1', 'Site Name')->getStyle('A1')->applyFromArray($style);
					        $dateFilter = '';
					        $fromDTWeek = date('Y-m-d',strtotime('-1 week'));
					        $toDTWeek = date('Y-m-d');
					        $dateFilter = " AND DATE(tk.start_date) BETWEEN '".$fromDTWeek."' AND '".$toDTWeek."' ";
					        
					       $ticketQuery = "SELECT ts.ticket_id,tk.start_date,tk.close_date,st.parent_site,st.site_id FROM ntticketsites ts LEFT JOIN ntticket tk ON tk.id = ts.ticket_id LEFT JOIN ntsites st ON st.site_id = ts.site_id LEFT JOIN ntticket_incident inc ON inc.id=tk.id WHERE ts.site_id = '".$siteRows['site_id']."' AND inc.priority=1 AND tk.operational_status='closed' $dateFilter GROUP BY ts.ticket_id";

					        //echo $ticketQuery."<br/>";

							$ticketResult = mysqli_query($conf,$ticketQuery);

							if($ticketResult){

								if($ticketResult->num_rows>0){

									$dayExternal = 0; $hourExternal = 0; $minExternal = 0; $slaExtend = ''; $slaExtendPer = ''; $slaExtendArr='';
									while ($ticketRows = mysqli_fetch_array($ticketResult,MYSQLI_ASSOC)) {

										$objWorkSheet->setCellValue('A'.$site_row, $siteRows['site_name'])->getColumnDimension('A')->setAutoSize(true);
										$day = 0; $hour = 0; $min = 0;
										$mergeNextCol = $avariaCol+4;

										/************* Set Headers **************/

										$objWorkSheet->getStyleByColumnAndRow($avariaCol,1)->applyFromArray($style);
										$objWorkSheet->getStyleByColumnAndRow($avariaCol,2)->applyFromArray($style);
										$objWorkSheet->getStyleByColumnAndRow($avariaCol+1,2)->applyFromArray($style);
										$objWorkSheet->getStyleByColumnAndRow($avariaCol+2,2)->applyFromArray($style);
										$objWorkSheet->getStyleByColumnAndRow($avariaCol+3,2)->applyFromArray($style);
										$objWorkSheet->getStyleByColumnAndRow($avariaCol+4,2)->applyFromArray($style);

										$objWorkSheet->setCellValueByColumnAndRow($avariaCol,1,$avaria.' Avaria')->mergeCellsByColumnAndRow($avariaCol,1,$mergeNextCol,1)->getStyleByColumnAndRow($avariaCol,1)->getFont()->setBold(true);

										$objWorkSheet->setCellValueByColumnAndRow($avariaCol,2, 'Início da Avaria')->getColumnDimensionByColumn($avariaCol,2)->setAutoSize(true);

										$objWorkSheet->setCellValueByColumnAndRow($avariaCol+1,2, 'Notificação')->getColumnDimensionByColumn($avariaCol+1,2)->setAutoSize(true);
										
										$objWorkSheet->setCellValueByColumnAndRow($avariaCol+2,2, 'Fim da Notificação')->getColumnDimensionByColumn($avariaCol+2,2)->setAutoSize(true);

										$objWorkSheet->setCellValueByColumnAndRow($avariaCol+3,2, 'Fim da Avaria')->getColumnDimensionByColumn($avariaCol+3,2)->setAutoSize(true);
										
										$objWorkSheet->setCellValueByColumnAndRow($avariaCol+4,2, 'Duração da Avaria ( Dia hh:mm:ss)')->getColumnDimensionByColumn($avariaCol+4,2)->setAutoSize(true);
										$objWorkSheet->getStyleByColumnAndRow($avariaCol+4,2)->getFont()->setBold(true);

										/************* Set Values **************/

										$parentCharged = FALSE;
										$parentSiteQuery = "SELECT * FROM ntticketsites WHERE site_id = ".$ticketRows['parent_site']." AND ticket_id = ".$ticketRows['ticket_id']." AND is_active = 1";
										$parentSiteRes = mysqli_query($conf,$parentSiteQuery);
										if($parentSiteRes){
											if($parentSiteRes->num_rows>0){
												$parentCharged = TRUE;
											}
										}

										
										if(!$parentCharged){
											$objWorkSheet->setCellValueByColumnAndRow($avariaCol,$site_row, $ticketRows['start_date']);
											$objWorkSheet->setCellValueByColumnAndRow($avariaCol+3,$site_row, $ticketRows['close_date']);

											if($ticketRows['close_date']!=''){
												$closeDate = new DateTime(date('Y-m-d H:i',strtotime($ticketRows['close_date'])));
										    	$startDate = new DateTime(date('Y-m-d H:i',strtotime($ticketRows['start_date'])));
												$age = $closeDate->diff($startDate);
												
												$day = (int)$age->format('%d');
												$hour = (int)$age->format('%h');
												$min = (int)$age->format('%i');												

												$slaData = $age->format('%d Day %h Hr %i Min %s Sec');

												$slaData = $day!=0? "$day Day $hour Hr $min Min":($hour!=0? "$hour Hr $min Min":($min!=0? "$min Min":""));

												$objWorkSheet->setCellValueByColumnAndRow($avariaCol+4,$site_row, $slaData);

												if($slaData!=''){
													$objWorkSheet->getStyleByColumnAndRow($avariaCol+4,$site_row)->applyFromArray($styleTotal);
												}
												$dayExternal = $dayExternal+$day;
												$hourExternal = $hourExternal+$hour;
												$minExternal = $minExternal+$min;

												if($minExternal>=60){
													while($minExternal>=60){
														$minExternal = $minExternal-60;
														$hourExternal = $hourExternal+1;
													}
												}
												if($hourExternal>=24){
													while($hourExternal>=24){
														$hourExternal = $hourExternal-24;
														$dayExternal = $dayExternal+1;
													}
												}

												$fromDt = ''; $toDate = ''; $selectedDays = 1; 
												$dateDiff = '02:06';
										        	
												$toDate = strtotime($toDTWeek);
												$fromDt = strtotime($fromDTWeek);
												$selectedDates = $toDate - $fromDt;
												$selectedDays = round($selectedDates / (60 * 60 * 24))+1;

												$cal1 = ($selectedDays*24*98.5)/100;
												$cal2 = $selectedDays*24;
												$finalCal = round( ($cal2 - $cal1) ,1);
												$dateDiff = str_replace('.', ':', $finalCal);
										        
												if((date("H:i",strtotime($hourExternal.":".$minExternal))>date("H:i",strtotime($dateDiff))) || $dayExternal>0){

													$diffArr = explode(':', $dateDiff);
													$diffhr = $diffArr[0]; $difmin = $diffArr[1]; 

													$tempDt1 = date("H:i",strtotime($hourExternal.":".$minExternal." -$diffhr hours"));
													$tempDt2 = date("H:i",strtotime($tempDt1." -$difmin minute"));
													$tempDt3 = explode(':', $tempDt2);
													$tempHour = $tempDt3[0];
													$tempMin = $tempDt3[1];

													$compHr = 24-$diffhr;
													if($tempHour>=$compHr){ 
														$tempDay = $dayExternal-1;
													}else{ 
														$tempDay = $dayExternal;
													}
													$slaExtend = $tempDay." Day ".$tempHour." Hr ".$tempMin." Min";
													$slaExtendArr = $tempDay.":".$tempHour.":".$tempMin;
													$dyasToHr = (float)(($dayExternal*24)+$hourExternal.".".$minExternal);
													$slaExtendPer = $dyasToHr/$selectedDays*24;

												}
												$slaTotal = $dayExternal!=0? "$dayExternal Day $hourExternal Hr $minExternal Min":($hourExternal!=0? "$hourExternal Hr $minExternal Min":($minExternal!=0? "$minExternal Min":""));

												$totalSla[$siteRows['site_id']] = array('row'=>$site_row,'sla'=>$slaTotal);

												$beyondSla[$siteRows['site_id']] = array('row'=>$site_row,'sla'=>$slaExtend);
												
												if(isset($slaExtendArr)){
													$beyondSla[$siteRows['site_id']]['slaArr'] = $slaExtendArr;
												}

											} // Check if close date exist or not

										} // Check if site parent is already charged for SLA or not
										
										$beyondSlaPer[$siteRows['site_id']] = array('row'=>$site_row,'sla'=>round($slaExtendPer,2).'%');

										$avariaCol = $avariaCol+5;

										if($maxCol < $avariaCol){
											$maxCol = $avariaCol;
										}

										$avaria++;

									} // While loop for ticket
									if(isset($dayExternal)){$daySum = $daySum + $dayExternal;}
									if(isset($hourExternal)){$hourSum = $hourSum + $hourExternal;}
									if(isset($minExternal)){$minSum = $minSum + $minExternal;}

									if($minSum>=60){
										while($minSum>=60){
											$minSum = $minSum-60;
											$hourSum = $hourSum+1;
										}
									}
									if($hourSum>=24){
										while($hourSum>=24){
											$hourSum = $hourSum-24;
											$daySum = $daySum+1;
										}
									}
									$site_row++;
								} // Check num rows ticket

							} // check query ticket
							
							$totalExtend = (!empty($dayExt))? array_sum($dayExt)." Day ".array_sum($hourExt)." Hr ".array_sum($minExt)." Min":(!empty($hourExt)? array_sum($hourExt)." Hr ".array_sum($minExt)." Min":(!empty($minExt)? array_sum($minExt)." Min":""));

					        $totalSum = $daySum!=0? "$daySum Day $hourSum Hr $minSum Min":($hourSum!=0? "$hourSum Hr $minSum Min":($minSum!=0? "$minSum Min":""));

					        $objWorkSheet->mergeCells("A1:A2");

					        $col++; $row_count++;
					        $i++;
				      } // While loop sites
				      
				      	$objWorkSheet->getStyleByColumnAndRow($maxCol,1)->applyFromArray($style);
				      	$objWorkSheet->getStyleByColumnAndRow($maxCol+1,1)->applyFromArray($style);
						$objWorkSheet->getStyleByColumnAndRow($maxCol+2,1)->applyFromArray($style);
						$objWorkSheet->getStyleByColumnAndRow($maxCol+3,1)->applyFromArray($style);
						$objWorkSheet->getStyleByColumnAndRow($maxCol+4,1)->applyFromArray($style);

				        $objWorkSheet->setCellValueByColumnAndRow($maxCol,1, 'Total (DD hh) OOS')->mergeCellsByColumnAndRow($maxCol,1,$maxCol,2)->getColumnDimensionByColumn($maxCol)->setAutoSize(true);
						$objWorkSheet->getStyleByColumnAndRow($maxCol,1)->getFont()->setBold(true);

						if(!empty($totalSla)){
							foreach ($totalSla as $row) {
								$objWorkSheet->setCellValueByColumnAndRow($maxCol,$row['row'], $row['sla']);
								$objWorkSheet->getStyleByColumnAndRow($maxCol,$row['row'])->applyFromArray($styleTotal);
							}
						}
						if(!empty($beyondSla)){
							foreach ($beyondSla as $row) {
								if($row['slaArr']!=''){
									$slaTotalBeyond = explode(':', $row['slaArr']);
									array_push($dayExt, $slaTotalBeyond[0]);
									array_push($hourExt, $slaTotalBeyond[1]);
									array_push($minExt, $slaTotalBeyond[2]);	
								}
								$objWorkSheet->setCellValueByColumnAndRow($maxCol+1,$row['row'], $row['sla']);
							}
						}

						if(!empty($beyondSlaPer)){
							foreach ($beyondSlaPer as $row) {
								$objWorkSheet->setCellValueByColumnAndRow($maxCol+2,$row['row'], $row['sla']);
								array_push($extendPerTotal, $row['sla']);
								$extendPerTotalSatge++;
							}
						}

						$objWorkSheet->setCellValueByColumnAndRow($maxCol+1,1, 'Tempo Além do SLA (≥'.$dateDiff.')')->mergeCellsByColumnAndRow($maxCol+1,1,$maxCol+1,2)->getColumnDimensionByColumn($maxCol+1,1)->setAutoSize(true);
						$objWorkSheet->getStyleByColumnAndRow($maxCol+1,1)->getFont()->setBold(true);
						

						$objWorkSheet->setCellValueByColumnAndRow($maxCol+2,1, 'Indisponibilidade (%)')->mergeCellsByColumnAndRow($maxCol+2,1,$maxCol+2,2)->getColumnDimensionByColumn($maxCol+2,1)->setAutoSize(true);
						$objWorkSheet->getStyleByColumnAndRow($maxCol+2,1)->getFont()->setBold(true);
						

						$objWorkSheet->setCellValueByColumnAndRow($maxCol+3,1, 'Observações Desco')->mergeCellsByColumnAndRow($maxCol+3,1,$maxCol+3,2)->getColumnDimensionByColumn($maxCol+3,1)->setAutoSize(true);
						$objWorkSheet->getStyleByColumnAndRow($maxCol+3,1)->getFont()->setBold(true);
						

						$objWorkSheet->setCellValueByColumnAndRow($maxCol+4,1, 'Observações Movicel')->mergeCellsByColumnAndRow($maxCol+4,1,$maxCol+4,2)->getColumnDimensionByColumn($maxCol+4,1)->setAutoSize(true);
						$objWorkSheet->getStyleByColumnAndRow($maxCol+4,1)->getFont()->setBold(true);

				      	$objWorkSheet->setTitle($providerRows['name']);

						$objWorkSheet->setCellValueByColumnAndRow($maxCol,$site_row,$totalSum);
						$objWorkSheet->getStyleByColumnAndRow($maxCol,$site_row)->applyFromArray($styleTotalFinal);

						$finalExtD = array_sum($dayExt); 
						$finalExtH = array_sum($hourExt); 
						$finalExtM = array_sum($minExt);
						if($finalExtM>=60){
							while($finalExtM>=60){
								$finalExtM = $finalExtM-60;
								$finalExtH = $finalExtH+1;
							}
						}
						if($finalExtH>=24){
							while($finalExtH>=24){
								$finalExtH = $finalExtH-24;
								$finalExtD = $finalExtD+1;
							}
						}
						$totalExtend = $finalExtD." Day ".$finalExtH." Hr ".$finalExtM." Min";
						$objWorkSheet->setCellValueByColumnAndRow($maxCol+1,$site_row,$totalExtend);
						$objWorkSheet->getStyleByColumnAndRow($maxCol+1,$site_row)->applyFromArray($styleTotalFinal);

						if($extendPerTotalSatge!=0){
							$extendPerTotalFinal = array_sum($extendPerTotal)/$extendPerTotalSatge;
						}else{
							$extendPerTotalFinal = 0;
						}
						$objWorkSheet->setCellValueByColumnAndRow($maxCol+2,$site_row,round($extendPerTotalFinal,2)."%");
						$objWorkSheet->getStyleByColumnAndRow($maxCol+2,$site_row)->applyFromArray($styleTotalFinal);
					} // Check if num rows sites

				} // If check result of sites query

		} // While loop providers


	} // Check if num rows providers

} // If check result of providers query
$objPHPExcel->removeSheetByIndex(0);
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('nt3WeeklySLAReport.xlsx');

$to  = "nilesh.vishwakarma@nectarinfotel.com";
$subject = 'NT3 - Relatorio de Lojas ['.date('jS F Y').'] ['.date('h:i a').']';
$headers = 'From: nt3system@movicel.co.ao' . "\r\n" .
    'Reply-To: nt3system@movicel.co.ao' . "\r\n" .
    'Cc: Joel.Paka@movicel.co.ao,Joao.Sashitiuti@movicel.co.ao,Gilberto.Modiz@movicel.co.ao' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

// 'Cc: Joel.Paka@movicel.co.ao,Alfredo.Julio@movicel.co.ao,nilesh.vishwakarma@movicel.co.ao' . "\r\n" .

$headers .= "MIME-Version: 1.0\r\n"
  ."Content-Type: multipart/mixed; boundary=\"1a2a3a\"";
 
$message .= "If you can see this MIME than your client doesn't accept MIME types!\r\n"
  ."--1a2a3a\r\n";

	$message .= "Content-Type: text/html; charset=UTF-8"
			  ."Content-Transfer-Encoding: base64\r\n\r\n"
			  .".<br/>".$reportHtml
			  ."<p>Encontre cópia em anexo do NT3 SLA Weekly Report</span></p> <br/><p><b>Obrigado!</b></p> \r\n"
			  ."--1a2a3a\r\n";

			  //."Hello, <br/>NT3 Loja Site Report is generated for ".date('jS F Y')." at ".date('h:i a')

	$file = file_get_contents("nt3WeeklySLAReport.xlsx");
	
	$message .= "Content-Type: image/jpg; name=\"nt3WeeklySLAReport.xlsx\"\r\n"
		  ."Content-Transfer-Encoding: base64\r\n"
		  ."Content-disposition: attachment; file=\"nt3WeeklySLAReport.xlsx\"\r\n"
		  ."\r\n"
		  .chunk_split(base64_encode($file))
		  ."--1a2a3a--";

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

?>