<?php

include('/home/nt3/webservices/wbdb.php');
require_once('/home/nt3/webservices/PHPExcel/Classes/PHPExcel.php');

	/** No service and priority for change management **/
	$query1 = "SELECT tk.id,tk.finalclass,tk.ref,tk.title,tk.description,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'') as caller,IF(inc.service_id!='',inc.service_id,prob.service_id) as service_id,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'') as agent,tk.start_date,prov.province,IF(inc.urgency!='',inc.urgency,prob.urgency) as priority,rsn.reason,IF(inc.ttr_100_deadline!='',inc.ttr_100_deadline,'') as ttr,tk.operational_status,tk.close_date FROM ntticket tk LEFT JOIN ntticket_incident inc ON (inc.id = tk.id AND tk.finalclass='Incident') LEFT JOIN ntticket_problem prob ON (prob.id = tk.id AND tk.finalclass='Problem') LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') LEFT JOIN ntperson callerper ON callerper.id=callercnt.id LEFT JOIN ntorganization org ON org.id=tk.org_id LEFT JOIN ntsiteprovince prov ON prov.province_id=tk.province_id LEFT JOIN ntreason rsn ON rsn.reason_id=tk.reason_id LEFT JOIN ntticketsites sttk ON sttk.ticket_id=tk.id LEFT JOIN ntsites st ON st.site_id=sttk.site_id WHERE tk.operational_status='ongoing' AND tk.finalclass='Incident' AND st.site_name LIKE '%loja%' ORDER BY tk.id DESC";

	//tk.start_date BETWEEN '".$fromDate."' AND '".date('Y-m-d H:i:s')."' AND

	$result1 = mysqli_query($conf,$query1);
	$reportHtml = "";

	if($result1){

		if($result1->num_rows>0){

			$reportHtml .= "<table style='background:None;width: max-content;font-family: lato;font-size: 13px;color: #969696;'>";

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
						        'color' => array('rgb' => 'F17422')
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
								<td width='60' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Tarefa</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Título</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Servicos Afectados</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Tecnologias Afectados</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Província</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Sites Afectados</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Motivo</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Duração</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Tempo acima do SLA</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Técnico do NOC</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Técnico Responsável</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Data de Criação</td>
							</tr>";

			$rowCount = 2;
			while ($rows = mysqli_fetch_array($result1,MYSQLI_ASSOC)) {
				
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
								if(strpos($rows4['site_name'], "Loja") !== false){
									$aSites .= utf8_encode($rows4['site_name']);
						        	$aHtmlSites .= utf8_encode($rows4['site_name']);
								}
						    }else {
						    	if(strpos($rows4['site_name'], "Loja") !== false){
							        $aSites .= utf8_encode($rows4['site_name'])."\n";
							        $aHtmlSites .= utf8_encode($rows4['site_name'])." *** ";
						    	}
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
				/*$ageData = $age->format('%d Day %h Hr %i Min %s Sec');
				$ageData = strpos($ageData, '0 Day')!==FALSE? (strpos($age->format('%h Hr %i Min'), '0 Hr')!==FALSE? $age->format('%i Min'):$age->format('%h Hr %i Min')):$age->format('%d Day %h Hr %i Min');*/

				/*if($age->format('%y')!=0){
					$ageData = $age->format('%y Year %m Month %d Day %h Hr %i Min %s Sec');
				}else if($age->format('%d')!=0){
					$ageData = $age->format('%m Month %d Day %h Hr %i Min %s Sec');
				}else if($age->format('%d')!=0){
					$ageData = $age->format('%d Day %h Hr %i Min %s Sec');
				}else if($age->format('%d')==0){
					$ageData = $age->format('%h Hr %i Min %s Sec');
				}*/
				$ageData = $age->format('%a Day %h Hr %i Min %s Sec');
				$ageData = strpos($ageData, '0 Day')!==FALSE? (strpos($age->format('%h Hr %i Min'), '0 Hr')!==FALSE? $age->format('%i Min'):$age->format('%h Hr %i Min')):$age->format('%a Day %h Hr %i Min');

		        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $ageData);
		    	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, date('d-m-Y h:i a',strtotime($rows['start_date'])));
		    	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, utf8_encode($rows['reason']));

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

				//echo "Dur : ".$sla."**** Start Date: ".date('Y-m-d H:i',strtotime($rows['start_date']))." **** SLA Date : ".date('Y-m-d H:i',strtotime($rows['start_date'].$sla))." **** Status: ".$slaStatus." **** Close Date:  ".date("Y-m-d H:i",strtotime($rows['close_date']))."<br>";
		    	
		    	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $slaStatus);

		    	if($rowCount%2==0){
	    			$evenOdd="";
	    		}else{
	    			$evenOdd="background-color: #f0f0f5!important;list-style:none;padding:4px 0px;color: #3d3796;";
	    		}
		    	$url = 'https://nt3.nectarinfotel.com/pages/UI.php?operation=details&class='.$rows['finalclass'].'&id='.$rows['id'].'&c[menu]=New'.$rows['finalclass'];
		    	$reportHtml .= "<tr style='".$evenOdd."'>";
				$reportHtml .= "<td style='background-color:#".$color.";font-family: Calibri;font: normal Calibri, sans-serif;'>
								<a href='".$url."' target='_change'>".$rows['ref']."</a></td>";
				$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".utf8_encode($rows['title'])."</td>";
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

			$reportHtml .= "</table>";
			$reportHtml .= "<br/><div><label style='float:left;'>Legends: </label>&nbsp;&nbsp;&nbsp;
				<span style='float:left;text-align:center;background-color:#FF0000;width:100px;height:25px;color: white;margin: 1px 6px;padding: 1px 6px;'>Critical</span>&nbsp;&nbsp;&nbsp;
				<span style='float:left;text-align:center;background-color:#E7782A;width:100px;height:25px;color: white;margin: 1px 6px;padding: 1px 6px;'>High</span>&nbsp;&nbsp;&nbsp;
				<span style='float:left;text-align:center;background-color:#E6E92F;width:100px;height:25px;color: white;margin: 1px 6px;padding: 1px 6px;'>Medium</span>&nbsp;&nbsp;&nbsp;
				<span style='float:left;text-align:center;background-color:#77CC29;width:100px;height:25px;color: white;margin: 1px 6px;padding: 1px 6px;'>Low</span>&nbsp;&nbsp;&nbsp; </div><br/>";
			$reportHtml .= "<div><label>Total Loja OOS: </label> <span style='font-weight:bold;'>".($rowCount-2)."</span></div>";

			$rowCount++;
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
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->freezePane('B2');
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save('nt3LojaDailyReport.xlsx');
			$isExcel = TRUE;
		} // EOF If Ticket Is Created Or Not
		else{
			$isExcel = FALSE;
		}
	}else{
		echo mysqli_error($conf);
	}

	/************************* Mail Part ***************************/

	/*$query2 = "SELECT CNT.email as mailid, CONCAT(PER.first_name,' ',CNT.name) as receivername FROM ntreport_conf_contact RC LEFT JOIN ntpriv_user USR ON USR.contactid=RC.contact_id LEFT JOIN ntperson PER ON PER.id=RC.contact_id LEFT JOIN ntcontact CNT ON CNT.id=RC.contact_id WHERE RC.is_active=1";
	$result2 = $conf->query($query2);*/

	if(TRUE){
	/*if($result2){*/

		//if($result2->num_rows>0){
		if(TRUE){

			//while($rows = mysqli_fetch_array($result2,MYSQLI_ASSOC)){

				$mailMsg = ""; $status = 0;
				//if($rows['mailid']==''){
				if(FALSE){
					$mailMsg = "User do not have mail id";
					$status = 2;
				}else{

					//$to  = $rows['mailid'];
					$//to  = "nilesh.vishwakarma@movicel.co.ao";
					$to  = "noc@movicel.co.ao";
					$subject = 'NT3 - Relatorio de Lojas ['.date('jS F Y').'] ['.date('h:i a').']';
					$headers = 'From: nt3system@movicel.co.ao' . "\r\n" .
					    'Reply-To: nt3system@movicel.co.ao' . "\r\n" .
					    'Cc: Paul.Jaikaran@movicel.co.ao,Pepino.Prazer@movicel.co.ao,Antonio.Francisco@movicel.co.ao,Apolinario.Mavakala@movicel.co.ao,Arlindo.Alves@movicel.co.ao,Joel.Paka@movicel.co.ao,Placido.Contreiras@movicel.co.ao,Diocliciano.Cosme@movicel.co.ao,helder.bras@movicel.co.ao,Alfredo.Julio@movicel.co.ao,nilesh.vishwakarma@movicel.co.ao,DT_Qualidade_da_Rede@movicel.co.ao,DT_Operacoes_RedeCore@movicel.co.ao,DT.DOM.Transmissao@movicel.co.ao,DT.DOM.Rede.Acesso@movicel.co.ao,DT.DOM.IEE@movicel.co.ao,Justino.Katandala@movicel.co.ao,Pedro.Afonso@movicel.co.ao,Joao.Massiala@movicel.co.ao,Carlos.Duarte@movicel.co.ao,Hildebrando.Costa@movicel.co.ao,Felix.Aurelio@movicel.co.ao' . "\r\n" .
					    /*'Cc: Joel.Paka@movicel.co.ao,Alfredo.Julio@movicel.co.ao,nilesh.vishwakarma@movicel.co.ao,helder.bras@movicel.co.ao,paul.jaikaran@movicel.co.ao,Pepino.Prazer@movicel.co.ao,placido.contreiras@movicel.co.ao,DT_Qualidade_da_Rede@movicel.co.ao,DT_Operacoes_RedeCore@movicel.co.ao,DT.DOM.Transmissao@movicel.co.ao,DT.DOM.Rede.Acesso@movicel.co.ao,DT.DOM.IEE@movicel.co.ao' . "\r\n" .*/
					    'X-Mailer: PHP/' . phpversion();

					$headers .= "MIME-Version: 1.0\r\n"
					  ."Content-Type: multipart/mixed; boundary=\"1a2a3a\"";
					 
					$message .= "If you can see this MIME than your client doesn't accept MIME types!\r\n"
					  ."--1a2a3a\r\n";
					 
					if($isExcel){

						$message .= "Content-Type: text/html; charset=UTF-8"
								  ."Content-Transfer-Encoding: base64\r\n\r\n"
								  ."<br/>".$reportHtml
								  ."<p>Please find attached copy of NT3 Report</span></p> <br/><p><b>Thank You!</b></p> \r\n"
								  ."--1a2a3a\r\n";

								  //."Hello, <br/>NT3 Loja Site Report is generated for ".date('jS F Y')." at ".date('h:i a')

						$file = file_get_contents("nt3LojaDailyReport.xlsx");
						
						$message .= "Content-Type: image/jpg; name=\"nt3LojaDailyReport.xlsx\"\r\n"
							  ."Content-Transfer-Encoding: base64\r\n"
							  ."Content-disposition: attachment; file=\"nt3LojaDailyReport.xlsx\"\r\n"
							  ."\r\n"
							  .chunk_split(base64_encode($file))
							  ."--1a2a3a--";
					}else{
						$message .= "Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
							  ."Content-Transfer-Encoding: 7bit\r\n\r\n"
							  ."Hello, <br/> Tickets are not generated for loja sites."
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

				/*$query3 = "INSERT INTO ntreport_history (name,email,status,reason,created_date) VALUES ('".$rows['receivername']."','".$rows['mailid']."',".$status.",'".$mailMsg."','".date('Y-m-d H:i:s')."')";
				$result3 = $conf->query($query3);
				if($result3){
					echo "History Created";
				}else{
					echo "History Failed";
				}*/

			//} // EOF While Loop Contact

		} // EOF Check Has Records In Contact Or Not
		else{
			echo "Contact Empty";
		}
	}// EOF Check Query Executed For Contact
				

?>