<?php
	include('../webservices/wbdb.php');
	if(isset($_GET['id'])){

	$query1 = "SELECT * FROM `ntsites` WHERE `is_active` = 1 AND `site_id`=".$_GET['id'];
	$result1 = mysqli_query($conf, $query1);
	$info = mysqli_fetch_all($result1, MYSQLI_ASSOC);

	$info[0]['province'] = iconv('UTF-8', 'ISO-8859-1//IGNORE', $info[0]['province']);

	$query2 = "SELECT * FROM `ntsitenetwork` WHERE `is_active` = 1 AND `site_id`=".$_GET['id'];
	$result2 = mysqli_query($conf, $query2);
	$str = array();

	foreach (mysqli_fetch_all($result2, MYSQLI_ASSOC) as $rows) {
		array_push($str, $rows['network']);
	}
	$info[0]['network'] = $str;
	$site = $info[0];
	//print_r($site);

	/********** Province ***********/
	$query1 = "SELECT * FROM ntsiteprovince WHERE is_active = 1";
	$result1 = mysqli_query($conf, $query1);
	$province = "<select name='site_province' id='site_province'>";					
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {	
		$selected = ($site['province']==$aDBInfo['province_id'])? "selected='selected'":"";
		$province .= "<option value='".$aDBInfo['province_id']."' $selected>".$aDBInfo['province']."</option>";
	}
	$province .= "</select>";

	/********** Responsible Site ***********/
	$query1 = "SELECT * FROM ntsiteresponsible WHERE is_active = 1 ORDER BY responsible_area DESC";
	$result1 = mysqli_query($conf, $query1);
	$responsible = "<select name='site_responsible' id='site_responsible'><option value=''> -- Select One --</option>";					
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {

		$selected = ($site['responsible_area']==$aDBInfo['responsible_area'])? "selected='selected'":"";
		$responsible .= "<option value='".$aDBInfo['responsible_area']."' $selected>".$aDBInfo['responsible_area']."</option>";
	}
	$responsible .= "</select>";

	/********** Priority Site ***********/
	$query1 = "SELECT * FROM ntsitepriority WHERE is_active = 1 ORDER BY priority DESC";
	$result1 = mysqli_query($conf, $query1);
	$priority = "<select name='site_priority' id='site_priority'><option value=''> -- Select One --</option>";					
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {

		$selected = ($site['priority']==$aDBInfo['priority'])? "selected='selected'":"";
		$priority .= "<option value='".$aDBInfo['priority']."' $selected>".$aDBInfo['priority']."</option>";
	}
	$priority .= "</select>";

	/********** Munciple Site ***********/
	$query1 = "SELECT * FROM ntsitemunciple WHERE is_active = 1 ORDER BY munciple DESC";
	$result1 = mysqli_query($conf, $query1);
	$munciple = "<select name='site_munciple' id='site_munciple'><option value=''> -- Select One --</option>";
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {

		$selected = ($site['munciple']==$aDBInfo['munciple_id'])? "selected='selected'":"";
		$munciple .= "<option value='".$aDBInfo['munciple_id']."' $selected>".$aDBInfo['munciple']."</option>";
	}
	$munciple .= "</select>";

	/********** Element Type Site ***********/
	$query1 = "SELECT * FROM ntsiteelementtype WHERE is_active = 1 ORDER BY element_type DESC";
	$result1 = mysqli_query($conf, $query1);
	$elementType = "<select name='site_element_type' id='site_element_type'><option value=''> -- Select One --</option>";					
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {

		$selected = ($site['element_type']==$aDBInfo['element_type'])? "selected='selected'":"";
		$elementType .= "<option value='".$aDBInfo['element_type']."' $selected>".$aDBInfo['element_type']."</option>";
	}
	$elementType .= "</select>";

	/********** Vendor Site ***********/
	$query1 = "SELECT * FROM ntsitevendor WHERE is_active = 1 ORDER BY vendor DESC";
	$result1 = mysqli_query($conf, $query1);
	$vendor = "<select name='site_vendor' id='site_vendor'><option value=''> -- Select One --</option>";					
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {

		$selected = ($site['vendor']==$aDBInfo['vendor'])? "selected='selected'":"";
		$vendor .= "<option value='".$aDBInfo['vendor']."' $selected>".$aDBInfo['vendor']."</option>";
	}
	$vendor .= "</select>";

	/********** Model Site ***********/
	$query1 = "SELECT * FROM ntsitemodel WHERE is_active = 1 ORDER BY model DESC";
	$result1 = mysqli_query($conf, $query1);
	$model = "<select name='site_model' id='site_model'><option value=''> -- Select One --</option>";
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {

		$selected = ($site['model']==$aDBInfo['model'])? "selected='selected'":"";
		$model .= "<option value='".$aDBInfo['model']."' $selected>".$aDBInfo['model']."</option>";
	}
	$model .= "</select>";

	/********** MSC Site ***********/
	$query1 = "SELECT * FROM ntsitemsc WHERE is_active = 1 ORDER BY msc DESC";
	$result1 = mysqli_query($conf, $query1);
	$msc = "<select name='site_msc' id='site_msc'><option value=''> -- Select One --</option>";					
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {

		$selected = ($site['msc']==$aDBInfo['msc'])? "selected='selected'":"";
		$msc .= "<option value='".$aDBInfo['msc']."' $selected>".$aDBInfo['msc']."</option>";
	}
	$msc .= "</select>";

	/********** MGW Site ***********/
	$query1 = "SELECT * FROM ntsitemgw WHERE is_active = 1 ORDER BY mgw DESC";
	$result1 = mysqli_query($conf, $query1);
	$mgw = "<select name='site_mgw' id='site_mgw'><option value=''> -- Select One --</option>";					
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {

		$selected = ($site['mgw']==$aDBInfo['mgw'])? "selected='selected'":"";
		$mgw .= "<option value='".$aDBInfo['mgw']."' $selected>".$aDBInfo['mgw']."</option>";
	}
	$mgw .= "</select>";

	/********** BSC Site ***********/
	$query1 = "SELECT * FROM ntsitebsc WHERE is_active = 1 ORDER BY bsc DESC";
	$result1 = mysqli_query($conf, $query1);
	$bsc = "<select name='site_bsc' id='site_bsc'><option value=''> -- Select One --</option>";					
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {

		$selected = ($site['bsc']==$aDBInfo['bsc'])? "selected='selected'":"";
		$bsc .= "<option value='".$aDBInfo['bsc']."' $selected>".$aDBInfo['bsc']."</option>";
	}
	$bsc .= "</select>";

	/********** Phase Site ***********/
	$query1 = "SELECT * FROM ntsitephase WHERE is_active = 1 ORDER BY phase DESC";
	$result1 = mysqli_query($conf, $query1);
	$phase = "<select name='site_phase' id='site_phase'><option value=''> -- Select One --</option>";					
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {

		$selected = ($site['phase']==$aDBInfo['phase'])? "selected='selected'":"";
		$phase .= "<option value='".$aDBInfo['phase']."' $selected>".$aDBInfo['phase']."</option>";
	}
	$phase .= "</select>";					

	/********** Stage Site ***********/
	$query1 = "SELECT * FROM ntsitestage WHERE is_active = 1 ORDER BY stage DESC";
	$result1 = mysqli_query($conf, $query1);
	$stage = "<select name='site_stage' id='site_stage'><option value=''> -- Select One --</option>";					
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {

		$selected = ($site['stage']==$aDBInfo['stage'])? "selected='selected'":"";
		$stage .= "<option value='".$aDBInfo['stage']."' $selected>".$aDBInfo['stage']."</option>";
	}
	$stage .= "</select>";

	/********** Sub Stage Site ***********/
	$query1 = "SELECT * FROM ntsitesubstage WHERE is_active = 1 ORDER BY sub_stage DESC";
	$result1 = mysqli_query($conf, $query1);
	$subStage = "<select name='site_sub_stage' id='site_sub_stage'><option value=''> -- Select One --</option>";					
	while ($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {

		$selected = ($site['sub_stage']==$aDBInfo['sub_stage'])? "selected='selected'":"";
		$subStage .= "<option value='".$aDBInfo['sub_stage']."' $selected>".$aDBInfo['sub_stage']."</option>";
	}
	$subStage .= "</select>";
?>

<div class="ui-layout-content" style="overflow: auto; position: relative; visibility: visible;">
	<h1><img src="https://nt3.nectarinfotel.com/images/addactivity.png" style="vertical-align:middle;width: 32px;">&nbsp; Modify Site Information </h1>
                      
<!-- <div class="formactivity"> -->
                                            
<div class="table-responsive">

<form class="siteAdd" method="POST">
<div class="table-responsive">
<table class="table" style="vertical-align:top">
<tbody>
	<tr>
	<td style="vertical-align:top; width:33%">
	<div class="details">
    </div>
<fieldset>
<legend>Site</legend>
<div class="details">
<div class="field_container field_small">
<div class="field_label label">
<span title="">Site Code :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
	<input type="text" name="site_code" id="site_code" value="<?php echo $site['site_code'] ?>" style="width:225px;">
<!-- <div class="field_select_wrapper">
	<span style="float:left">
	<input type="text" name="site_id" id="site_id" style="width:225px;">
	</span>
	<span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;">
	<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
	</span>
</div> -->
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Site Name :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
	 <span style="float:left">
	 	<input type="text" style="width:225px;" value="<?php echo $site['site_name'] ?>" name="site_name" id="site_name"></span>
	    <input type="hidden" value="<?php echo $site['site_id'] ?>" name="site_id" id="site_id">
	<!-- <span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;">
	<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
	</span> -->
	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Network :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
	 <span style="float:left;padding-right: 57px;">
				            <input type="checkbox" name="network[]" id="2G" value="2G" <?php echo (in_array('2G', $site['network'])? "checked='checked'":""); ?>>2G
				            <input type="checkbox" name="network[]" id="3G" value="3G" <?php echo (in_array('3G', $site['network'])? "checked='checked'":""); ?>>3G
				            <input type="checkbox" name="network[]" id="4G" value="4G" <?php echo (in_array('4G', $site['network'])? "checked='checked'":""); ?>>4G
				    		</span>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Responsible area :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;"><?php echo $responsible; ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Priority :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;"><?php echo $priority; ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Priority Comment :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left">
<textarea name="priority_comment" id="priority_comment" style="width:225px;" value="<?php echo $site['priority_comment']; ?>"><?php echo htmlspecialchars($site['priority_comment']); ?></textarea>
	</span>	
	</div>
</div>
</div></div></div>
</div>
</div>
</fieldset>

<fieldset>
<legend>Model</legend>
<div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title="">Element Type : </span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
	<span style="float:left;"><?php echo $elementType; ?> </span>
</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Vendor :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;"><?php echo $vendor; ?></span>
<!-- <span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;">
	<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
</span>	 -->
</div>
</div>
</div></div></div>
</div>
</div>

<div class="field_container field_small">
<div class="field_label label"><span title="">Model :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
	<span style="float:left;"><?php echo $model ?>	</span>	
	</div>
</div>
</div></div></div>
</div>
</div>
</fieldset>
<fieldset>
<legend>Dependency</legend>
<div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title="">MSC : </span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
	<span style="float:left;"><?php echo $msc ?></span>
</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">MGW :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;">	<?php echo $mgw; ?> </span>
</div>
</div>
</div></div></div>
</div>
</div>

<div class="field_container field_small">
<div class="field_label label"><span title="">BSC : </span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
	<span style="float:left;"><?php echo $bsc ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
</fieldset>
</td>

	<td style="vertical-align:top; width:33%">
	<div class="details">
    </div>
<fieldset>
<legend>Localization</legend>
<div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title="">Province :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
 <select name="province" id="nsite_province" style="width: 223px;">
					            <option>Select province</option>
					            <?php  
					             //echo "test"; 
					             $query = "SELECT * FROM ntsiteprovince WHERE is_active = 1";
					                        if($query!=''){
					        $result = mysqli_query($conf, $query);
					        if ($result) {

					            if(mysqli_num_rows($result)>0){
					                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
					            <option value="<?php echo $row['province_id'] ?>" <?php echo ($row['province_id']==$site['province'])? "selected='selected'":"" ?>><?php echo $row['province'] ?></option>
					             <?php   }
					                        }
					                }
					                                                } ?>
					        </select>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Munciple :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
 <div class="field_input_zone field_input_string">
 	<select name="munciple" id="nsite_munciple"  style="width: 223px;">
					            <option>Select Munciple</option>
					            <?php  
					             //echo "test"; 
					             $query = "SELECT * FROM ntsitemunciple WHERE province_id='".$site['province']."' AND is_active = 1";
					                        if($query!=''){
					        $result = mysqli_query($conf, $query);

					        if ($result) {
					            if(mysqli_num_rows($result)>0){
					                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
					            <option value="<?php echo $row['munciple_id'] ?>" <?php echo ($row['munciple_id']==$site['munciple'])? "selected='selected'":"" ?>><?php echo $row['munciple'] ?></option>
					             <?php   }
					                        }
					                }
					                                                } ?>
					        </select>
						           
					           
					            </div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Locality :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
	<select name="locality" id="site_locality"  style="width: 223px;">
					            <option>Select Locality</option>
					            <?php  
					             //echo "test"; 
					             $query = "SELECT * FROM nplocation WHERE munciple_id = '".$site['munciple']."' AND is_active = 1";
					                        if($query!=''){
					        $result = mysqli_query($conf, $query);
					        if ($result) {

					            if(mysqli_num_rows($result)>0){
					                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
					            <option value="<?php echo $row['locationid'] ?>" <?php echo ($row['locationid']==$site['locality'])? "selected='selected'":"" ?>><?php echo $row['locationname'] ?></option>
					             <?php   }
					                        }
					                }
					                                                } ?>
					        </select>

	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Lattidude :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left">
	<input type="text" name="lat" id="lat" value="<?php echo $site['lat'] ?>" style="width:225px;">

</span>
	</div>
</div>
</div></div></div>
</div>
</div>

<div class="field_container field_small" style="margin-bottom: 27px;">
<div class="field_label label"><span title="">Longitude :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left">
	<input type="text" name="lng" id="lng" value="<?php echo $site['lng'] ?>" style="width:225px;">
</span>	
	</div>
</div>
</div></div></div>
</div>
</div>
</fieldset>


<fieldset>
<legend>Planning</legend>
<div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title="">Phase :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
	<span style="float:left;"><?php echo $phase ?></span>
</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Service Date :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;">
	<input type="date" name="service_date" id="service_date" value="<?php echo date('Y-m-d',strtotime($site['service_date'])) ?>" style="width:225px;">
		
											    	</span>
	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Stage : </span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;"> <?php echo $stage; ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Sub Stage :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;"><?php echo $subStage; ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title="">Start Date :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;">
	<input type="date" name="start_date" id="start_date" value="<?php echo date('Y-m-d',strtotime($site['start_date'])) ?>" style="width:225px;">
										    		</span>
	</div>
</div>
</div></div></div>
</div>
</div>

<div class="field_container field_small">
<div class="field_label label"><span title="">End Date :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;">
	<input type="date" name="end_date" id="end_date" value="<?php echo date('Y-m-d',strtotime($site['end_date'])) ?>" style="width:225px;">
									    		</span>
	</div>
</div>
</div></div></div>
</div>
</div>
</fieldset>

</td>

</tr>
</tbody>
</table>
<!-- <button type="button" class="action cancel">Cancel</button> -->
<button type="button" class="action cancel" onclick=""><a href="https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite">Cancel</a></button>
<input type="submit" class="updateSite activsubtn modifysitebtn" value="Update">									    		
</div>


</form>

</div>
    <br><br><br><br>
</div>
<!-- </div> -->

<script type="text/javascript">
	$('.siteAdd').submit(function(event) {
		event.preventDefault();
		$.ajax({
			url: '../application/siteDetails.php',
			data: $(this).serialize(),
			type: 'POST',
			dataType: 'json',
			success: function(res){
				console.log(res);
				if(res.flag){
					alert('Site details updated successfully');
					location.replace("https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite");
				}
			}
		});
	});
</script>
<script type="text/javascript">
            $(document).ready(function () {
                $('#nsite_province').on('change', function () {
                  $('#nsite_munciple').find('option').not(':first').remove();
                    var province_id = $(this).val();
                    //alert(province_id);
                   // alert(province_id);
                    if (province_id) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajaxdropdown.php',
                            dataType: "html",
                            data: JSON.stringify({'province_id': province_id}),
                            success: function (html) {
                              //console.log(html);
                              $('#nsite_munciple').append(html);
                                //$('#site_munciplesss').html(html);
                                $('#tehesil1').html('<option value="">Select District first</option>');
                            }
                        });
                    } else {
                        $('#district1').html('<option value="">Select country first</option>');
                        $('#tehesil1').html('<option value="">Select state first</option>');
                    }
                });
            });
        </script>

        <script>
            $(document).ready(function () {
                $('#nsite_munciple').on('change', function () {
                	  $('#site_locality').find('option').not(':first').remove();
                    var munciple_id = $(this).val();
                    //alert(munciple_id);
                    if (munciple_id) {
                      $.ajax({
                            type: 'POST',
                            url: 'ajaxdropdown.php',
                            dataType: "html",
                            data: JSON.stringify({'munciple_id': munciple_id}),
                            success: function (html) {
                              //console.log(html);
                              $('#site_locality').append(html);
                                $('#city1').html('<option value="">Select Munciple first</option>');
                            }
                        });
                    } else {
                        $('#tehesil1').html('<option value="">Select country first</option>');
                        $('#city1').html('<option value="">Select state first</option>');
                    }
                });
            });
        </script>


<?php
	}else if(isset($_POST['site_id'])){

		$data = array('flag'=>FALSE);
		$query1 = "UPDATE ntsites SET `site_name`='".$_POST['site_name']."',`province`='".$_POST['province']."',`munciple`='".$_POST['munciple']."',`locality`='".$_POST['locality']."',`lat`='".$_POST['lat']."',`lng`='".$_POST['lng']."',`site_code`='".$_POST['site_code']."',vendor='".$_POST['site_vendor']."',`responsible_area`='".$_POST['site_responsible']."',`priority`='".$_POST['site_priority']."',`priority_comment`='".$_POST['priority_comment']."',`element_type`='".$_POST['site_element_type']."',`model`='".$_POST['site_model']."',`msc`='".$_POST['site_msc']."',`mgw`='".$_POST['site_mgw']."',`bsc`='".$_POST['site_bsc']."',`phase`='".$_POST['site_phase']."',`service_date`='".$_POST['service_date']."',`stage`='".$_POST['site_stage']."',`sub_stage`='".$_POST['site_sub_stage']."',`start_date`='".$_POST['start_date']."',`end_date`='".$_POST['end_date']."' WHERE `site_id`=".$_POST['site_id'];
		
		$result1 = mysqli_query($conf, $query1);
		if($result1){

			$query2 = "DELETE FROM ntsitenetwork WHERE `site_id`=".$_POST['site_id'];
			$result2 = mysqli_query($conf, $query2);
			if(is_array($result2)){
				foreach ($_POST['network'] as $key => $value) {
					$query3 = "INSERT INTO ntsitenetwork VALUES ('',".$_POST['site_id'].",'".$value."','".date('Y-m-d H:i:s')."',1)";
					$result3 = mysqli_query($conf, $query3);					
				}
			}
			$data['flag'] = TRUE;
		}else{
			echo mysqli_error($conf);
		}
		echo json_encode($data);
	}
?>