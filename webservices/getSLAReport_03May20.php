<?php 

require_once('../webservices/wbdb.php');      
/*header('Content-Type: text/csv; charset=utf-8');  
header('Content-Disposition: attachment; filename=Site.csv');  
$output = fopen("php://output", "w");  
fputcsv($output, array('Site ID', 'Site Name', 'Province', 'Munciple', 'Locality','Latitude ','Longitude ','Site Code','Vendor','Responsible Area','Priority','Priority Comment','Element Type','Model','MSC','MGW','BSC','RNC','Phase','Service Date','Stage','Sub Stage','Start Date','End_Date','Created Date'));  
$query = "SELECT ntsites.site_id,ntsites.site_name,ntsiteprovince.province,ntsitemunciple.munciple,nplocation.locationname,ntsites.lat,ntsites.lng,ntsites.site_code,ntsites.vendor,ntsites.responsible_area,ntsites.priority,ntsites.priority_comment,ntsites.element_type,ntsites.model,ntsites.msc,ntsites.mgw,ntsites.bsc,ntsites.rnc,ntsites.phase,ntsites.service_date,ntsites.stage,ntsites.sub_stage,ntsites.start_date,ntsites.end_date,ntsites.created_date FROM ntsites join ntsiteprovince on ntsites.province=ntsiteprovince.province_id left join ntsitemunciple on ntsites.munciple=ntsitemunciple.munciple_id left join nplocation on ntsites.locality=nplocation.locationid WHERE ntsites.is_active=1 ORDER BY ntsites.site_id ASC";  
$result = mysqli_query($conf, $query);  
while($row = mysqli_fetch_assoc($result))  
{  
  fputcsv($output, $row);  
}  
fclose($output);*/

require_once('../webservices/PHPExcel/Classes/PHPExcel.php');

$objPHPExcel = new PHPExcel();
/*$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle("NT3SLAReport");				
$objPHPExcel->getActiveSheet()->SetCellValue('A1', "Site Name");
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Título");
$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Servicos Afectados');*/
//$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true);

$providerQuery = "SELECT ctr.name,ctr.id as provider_id,pc.sla_id FROM ntcontract ctr LEFT JOIN ntprovidercontract pc ON pc.id=ctr.id WHERE ctr.finalclass='ProviderContract'";

//tk.start_date BETWEEN '".$fromDate."' AND '".date('Y-m-d H:i:s')."'";

$providerRes = mysqli_query($conf,$providerQuery);
$reportHtml = "";

if($providerRes){
	
	if($providerRes->num_rows>0){	
		
		while ($providerRows = mysqli_fetch_array($providerRes,MYSQLI_ASSOC)) {

				$totalSla = array(); $beyondSla = array(); $beyondSlaPer = array(); // For final col data
				$daySum=0; $hourSum=0; $minSum=0; 

				$query2 = "SELECT st.site_name,st.site_id FROM ntsites st LEFT JOIN ntprovidersites ps ON ps.site_id = st.site_id  WHERE ps.provider_id='".$providerRows['provider_id']."' AND ps.is_active = 1";
				$result2 = mysqli_query($conf,$query2);
				
				if($result2){

					if($result2->num_rows>0){

						$i = 1; $col = 0; $row_count = 1; $site_row = 3; $maxCol = 0;
						

						$slaTotal = ''; // For final col data
						$totalSum = ''; $totalExtend = ''; $totalExtendPer = '';

						$objWorkSheet = $objPHPExcel->createSheet($i); //Setting index when creating

						while ($siteRows = mysqli_fetch_array($result2,MYSQLI_ASSOC)) {
							$dayExt=array(); $hourExt=array(); $minExt=array();
							$avaria = 1; $avariaCol = 1;
					        /*$cell = $objWorkSheet->getCellByColumnAndRow($col, $row_count);
							$colIndex = PHPExcel_Cell::columnIndexFromString($cell->getColumn());*/

					        $objWorkSheet->setCellValue('A1', 'Site Name');
					        $objWorkSheet->setCellValue('A'.$site_row, $siteRows['site_name'])->getColumnDimension('A')->setAutoSize(true);

					        $dateFilter = '';
					        if(isset($_GET['fromDate']) && isset($_GET['toDate'])){
					        	$dateFilter = " AND DATE(tk.start_date) BETWEEN '".$_GET['fromDate']."' AND '".$_GET['toDate']."' ";
					        }

					        $ticketQuery = "SELECT ts.ticket_id,tk.start_date,tk.close_date,st.parent_site FROM ntticketsites ts LEFT JOIN ntticket tk ON tk.id = ts.ticket_id LEFT JOIN ntsites st ON st.site_id = ts.site_id WHERE ts.site_id = '".$siteRows['site_id']."' AND tk.operational_status='closed' $dateFilter";

					        /*$ticketQuery = "SELECT ts.ticket_id,tk.start_date,tk.close_date,st.parent_site FROM ntticketsites ts LEFT JOIN ntticket tk ON tk.id = ts.ticket_id LEFT JOIN ntsites st ON st.site_id = ts.site_id WHERE ts.site_id = '".$siteRows['site_id']."' AND tk.operational_status='closed' AND tk.start_date BETWEEN '".date('Y-m-d H:i:s',strtotime('-1 week'))."' AND '".date('Y-m-d H:i:s')."'";*/

					        //echo $ticketQuery."<br/>";

							$ticketResult = mysqli_query($conf,$ticketQuery);

							if($ticketResult){

								if($ticketResult->num_rows>0){

									$dayExternal = 0; $hourExternal = 0; $minExternal = 0; $slaExtend = ''; $slaExtendPer = '';
									//$preAvaria = 0;
									while ($ticketRows = mysqli_fetch_array($ticketResult,MYSQLI_ASSOC)) {

										$day = 0; $hour = 0; $min = 0;
										/*if($preAvaria!=$avaria){
											$day = 0;
											$preAvaria = $avaria;
										}*/

										/*echo "<pre>";
										print_r($ticketRows);*/

										$mergeNextCol = $avariaCol+4;

										/************* Set Headers **************/
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

												//echo $age->format('%h Hr %i Min %s Sec')."<br/>";

												//$HrMin1 = date("H:i",strtotime((int)$age->format('%h')));
												

												$dayExternal = $dayExternal+$day;
												$hourExternal = $hourExternal+$hour;
												$minExternal = $minExternal+$min;
												if($minExternal>=60){
													$minExternal = $minExternal-60;
													$hourExternal = $hourExternal+1;
												}
												if($hourExternal>=24){
													$hourExternal = $hourExternal-24;
													$dayExternal = $dayExternal+1;
												}

												//echo $siteRows['site_name']."**** $day Days $hour Hr $min Min **** ROW ".$site_row." &nbsp;&nbsp;&nbsp;";
												//if(($hour>=2 && $min>6) || $day>0){
												if((date("H:i",strtotime($hourExternal.":".$minExternal))>"02:06") || $dayExternal>0){

													$tempDt1 = date("H:i",strtotime($hourExternal.":".$minExternal." -2 hours"));
													$tempDt2 = date("H:i",strtotime($tempDt1." -6 minute"));
													$tempDt3 = explode(':', $tempDt2);
													$tempHour = $tempDt3[0];
													$tempMin = $tempDt3[1];

													if($tempHour>22){ 
														$tempDay = $dayExternal-1;
													}else{ 
														$tempDay = $dayExternal;
													}

													/*$slaExtend = $tempDay>0? $tempDay." Day ".$tempHour." Hr ".$tempMin." Min":($tempHour>0)? $tempHour." Hr ".$tempMin." Min":$tempMin." Min";*/

													$slaExtend = $tempDay." Day ".$tempHour." Hr ".$tempMin." Min";
													
													//:
													//$slaExtend = ($day>0)? $day." Day ".($hour-2)." Hr ".($min-6)." Min":($hour>0)? ($hour-2)." Hr ".($min-6)." Min":($min-6)." Min";
													
													/*echo $slaExtend."<br/>";*/

													array_push($dayExt, $dayExternal);
													array_push($hourExt, ($hourExternal-2));
													array_push($minExt, ($minExternal-6));
													/*$dayExt = $dayExt+$day;
													$hourExt = $hourExt+($hour-0);
													$minExt = $minExt+($min-6);*/

													/*if($hourExt>=24){
														$hourExt = $hourExt-24;
														$dayExt = $dayExt+1;
													}
													if($minExt>=60){
														$minExt = $minExt-60;
														$hourExt = $hourExt+1;
													}*/

													$slaExtendPer = ((float)(($hourExternal-2).".".($minExternal-6)))/31*24;
												}

												$slaTotal = $dayExternal!=0? "$dayExternal Day $hourExternal Hr $minExternal Min":($hourExternal!=0? "$hourExternal Hr $minExternal Min":($minExternal!=0? "$minExternal Min":""));

												$totalSla[$siteRows['site_id']] = array('row'=>$site_row,'sla'=>$slaTotal);
												$beyondSla[$siteRows['site_id']] = array('row'=>$site_row,'sla'=>$slaExtend);
												$beyondSlaPer[$siteRows['site_id']] = array('row'=>$site_row,'sla'=>round($slaExtendPer,2).'%');
												
												//$slaData = $age->format('%d Day %h Hr %i Min %s Sec');
												

											} // Check if close date exist or not

										} // Check if site parent is already charged for SLA or not
										
										
										$avariaCol = $avariaCol+5;

										if($maxCol < $avariaCol){
											$maxCol = $avariaCol;
										}

										$avaria++;

									} // While loop for ticket
									if(isset($day)){$daySum = $daySum + $day;}
									if(isset($hour)){$hourSum = $hourSum + $hour;}
									if(isset($min)){$minSum = $minSum + $min;}
								} // Check num rows ticket

							} // check query ticket
							
							
							//$totalExtend = $dayExt!=0? "$dayExt Day $hourExt Hr $minExt Min":($hourExt!=0? "$hourExt Hr $minExt Min":($minExt!=0? "$minExt Min":""));
							$totalExtend = (!empty($dayExt))? array_sum($dayExt)." Day ".array_sum($hourExt)." Hr ".array_sum($minExt)." Min":(!empty($hourExt)? array_sum($hourExt)." Hr ".array_sum($minExt)." Min":(!empty($minExt)? array_sum($minExt)." Min":""));

					        $totalSum = $daySum!=0? "$daySum Day $hourSum Hr $minSum Min":($hourSum!=0? "$hourSum Hr $minSum Min":($minSum!=0? "$minSum Min":""));

					        $objWorkSheet->mergeCells("A1:A2");

					        $col++; $row_count++; $site_row++;

					        $i++;
					       // echo "<br/>";

				      } // While loop sites
				      

				        $objWorkSheet->setCellValueByColumnAndRow($maxCol,1, 'Total (DD hh) OOS')->mergeCellsByColumnAndRow($maxCol,1,$maxCol,2)->getColumnDimensionByColumn($maxCol,1)->setAutoSize(true);
						$objWorkSheet->getStyleByColumnAndRow($maxCol,1)->getFont()->setBold(true);

						if(!empty($totalSla)){
							foreach ($totalSla as $row) {
								$objWorkSheet->setCellValueByColumnAndRow($maxCol,$row['row'], $row['sla']);
								//foreach ($slaTot as $key => $value) { }
							}
						}

						/*echo "<pre>";
						print_r($beyondSla);*/
						if(!empty($beyondSla)){
							foreach ($beyondSla as $row) {
								$objWorkSheet->setCellValueByColumnAndRow($maxCol+1,$row['row'], $row['sla']);
								//foreach ($slaTot as $key => $value) { }
							}
						}

						if(!empty($beyondSlaPer)){
							foreach ($beyondSlaPer as $row) {
								$objWorkSheet->setCellValueByColumnAndRow($maxCol+2,$row['row'], $row['sla']);
								//foreach ($slaTot as $key => $value) { }
							}
						}

						//$objWorkSheet->setCellValueByColumnAndRow($maxCol,3, '=SUM(F3:K3)');

						$objWorkSheet->setCellValueByColumnAndRow($maxCol+1,1, 'Tempo Além do SLA (≥02,06h)')->mergeCellsByColumnAndRow($maxCol+1,1,$maxCol+1,2)->getColumnDimensionByColumn($maxCol+1,1)->setAutoSize(true);
						$objWorkSheet->getStyleByColumnAndRow($maxCol+1,1)->getFont()->setBold(true);
						

						$objWorkSheet->setCellValueByColumnAndRow($maxCol+2,1, 'Indisponibilidade (%)')->mergeCellsByColumnAndRow($maxCol+2,1,$maxCol+2,2)->getColumnDimensionByColumn($maxCol+2,1)->setAutoSize(true);
						$objWorkSheet->getStyleByColumnAndRow($maxCol+2,1)->getFont()->setBold(true);
						

						$objWorkSheet->setCellValueByColumnAndRow($maxCol+3,1, 'Observações Desco')->mergeCellsByColumnAndRow($maxCol+3,1,$maxCol+3,2)->getColumnDimensionByColumn($maxCol+3,1)->setAutoSize(true);
						$objWorkSheet->getStyleByColumnAndRow($maxCol+3,1)->getFont()->setBold(true);
						

						$objWorkSheet->setCellValueByColumnAndRow($maxCol+4,1, 'Observações Movicel')->mergeCellsByColumnAndRow($maxCol+4,1,$maxCol+4,2)->getColumnDimensionByColumn($maxCol+4,1)->setAutoSize(true);
						$objWorkSheet->getStyleByColumnAndRow($maxCol+4,1)->getFont()->setBold(true);

				      	$objWorkSheet->setTitle($providerRows['name']);

						/*$objWorkSheet->setCellValueByColumnAndRow($maxCol,$site_row,$totalSum);
						$objWorkSheet->setCellValueByColumnAndRow($maxCol+1,$site_row,$totalExtend);*/
					} // Check if num rows sites

				} // If check result of sites query

		} // While loop providers


	} // Check if num rows providers

} // If check result of providers query
$objPHPExcel->removeSheetByIndex(0);
/*$objPHPExcel->getActiveSheet()->SetCellValue('A2', "AXR12");
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "AXR12");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "AXR12");*/
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="ProviderSlaReport.xls"');
$objWriter->save('php://output');

?>