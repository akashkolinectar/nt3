<?php
	include('../webservices/wbdb.php');
	if(!isset($_POST['occurence'])){										 			
?>

<div class="ui-layout-content" style="overflow: auto; position: relative; visibility: visible;">
   <h1 style="font-family: Tahoma, Verdana, Arial, Helvetica!important;
                color: #422462!important;font-weight: bold!important;font-size: 12pt!important;">
 	<img src="https://nt3.nectarinfotel.com/images/reportconfig.png" style="vertical-align:middle;width: 32px;">&nbsp;Report Configuration</h1>

<div class="table-responsive">
	<form name='reportConform' id='reportConform'>
<table class="table" style="vertical-align:top">
	<tbody>
		<tr>
			<td style="vertical-align:top; width:33%">
				<div class="details"></div>
				<fieldset>
					<legend>Contact</legend>
					<div class="details">
						<div class="field_container field_small">
						<div class="field_label label">
						<span title=""> Reporting Contact :</span></div>
							<div class="field_data">
								<div class="field_value">
									<div class="field_value_container">
										<div class="attribute-edit">
											<div class="field_input_zone field_input_extkey">
												<select name='reporting_contact[]' class="reporting_contact" style="width: 65%">
											 		<option value="">-- Select One --</option>
											 		<?php
											 			$userModule = CMDBSource::QueryToArray("SELECT CONCAT(per.first_name,' ',cnt.name) as name,cnt.id FROM ntcontact cnt LEFT JOIN ntperson per ON cnt.id=per.id LEFT JOIN ntpriv_user_local usr ON usr.id=cnt.id WHERE cnt.finalclass='Person' ORDER BY per.first_name ASC");
														foreach ($userModule as $aDBInfo) {
															echo "<option value='".$aDBInfo['id']."'>".$aDBInfo['name']."</option>";
														}
											 		?>
											 	</select>
												<!-- <img src="../images/validation_error.png" style="vertical-align:top;margin-left: 5px;" title="Please specify a value"> -->
												<span onclick="addMoreContact()"><a href="javascript:void(0)"><img src="../images/mini_add.gif?t=1561626249.8396" style="vertical-align:middle;"></a></span>  
											<span onclick="removeContact()"><a href="javascript:void(0)">
	<img src="https://nt3.nectarinfotel.com/images/delete_icon1.png" style="vertical-align:middle;width: 20px;"></a></span>
											</div>
											<div class="reporting_dv"></div>
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
						<span title=""> Occurences :</span></div>
							<div class="field_data">
								<div class="field_value">
									<div class="field_value_container">
										<div class="attribute-edit">
											<div class="field_input_zone field_input_extkey">
												<input type="number" name="occurence" id="occurence" style="width:208px;" min="1" value="1" required=""> times per day

												<img src="../images/validation_error.png" style="vertical-align:top;margin-left: 5px;" title="Please specify a value">
												
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
													<span style="float:left">
													 	<input type="time" name="reporting_time[]" class="reporting_time" style="width:208px;">
													 	<!-- <img src="../images/validation_error.png" style="vertical-align:top;" title="Please specify a value"> -->
													</span>
												</div>
											</div>
												<div class="time_dv"></div>
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

<button type="button" class="action cancel" onclick="window.history.back();">Cancel</button>
<button type="submit" class="action">Create</button>
</form>
</div>

</div>

<script type="text/javascript">
	var num = 1;
	function addMoreContact(){
		var cloned = '';
		cloned = $('.reporting_contact').clone().prop('class', 'reporting_contact'+num );
		num++;
		$('.reporting_dv').append(cloned);
	};
	function removeContact(){
		//if(num>1){
			$('.reporting_dv').find('.reporting_contact'+(num-1)).remove();
			num--;
		//}
	};
	$(document).on('change','#occurence',function(){
		var occ = $(this).val();
		$('.time_dv').html('');
		for(var i=1; i<occ; i++){
			var cloned = $('.reporting_time').clone().prop('class', 'reporting_time'+i );
			$('.time_dv').append(cloned);
		}
	});
	$('#reportConform').on('submit',function(evt){
		evt.preventDefault();
		$.ajax({
			url: '../application/report_conf_add.php',
			type: 'post',
			data: $(this).serialize(),
			dataType: 'json',
			success: function(res){
				if(res.contact==true && res.time==true){
					alert('New contact person and new time are added for daily report notification');
				}else if(res.contact==true){
					alert('New contact person is added for daily report notification');
				}else if(res.time==true){
					alert('New time is added for daily report notification');
				}else{
					alert('No New changes');
				}
				console.log(res);
				window.location.href = 'https://nt3.nectarinfotel.com/pages/UI.php?c[menu]=reportConfiguration';
			},
			error: function(xhr){
				console.log(xhr);
			}
		});
	});
</script>

<?php 
}else if(isset($_POST['occurence'])){
	$data = array('contact'=>FALSE,'time'=>FALSE);

	if($_POST['reporting_contact'][0]!=''){
		foreach ($_POST['reporting_contact'] as $key => $value) {
			$query1 = CMDBSource::InsertInto("INSERT INTO ntreport_conf_contact (`contact_id`,`created_date`) VALUES (".$value.",'".date('Y-m-d H:i:s')."')");
			if($query1){
				$data['contact'] = TRUE;
			}
		}
	}
	
	if($_POST['reporting_time'][0]!=''){
		foreach ($_POST['reporting_time'] as $key => $value) {
			$query1 = CMDBSource::InsertInto("INSERT INTO ntreport_conf_time (`time`,`created_date`) VALUES ('".$value."','".date('Y-m-d H:i:s')."')");
			if($query1){
				$data['time'] = TRUE;
			}
		}
	}
	echo json_encode($data);
}

?>
