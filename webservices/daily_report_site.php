<?php

include('/home/nt3/webservices/wbdb.php');
require_once('/home/nt3/webservices/PHPExcel/Classes/PHPExcel.php');

	/** No service and priority for change management **/
	$query1 = "SELECT tk.id,tk.finalclass,tk.ref,tk.title,tk.description,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'') as caller,IF(inc.service_id!='',inc.service_id,prob.service_id) as service_id,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'') as agent,tk.start_date,prov.province,IF(inc.urgency!='',inc.urgency,prob.urgency) as priority,rsn.reason,IF(inc.ttr_100_deadline!='',inc.ttr_100_deadline,'') as ttr,tk.operational_status,tk.close_date,subrsn.sub_reason FROM ntticket tk LEFT JOIN ntticket_incident inc ON (inc.id = tk.id AND tk.finalclass='Incident') LEFT JOIN ntticket_problem prob ON (prob.id = tk.id AND tk.finalclass='Problem') LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person') LEFT JOIN ntperson callerper ON callerper.id=callercnt.id LEFT JOIN ntorganization org ON org.id=tk.org_id LEFT JOIN ntsiteprovince prov ON prov.province_id=tk.province_id LEFT JOIN ntreason rsn ON rsn.reason_id=tk.reason_id LEFT JOIN ntsubreason subrsn ON subrsn.sub_reason_id=tk.sub_reason_id LEFT JOIN ntticketsites sttk ON sttk.ticket_id=tk.id LEFT JOIN ntsites st ON st.site_id=sttk.site_id WHERE tk.operational_status='ongoing' AND tk.finalclass='Incident' AND st.site_name NOT LIKE '%loja%' GROUP BY tk.id ORDER BY tk.id DESC";
	$result1 = mysqli_query($conf,$query1);
	$reportHtml = "";
 	$color = 'FFFFFF'; $oosCount=0;
	if($result1){

		if($result1->num_rows>0){

			$reportHtml .= "<table style='background:None;width: max-content;font-family: lato;font-size: 13px;color: #969696;'>";

			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle("NT3Report");				
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', "Tarefa");
			$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Título");
			$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Província');
			$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Site Principal');
			$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Dependente');
			$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Motivo');
			$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Duração');
			$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);

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
			$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArr);
			$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(-1);

			$reportHtml .= "<tr style='background-color:#422462;text-align:center;'>
								<td width='60' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Tarefa</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Título</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Província</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Site Principal</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Dependente</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Motivo</td>
								<td width='100' style='list-style:none;padding:4px 9px;font-weight:bold;background-color: #dfdfe4;color: #262262;font-size: 14px;'>Duração</td>
							</tr>";

			$rowCount = 2;
			while ($rows = mysqli_fetch_array($result1,MYSQLI_ASSOC)) {
				
				$styleIdArr = array(
						        'borders' => array(
								    'allborders' => array(
								      'style' => PHPExcel_Style_Border::BORDER_THIN,
								      'color' => array('rgb' => '000000')
								    )
							  	)
					    	);
			 	/************* Dependant and principal sites *******************/
			    $allSites = array();
	            $dependantSitesHtml = ""; $sitePricipalHtml = "";
	            $dependantSites = ""; $sitePricipal = "";
	          
	            $sites = CMDBSource::QueryToArray("SELECT st.site_name,st.site_id,st.parent_site FROM ntticketsites ts LEFT JOIN ntsites st ON st.site_id=ts.site_id WHERE ts.ticket_id=".$rows['id']." AND ts.is_active = 1");

	            if(!empty($sites)){
	              $allSites = array_column($sites, 'site_id');
	              foreach ($sites as $rowsData) {                                  
	                  if($rowsData['parent_site']!=0){
	                      if(!in_array($rowsData['parent_site'], $allSites)){

	                          $sitePricipal .= utf8_encode($rowsData['site_name'])."\n";
	                          $sitePricipalHtml .= utf8_encode($rowsData['site_name']).", <br>";
	                      }else{
	                          $dependantSites .= utf8_encode($rowsData['site_name'])."\n";
	                          $dependantSitesHtml .= utf8_encode($rowsData['site_name']).", <br>";
	                      }
	                  }else{
	                      $sitePricipal .= utf8_encode($rowsData['site_name'])."\n";
	                      $sitePricipalHtml .= utf8_encode($rowsData['site_name']).", <br>";
	                  }
	                  $oosCount++;
	              }
	            }
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
				$ageData = $age->format('%a Day %h Hr %i Min %s Sec');
				$ageData = strpos($ageData, '0 Day')!==FALSE? (strpos($age->format('%h Hr %i Min'), '0 Hr')!==FALSE? $age->format('%i Min'):$age->format('%h Hr %i Min')):$age->format('%a Day %h Hr %i Min');

				$query3 = "SELECT * FROM ntticketnetworks WHERE ticket_id = ".$rows['id'];
				$result3 = mysqli_query($conf,$query3);
				if($result3){
					$numResults = mysqli_num_rows($result3);
					$TWG = FALSE; $TRG = FALSE; $FRG = FALSE;
					$colorTWG = '0bb010'; $colorTRG = '0bb010'; $colorFRG = '0bb010';
					$statusTWG = 'UP'; $statusTRG = 'UP'; $statusFRG = 'UP';
					if($numResults>0){
						while($row = mysqli_fetch_array($result3,MYSQLI_ASSOC)) {
							switch ($row['network']) {
								case '2G': $TWG = TRUE; $colorTWG = 'f0291a'; $statusTWG='DOWN'; break;
								case '3G': $TRG = TRUE; $colorTRG = 'f0291a'; $statusTRG='DOWN'; break;
								case '4G': $FRG = TRUE; $colorFRG = 'f0291a'; $statusFRG='DOWN'; break;
							}
						}
					}
					
				}

				if($TWG==TRUE && $TRG==TRUE){
					$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $rows['ref']);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleIdArr);
					$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$rowCount)->getHyperlink()->setUrl('https://nt3.nectarinfotel.com/pages/UI.php?operation=details&class='.$rows['finalclass'].'&id='.$rows['id'].'&c[menu]=New'.$rows['finalclass']);
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, utf8_encode($rows['title']));
			    	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, utf8_encode($rows['province']));
			    	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $sitePricipal);
			    	$objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->getAlignment()->setWrapText(true);
			    	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $dependantSites);
			    	$objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setWrapText(true);
			    	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, utf8_encode($rows['reason'])."-".utf8_encode($rows['sub_reason']));
			        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $ageData);
				}

		    	if($TWG==TRUE && $TRG==TRUE){
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
			    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".utf8_encode($rows['province'])."</td>";
			    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".rtrim($sitePricipalHtml, ',')."</td>";
			    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".rtrim($dependantSitesHtml, ',')."</td>";
			    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".utf8_encode($rows['reason'])."-".utf8_encode($rows['sub_reason'])."</td>";
			    	$reportHtml .= "<td style='font-family: Calibri;font: normal Calibri, sans-serif;'>".$ageData."</td>";
			    	$reportHtml .= "</tr>";

			    	$rowCount++;
		    	}

			} // EOF While Loop

			$reportHtml .= "</table>";
			$reportHtml .= "<br/><div><label style='float:left;'>Legends: </label>&nbsp;&nbsp;&nbsp;
				<span style='float:left;text-align:center;background-color:#FF0000;width:100px;height:25px;color: white;margin: 1px 6px;padding: 1px 6px;'>Critical</span>&nbsp;&nbsp;&nbsp;
				<span style='float:left;text-align:center;background-color:#E7782A;width:100px;height:25px;color: white;margin: 1px 6px;padding: 1px 6px;'>High</span>&nbsp;&nbsp;&nbsp;
				<span style='float:left;text-align:center;background-color:#E6E92F;width:100px;height:25px;color: white;margin: 1px 6px;padding: 1px 6px;'>Medium</span>&nbsp;&nbsp;&nbsp;
				<span style='float:left;text-align:center;background-color:#77CC29;width:100px;height:25px;color: white;margin: 1px 6px;padding: 1px 6px;'>Low</span>&nbsp;&nbsp;&nbsp; </div><br/>";
			//$reportHtml .= "<div><label>Total OOS: </label> <span style='font-weight:bold;'>".$oosCount."</span></div>";

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
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->freezePane('B2');
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save('nt3ServAfectGSM_UMTS_LTE.xlsx');
			$isExcel = TRUE;
		} // EOF If Ticket Is Created Or Not
		else{
			$isExcel = FALSE;
		}
	}else{
		echo mysqli_error($conf);
	}

	/************************* Mail Part ***************************/
	//$to  = 'nilesh.vishwakarma@movicel.co.ao';
	//$to  = 'nilesh.vishwakarma@nectarinfotel.com';
	$to  = "noc@movicel.co.ao";
	$subject = 'NT3 - Relatorio de Servicos Afectados(GSM/UMTS/LTE) ['.date('jS F Y').'] ['.date('h:i a').']';
	$headers = 'From: nt3system@movicel.co.ao' . "\r\n" .
	    'Reply-To: nt3system@movicel.co.ao' . "\r\n" .
     	'Cc: Paul.Jaikaran@movicel.co.ao,Pepino.Prazer@movicel.co.ao,Antonio.Francisco@movicel.co.ao,Apolinario.Mavakala@movicel.co.ao,Arlindo.Alves@movicel.co.ao,Joel.Paka@movicel.co.ao,Placido.Contreiras@movicel.co.ao,Diocliciano.Cosme@movicel.co.ao,helder.bras@movicel.co.ao,Alfredo.Julio@movicel.co.ao,nilesh.vishwakarma@movicel.co.ao,DT_Qualidade_da_Rede@movicel.co.ao,DT_Operacoes_RedeCore@movicel.co.ao,DT.DOM.Transmissao@movicel.co.ao,DT.DOM.Rede.Acesso@movicel.co.ao,DT.DOM.IEE@movicel.co.ao,Justino.Katandala@movicel.co.ao,Pedro.Afonso@movicel.co.ao,Joao.Massiala@movicel.co.ao,Carlos.Duarte@movicel.co.ao,Hildebrando.Costa@movicel.co.ao,Felix.Aurelio@movicel.co.ao' . "\r\n" .
	   // 'Cc: Joel.Paka@movicel.co.ao'. "\r\n" .
	   /* 'Cc: Joel.Paka@movicel.co.ao,Alfredo.Julio@movicel.co.ao,nilesh.vishwakarma@movicel.co.ao,helder.bras@movicel.co.ao,paul.jaikaran@movicel.co.ao,Pepino.Prazer@movicel.co.ao,placido.contreiras@movicel.co.ao,DT_Qualidade_da_Rede@movicel.co.ao,DT_Operacoes_RedeCore@movicel.co.ao,DT.DOM.Transmissao@movicel.co.ao,DT.DOM.Rede.Acesso@movicel.co.ao,DT.DOM.IEE@movicel.co.ao' . "\r\n" .*/
	    'X-Mailer: PHP/' . phpversion();

	// 'Cc: Joel.Paka@movicel.co.ao,Alfredo.Julio@movicel.co.ao,nilesh.vishwakarma@nectarinfotel.com' . "\r\n" .

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

		$file = file_get_contents("nt3ServAfectGSM_UMTS_LTE.xlsx");
		
		$message .= "Content-Type: image/jpg; name=\"nt3ServAfectGSM_UMTS_LTE.xlsx\"\r\n"
			  ."Content-Transfer-Encoding: base64\r\n"
			  ."Content-disposition: attachment; file=\"nt3ServAfectGSM_UMTS_LTE.xlsx\"\r\n"
			  ."\r\n"
			  .chunk_split(base64_encode($file))
			  ."--1a2a3a--";
	}else{
		$message .= "Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
			  ."Content-Transfer-Encoding: 7bit\r\n\r\n"
			  ."Hello, <br/> Tickets are not generated for Service Affected (GSM) Report."
			  ."<br/><p><b>Thank You!</b></p> \r\n"
			  ."--1a2a3a\r\n";
	}

	$success = mail($to, $subject, $message, $headers);
	if (!$success) {
		echo "Mail to " . $to . " failed .";
	}else {
		echo "Success : Mail was send to " . $to . " **** Time : ".date("h:i a");
	}
?>