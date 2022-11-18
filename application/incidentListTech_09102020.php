
<?php /********** Province ***********/
include('../webservices/wbdb.php');

$jsonData = file_get_contents('php://input');
$postData = json_decode($jsonData,TRUE);
?>
<style type="text/css">
	h1{display: none;}
	#incidentTech_length{float: left;}
	#incidentTech_filter{float: right;}
	.active{
		background-color: green;
	    color: white;
	    padding: 4px 10px 4px 10px;
	    font-size: 9px;
	    border-radius: 3px;
	}
	.inactive{
	    background-color: red;
	    color: white;
	    padding: 4px 6px 4px 6px;
	    font-size: 9px;
	    border-radius: 3px;
	}
	div#incidentTech_paginate {
    float: right;
}
table.listResults td {
    padding: 9px;
}
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">
<div class="ui-layout-content" style="overflow: auto; position: relative; visibility: visible;">
    <h2 style="font-family: Tahoma, Verdana, Arial, Helvetica!important;
    color: #422462!important;font-weight: bold!important;font-size: 12pt!important;">
<img src="https://nt3.nectarinfotel.com/env-production/nt3-incident-mgmt-itil/images/incident.png" style="vertical-align:middle;width: 32px;">&nbsp; Open Incident Technologies</h2>
 	<div class="actions_button">
		<a href="https://nt3.nectarinfotel.com/pages/UI.php?operation=new&class=Incident&c%5Bmenu%5D=NewIncident" style="margin-top: 6px;">New...</a>
		<a href="javascript:void(0)" onclick="exportInc()" style="margin-top: 6px;"><?php echo ($postData['language']=='PT BR')? 'Exportar incidentes abertos':'Export All Open Incidents' ?></a>
		<a href="https://nt3.nectarinfotel.com/pages/openIncPreview.php" target="__change" style="margin-top: 6px;">Report</a>
	</div>
	
    <table class="listResults siteTbl" id="incidentTech">
    	<thead>
    		<tr>
    			<th class="header"><?php echo ($postData['language']=='PT BR')? 'Número do Ticket':'Ticket Number' ?></th>
    			<th class="header"><?php echo ($postData['language']=='PT BR')? 'Título':'Title' ?></th>
    			<th class="header"><?php echo ($postData['language']=='PT BR')? 'Província':'Province' ?></th>
    			<th class="header">Site Principal</th>
    			<th><?php echo ($postData['language']=='PT BR')? 'Dependente':'Dependent' ?></th>
    			<th><?php echo ($postData['language']=='PT BR')? 'Motivo':'Reason' ?></th>
    			<th class="header">2G</th>
    			<th class="header">3G</th>
    			<th class="header">4G</th>
    			<th class="header"><?php echo ($postData['language']=='PT BR')? 'Duração':'Duration' ?></th>
    		</tr>
    	</thead>
    	<tbody id="siteTBody">
		<?php
		$openTickets = CMDBSource::QueryToArray("SELECT tk.id,tk.ref,tk.title,tk.start_date,rs.reason,sbrs.sub_reason,pr.province FROM ntticket tk LEFT JOIN ntticket_incident inc ON inc.id=tk.id LEFT JOIN ntreason rs ON rs.reason_id=tk.reason_id LEFT JOIN ntsubreason sbrs ON sbrs.sub_reason_id=tk.sub_reason_id LEFT JOIN ntsiteprovince pr ON pr.province_id = tk.province_id WHERE tk.operational_status = 'ongoing' AND tk.finalclass='Incident' ORDER BY tk.id DESC"); 
		$i = 0;
		if(!empty($openTickets)){
			foreach ($openTickets as $aDBInfo) { 
				$TWG = FALSE; $TRG = FALSE; $FRG = FALSE;
				$tech = CMDBSource::QueryToArray("SELECT * FROM ntticketnetworks WHERE ticket_id=".$aDBInfo['id']);
				if(!empty($tech)){
					foreach ($tech as $rows) {
						switch ($rows['network']) {
							case '2G': $TWG = TRUE; break;
							case '3G': $TRG = TRUE; break;
							case '4G': $FRG = TRUE; break;
						}
					}
				}
			?>

				<tr>
					<td><a href='https://nt3.nectarinfotel.com/pages/UI.php?operation=details&class=Incident&id=<?php echo $aDBInfo['id'] ?>&c[menu]=Incident%3AOpenIncidents' class='siteDetails'><?php echo $aDBInfo['ref'] ?></td>
					<td><?php echo $aDBInfo['title'] ?></td>
					<td><?php echo $aDBInfo['province'] ?></td>
					<td>
						<?php 
							$allSites = array();
							$dependantSites = array();
							$sites = CMDBSource::QueryToArray("SELECT st.site_name,st.site_id,st.parent_site FROM ntticketsites ts LEFT JOIN ntsites st ON st.site_id=ts.site_id WHERE ts.ticket_id=".$aDBInfo['id']." AND ts.is_active = 1");
							if(!empty($sites)){
								$allSites = array_column($sites, 'site_id');
								foreach ($sites as $rows) {									
									if($rows['parent_site']!=0){
										if(!in_array($rows['parent_site'], $allSites)){
											echo $rows['site_name']."<br/>";
										}else{
											array_push($dependantSites, $rows['site_name']);
										}
									}else{
										echo $rows['site_name']."<br/>";
									}
								}
							}
						?>
					</td>
					<td>
						<?php
							/*$sites = CMDBSource::QueryToArray("SELECT st.site_name FROM ntticketsites ts LEFT JOIN ntsites st ON st.site_id=ts.site_id WHERE ts.ticket_id=".$aDBInfo['id']." AND ts.is_active = 1");*/
							if(!empty($dependantSites)){
								foreach ($dependantSites as $key => $val) {
									echo utf8_encode($val)."<br/>";
								}
							}
					 	?>
					</td>
					<td><?php echo $aDBInfo['reason']."-".$aDBInfo['sub_reason']; ?></td>
					<td><?php echo ($TWG==TRUE)? "<span class='inactive'>DOWN</span>":"<span class='active'>UP</span>"; ?></td>
					<td><?php echo ($TRG==TRUE)? "<span class='inactive'>DOWN</span>":"<span class='active'>UP</span>"; ?></td>
					<td><?php echo ($FRG==TRUE)? "<span class='inactive'>DOWN</span>":"<span class='active'>UP</span>"; ?></td>
					<td>
						<?php
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
							echo $age;
						?>
					</td>
				</tr>
			<?php 
						$i++;
					}
				}else{
			?>
				<tr><td colspan='6' style='text-align: center;'>No Tickets available</td></tr>
			<?php
				}
            ?>
        </tbody>
    </table>
    <br/>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<!-- <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script> -->
<script type="text/javascript">
	
if ( ! $.fn.DataTable.isDataTable( '#incidentTech' ) ) {
	$('#incidentTech').DataTable({
			"order": [[ 0, "desc" ]]
		});
	}

	/*
		,
			"processing": true,
		    "serverSide": true,
		    //any other configuration options
		    "ajax": "application/incidentListTech.php"
	*/

	function exportInc(){
		window.location.href="https://nt3.nectarinfotel.com/pages/exportOpenInc.php";
	}

	/*setInterval(function(){
		if ($.fn.DataTable.isDataTable('#incidentTech') ) {
    		$('#incidentTech').dataTable().fnDraw();
		}
		//console.log('test');
	},10000);*/

	/*setInterval(function(){
		$( "#incidentTech" ).load(window.location.href + " #incidentTech" );
		if ($.fn.DataTable.isDataTable( '#incidentTech' ) ) {
			
			$('#incidentTech').dataTable().fnClearTable();
    		$('#incidentTech').dataTable().fnDestroy();
			$('#incidentTech').DataTable({
					"order": [[ 0, "desc" ]]
				});
			}
			console.log('test');
	},20000);*/
</script>