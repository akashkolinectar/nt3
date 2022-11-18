<?php
	include('../webservices/wbdb.php');							 			
?>
<style type="text/css">
	#reportHistoryTbl_length{float: left;}
</style>
<div class="ui-layout-content" style="overflow: auto; position: relative; visibility: visible;">
   <h1 style="font-family: Tahoma, Verdana, Arial, Helvetica!important;
                color: #422462!important;font-weight: bold!important;font-size: 12pt!important;">
         <img src="https://nt3.nectarinfotel.com/images/reportconfig.png" style="vertical-align:middle;width: 32px;">&nbsp;Report Configuration</h1>

<button class="action" style="float: right;" onclick="window.location.href='https://nt3.nectarinfotel.com/pages/UI.php?c[menu]=reportConfigurationAdd'">Add More Contact/Duration</button>

<div class="table-responsive" style="margin-top: 42px;">
<table class="table" style="vertical-align:top;width: 100%;">
	<tbody>
		<tr>
			<td style="vertical-align:top; width:30%">
				<div class="details">
				</div>
				<fieldset>
					<legend>Contact</legend>
					<div class="details">
						<div class="field_container field_small">
				<div class="field_label label" style="width: 32%;">
						<span title=""> Reporting Contact :</span></div>
							<div class="field_data">
								<div class="field_value">
									<div class="field_value_container">
										<div class="attribute-edit">
											<div class="field_input_zone field_input_extkey" id='contactdv'>
											 		<?php
											 			$userModule = CMDBSource::QueryToArray("SELECT CONCAT(per.first_name,' ',cnt.name) as name,cnt.id FROM ntcontact cnt LEFT JOIN ntperson per ON cnt.id=per.id LEFT JOIN ntpriv_user_local usr ON usr.id=cnt.id LEFT JOIN ntreport_conf_contact rcc ON rcc.contact_id=cnt.id WHERE cnt.finalclass='Person' AND rcc.is_active=1 ORDER BY per.first_name ASC");
														foreach ($userModule as $aDBInfo) {
															/*echo $aDBInfo['name']." <span class='".$aDBInfo['id']." contact' onclick='removeContact(".$aDBInfo['id'].",\"contact\")'><a href='javascript:void(0)'> Remove</a></span><br/><br/>";*/
															echo $aDBInfo['name']." <span class='".$aDBInfo['id']." contact reportDataRmove'><a href='javascript:void(0)'><img src='https://nt3.nectarinfotel.com/images/delete_icon3.png' style='width: 10px;'></a></span><br/><br/>";
														}
											 		?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
			</fieldset>
		</td>

		<td style="vertical-align:top; width:33%">
				<div class="details"></div>
				<fieldset>
					<legend>Duration</legend>
					<div class="details">
						<div class="field_container field_small">
						<div class="field_label label">
						<span title=""> Occurrences :</span></div>
							<div class="field_data">
								<div class="field_value">
									<div class="field_value_container">
										<div class="attribute-edit">
											<div class="field_input_zone field_input_extkey occurences">
												<?php 

													$times = '';
													$timeModule = CMDBSource::QueryToArray("SELECT `time`,`id` FROM ntreport_conf_time  WHERE is_active=1");
													foreach ($timeModule as $aDBInfo) {
														/*$times .= $aDBInfo['time']." <span class='".$aDBInfo['id']." time reportDataRmove'><a href='javascript:void(0)' onclick='removeDetail(".$aDBInfo['id'].",\"time\")'> Remove</a></span><br/><br/>";*/
														$times .= $aDBInfo['time']."<span class='".$aDBInfo['id']." time reportDataRmove'><a href='javascript:void(0)'><img src='https://nt3.nectarinfotel.com/images/delete_icon3.png' style='width: 10px;'></a></span><br/><br/>";
													}
													echo count($timeModule);
												?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="field_container field_small">
						<div class="field_label label"><span title=""> Reporting Time :</span></div>
							<div class="field_data">
								<div class="field_value">
									<div class="field_value_container">
										<div class="attribute-edit">
											<div class="field_input_zone field_input_extkey">
												<div class="field_select_wrapper">
													<span style="float:left" id='timespan'>
													 	<?php echo $times; ?>
													</span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
			</fieldset>
		</td>

	</tr>
	</tbody>
</table>
</div>
</div>


<h1> Report History </h1>

<div class="table-responsive">
	<table class="listResults" id='reportHistoryTbl'>
		<thead>
			<th>id</th>
			<th>Name</th>
			<th>Email</th>
			<th>Status</th>
			<th>Result</th>
			<th>Date</th>
			<th>Time</th>
		</thead>
		<tbody>
			<?php 
				$historyModule = CMDBSource::QueryToArray("SELECT * FROM ntreport_history WHERE is_active = 1");
				$i = 1;
				if(!empty($historyModule)){
					foreach ($historyModule as $aDBInfo) {
						$status = $aDBInfo['status']==1? "Success":($aDBInfo['status']==2? "Failure":"No Result");
						echo "<tr> <td>$i</td> <td>".$aDBInfo['name']."</td> <td>".$aDBInfo['email']."</td>
							 	<td>$status</td> <td>".$aDBInfo['reason']."</td> <td>".date('d-m-Y',strtotime($aDBInfo['created_date']))."</td> <td>".date('h:i a',strtotime($aDBInfo['created_date']))."</td>
						 	 </tr>";
						$i++;
					}
				}else{
					echo "<tr><td colspan='7'>Report History Unavailable</td></tr>";
				}
			?>
		</tbody>
	</table>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$("#reportHistoryTbl").DataTable({
			"pagingType": "full_numbers",
			"pageLength": 10
		});
	});
	//function removeDetail(id,title){
	$(document).on('click','.reportDataRmove',function(){
		var attr = $(this).attr('class');
		var info = attr.split(' ');
		$.ajax({
			url: 'otherFields.php',
			data: {'id':info[0],'field':'removeReportDetail','title':info[1]},
			type: 'POST',
			//dataType: 'json',
			success: function(res){
				console.log(res);
				if(info[1]=='contact'){
					$("#contactdv").html(res);
				}else{
					var occ = $(".occurences").html();
					$(".occurences").html(occ-1);
					$("#timespan").html(res);
				}
			}
		});
	});
		
	//}
</script>