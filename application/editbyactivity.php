<style type="text/css">
    .field_container > div.field_label {
width: 23px;
    }
	 h1{display: none;}
	.field_container > div.field_label {
		width: 23px;
    }
	
	/* tab 2 style */
	table th , table td{text-align: left;}table tr:nth-child(even){background: #00000000;}
	th {background: #e2e2e2;color: #422462;}.pagination {margin: 0;}.pagination li:hover{cursor: pointer;}
	div#example_info {text-align: right;}.pager li>a, .pager li>span {padding: 2px 14px;}
	.previous, .next{font-size: 12px;color: #696969;margin-right: 7px;cursor: pointer;}
	div#activity {border: 1px solid #cccccc;border-radius: 3px;}
	.odd.even td {padding: 2px;}table.listResults td {padding: 2px;}
	table#siteTbl {width: 100%!important;}
	.table-responsive #siteTbl_wrapper #siteTbl_length label{font-size:12px !important;color: #696969;}
	.table-responsive #siteTbl_wrapper #siteTbl_filter label{font-size:12px !important;color: #696969;}
	div#siteTbl_info {font-size: 12px;color: #696969;}
	.dataTables_paginate .paging_simple_numbers{padding-bottom:10px;}
</style>
<script language="javascript" type="text/javascript">
    $(document).ready(function() {
        $("#tabs").tabs();
        $("#tabs > ul").bind("tabsshow", function(event, ui) { 
            window.location.hash = ui.tab.hash;
        });
    });
</script>
<?php /********** Province ***********/
include('../webservices/wbdb.php');  

$postData = file_get_contents("php://input");
$jsonData = json_decode($postData,TRUE);
?>  





<div class="ui-layout-content" style="overflow: auto; position: relative; visibility: visible;">
    <h2 style="font-family: Tahoma, Verdana, Arial, Helvetica!important;
		color: #422462!important;font-weight: bold!important;font-size: 12pt!important;">
		<img src="https://nt3.nectarinfotel.com/images/activityview.png" style="vertical-align:middle;width: 32px;">&nbsp;<?php echo ($jsonData['language']=='PT BR')? 'Modificar informações de NDR':'Modify NDR Information' ?></h2>

<form id="form_add_activity" method="post">
	<div class="wizContainer">
		<!--<form action="../pages/updateactivity.php" method="post" id="form_add_activity"><p></p>-->
		
				<button type="button" class="action cancel" onclick="window.location.href='https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity'"><?php echo ($jsonData['language']=='PT BR')? 'Cancelar':'Cancel' ?></button>&nbsp;&nbsp;&nbsp;&nbsp;
				<button type="submit" class="action"><span><?php echo ($jsonData['language']=='PT BR')? 'Aplique':'Apply' ?></span></button>
				
			<div id="tabs">
				<ul>
					<li><a href="#tabs-001"><?php echo ($jsonData['language']=='PT BR')? 'Atividade':'Activity' ?></a></li>
					<li><a href="#tabs-002"><?php echo ($jsonData['language']=='PT BR')? 'Site afetado':'Affected Site' ?></a></li>
				</ul>


				<!-- ************* Tab 2 Start ***********-->
			
            <div id="tabs-002">
            <div id="linkedset_sites_list">
				<table class="listResults siteTbl" id="siteTbl">
					<thead>
					<tr>
					<th title="Select All / Deselect All">
					<input class="select_all" onclick="CheckAll('#linkedset_sites_list .selection', this.checked);" 
					type="checkbox" value="1" ></th>
					<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Código do site':'Site Code' ?></th>
					<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Nome do site':'Site Name' ?></th>
					<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?></th>
					<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Área Responsável':'Responsible Area' ?></th>
					<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Data de criação':'Created Date' ?></th>
					</tr>
					</thead>
					<tbody id="siteTBody">
					<?php
					$addedSites = array();
					$q1 = "SELECT S.site_id,S.site_name,S.responsible_area,S.created_date,S.site_code,P.province FROM ntsites S LEFT JOIN ntsiteprovince P ON P.province_id=S.province LEFT JOIN ntactivitysite ACSI ON ACSI.site_id = S.site_id WHERE ACSI.activity_id=".$_GET['id']." AND ACSI.is_active = 1";
					$result1 = $conf->query($q1);
					if($result1){
					if($result1->num_rows>0){
						while($aDBInfo = mysqli_fetch_array($result1,MYSQLI_ASSOC)){
							array_push($addedSites, $aDBInfo['site_id']);
							echo "<tr class='tr_".$aDBInfo['site_id']."'><td><input class=\"selection sitemaster\" data-remote-id=\"".$aDBInfo['site_id']."\" data-link-id=\"\" 
							data-unique-id=\"".$i."\" type=\"checkbox\" value=\"".$aDBInfo['site_id']."\" name=\"removesites[]\"> 
							<input type='hidden' name='sites[]' value='".$aDBInfo['site_id']."'> </td>
							<td><a href='https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=NewCI&c[feature]=SiteInformation&id=".$aDBInfo['site_id']."' target='_change' class='siteDetails' id='".$aDBInfo['site_id']."'>".$aDBInfo['site_code']."</td>
							<td>".utf8_encode($aDBInfo['site_name'])."</td>
							<td>".utf8_encode($aDBInfo['province'])."</td>
							<td>".utf8_encode($aDBInfo['responsible_area'])."</td>
							<td>".date('d M Y (h:i a)',strtotime($aDBInfo['created_date']))."</td></tr>";

							array_push($addedSites, $aDBInfo['site_id']);
						}
					}else{
					echo "<tr><td colspan='6' style='text-align: center;'>The list is empty, use the \"Add...\" button to add aggregate sites.</td></tr>";
					}
					}
					?>
					</tbody>
				</table>
			</div>
				<!--</div>&nbsp;&nbsp;&nbsp;-->
				<input id='affectedsite_list_btnRemove' type='button' value='<?php echo ($jsonData['language']=='PT BR')? 'Remover objetos selecionados':'Remove selected objects' ?>' disabled='disabled'>&nbsp;&nbsp;&nbsp;
				<input id="affsite_list_btnAdd" type="button" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar site afetado...':'Add Affected Site...' ?>">
				

				<div id="addAffectedComponentDialog" class="modal">
				<h1 style="display: contents!important;"><?php echo ($jsonData['language']=='PT BR')? 'Adicionar site agregado':'Add Affected Site' ?> </h1>
				<div id="linkedset_main_sites_list">
				<input type="hidden" id="2_main_sites_list" value="[]">
				<input type="hidden" name="attr_main_sites_list" value="">
				<table class="listResults siteTbl" id="siteTblAdd" style="min-width: 900px;">
				<thead>
				<tr>
				<th title="Select All / Deselect All">
				<input class="select_all aftsitechk" onclick="CheckAll('#linkedset_main_sites_list .selection', this.checked); oWidget2_functionalcis_list.OnSelectChange();" 
				type="checkbox" value="1" >
				</th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Código do site':'Site Code' ?></th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Nome do site':'Site Name' ?></th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?></th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Área Responsável':'Responsible Area' ?></th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Data de criação':'Created Date' ?></th>
				</tr>
				</thead>
				<tbody id="mainSiteTBody">
				<?php

				$q2 = "SELECT * FROM ntsites LEFT JOIN ntsiteprovince ON ntsites.province=ntsiteprovince.province_id WHERE ntsites.is_active = 1 ORDER BY ntsites.created_date DESC";
				//$q2 = "SELECT * FROM `ntactivitysite` LEFT JOIN `ntsiteprovince` ON ntactivitysite.province = ntsiteprovince.province_id WHERE ntactivitysite.is_active = 1 ORDER BY ntactivitysite.created_date DESC";
				$result2 = $conf->query($q2);
				if($result2){
				if($result2->num_rows>0){
				$i = 0;
				while ($aDBInfo = mysqli_fetch_array($result2,MYSQLI_ASSOC)) {
				//$selected = "";
				$selected= in_array($aDBInfo['site_id'],$addedSites)? 'selected="selected"':'';
				echo "<tr>
				<td>
				<input class=\"selection aftsitechk\" data-remote-id=\"".$aDBInfo['site_id']."\" data-link-id=\"\" data-unique-id=\"".$i."\" type=\"checkbox\" value=\"".$aDBInfo['site_id']."\" name=\"allsites[]\" ".$selected."> </td>
				<td><a href='https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=NewCI&c[feature]=SiteInformation&id=".$aDBInfo['site_id']."' target='_change' class='siteDetails' id='".$aDBInfo['site_id']."'>".$aDBInfo['site_code']."</td>
				<td>".utf8_encode($aDBInfo['site_name'])."</td>
				<td>".utf8_encode($aDBInfo['province'])."</td>
				<td>".utf8_encode($aDBInfo['responsible_area'])."</td>
				<td>".date('d M Y (h:i a)',strtotime($aDBInfo['created_date']))."</td>
				</tr>";
				$i++;
				}
				}else{
				echo "<tr><td colspan='6' style='text-align: center;'>The list is empty, create new sites.</td></tr>";
				}
				}
				?>
				</tbody>
				</table>
				</div>
				<input type="button" value="Cancel" onclick="$('#addAffectedComponentDialog').dialog('close');">
				<input id="btn_ok_aftsite_list" disabled="disabled" type="button" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>">
				</div>

			</div>
			<!-- ***************** Tab 2 End ************** -->
			
				<!-- ***************** Tab 1 Start ************** -->
				<div id="tabs-001">
					<div class="ui-layout-content" style="overflow: auto; position: relative; height: auto; visibility: visible;">
						<div class="formactivity" style="border: none;padding: 10px;">
							<div class="table-responsive">
								<?php
									$query = "SELECT * FROM npactivity WHERE activityid ='".$_GET['id']."'";
									if($query!=''){
									$result = mysqli_query($conf, $query);
									if ($result) {
									if(mysqli_num_rows($result)>0){
											
										while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
				<table class="table">
									<tr>

				<td colspan="3" style="text-align: left;width: 100%">
				<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="description">
				<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Descrição':'Description' ?> :</span></div>
				
				<!-- <div class="field_data"> -->
				<div class="field_value">
					<input type="hidden" name="activityid" id="activityid" value="<?php echo $rows['activityid']; ?>">
					<input name="description" type="text" value="<?php echo $rows['description']; ?>" required="required" style="width: 95%;">
					<img src="../images/validation_error.png" style="vertical-align:top;float: right;" title="Please specify a value">
				<!-- <div id="field_2_title" class="field_value_container">
					
				<div class="attribute-edit" data-attcode="title"> -->
					
					
				<!-- <div class="field_input_zone field_input_string">
				<span style="float:left"></span>
				</div> -->
				
				<!-- </div>
				</div> -->
				</div>
				<!-- </div> -->
				</div>
				</td>

				
									</tr>
									<tr>
									<td style="width: 30%;">
									<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="privince">
									<!--<div class="field_label label"><span title="">Province :</span></div>-->
									<div class="field_label label"><label><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?> :</label></div>
									<div class="field_data">
									<div class="field_value">
									<!-- <div id="field_2_title" class="field_value_container">
									<div class="attribute-edit" data-attcode="title"> -->
									<div class="field_input_zone field_input_string">
								
		<select name="province" style="width: 80%;margin: 4px;" id="nsite_province">
									<!--            <option>select province</option>-->
									<?php  
										$query = "SELECT * FROM ntsiteprovince WHERE is_active = 1 ORDER BY province_id DESC";
										if($query!=''){
										$result = mysqli_query($conf, $query);
										if ($result) {
											if(mysqli_num_rows($result)>0){
												while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
													<option <?php if($rows['province']===$row['province_id']){ echo 'selected'; } ?> value="<?php echo $row['province_id'] ?>"><?php echo utf8_encode($row['province']) ?></option>
									<?php   
												}
											  }
											}
									  	}
									?>
									</select>
									<!-- <span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;"> -->
									<img src="../images/validation_error.png" style="vertical-align:top;float: right;" title="Please specify a value">
	
									<!-- </span> -->
									</div>
									<!-- </div>
									</div> -->
									</div>
									</div>
									</div>

									</td>
									<td style="width: 30%;">
									<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="municipal">
									<!--<div class="field_label label"><span title="">Municipal :</span></div>-->
									<div class="field_label label"><label><?php echo ($jsonData['language']=='PT BR')? 'municipal':'Municipal' ?>  :</label></div>
									<div class="field_data">
									<div class="field_value">
									<div id="field_2_title" class="field_value_container">
									<div class="attribute-edit" data-attcode="title">
									<div class="field_input_zone field_input_string">
								
									<select name="munciple" style="width: 80%;margin: 4px;" id="nsite_munciple">
									<!--            <option>select province</option>-->
										 <?php  $query = "SELECT * FROM ntsitemunciple WHERE province_id='".$rows['province']."' AND is_active = 1 ORDER BY munciple_id DESC";
													if($query!=''){
									$result = mysqli_query($conf, $query);
									if ($result) {
									//echo $query;
										if(mysqli_num_rows($result)>0){
										while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
											<option <?php if($rows['munciple']===$row['munciple_id']){ echo 'selected'; } ?> value="<?php echo $row['munciple_id'] ?>"><?php echo utf8_encode($row['munciple']) ?></option>
										 <?php   
												}
											  }
											}
										  }
										?>
									</select>
									<!-- <span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;"> -->
									<img src="../images/validation_error.png" style="vertical-align:top;float: right;" title="Please specify a value">
									<!-- </span> -->
									</div>
									</div>
									</div>
									</div>
									</div>
									</div>

									</td>
									<td style="width: 30%;">
									<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="location">
									<!--<div class="field_label label"><span title="">Location :</span></div>-->
									<div class="field_label label"><label><?php echo ($jsonData['language']=='PT BR')? 'Localização':'Location' ?> :</label></div>
									<div class="field_data">
									<div class="field_value">
									<div id="field_2_title" class="field_value_container">
									<div class="attribute-edit" data-attcode="title">
									<div class="field_input_zone field_input_string">
								
									<select name="location" style="width: 80%;margin: 4px;" id="site_locality">
									<option><?php echo ($jsonData['language']=='PT BR')? 'Selecionar local':'Select Location' ?></option>
									<?php  $query = "SELECT * FROM nplocation where munciple_id='".$rows['munciple']."' AND is_active = 1";
													if($query!=''){
									$result = mysqli_query($conf, $query);
									if ($result) {
										if(mysqli_num_rows($result)>0){
										while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
											<option <?php if($rows['location']===$row['locationid']){ echo 'selected'; } ?> value="<?php echo $row['locationid'] ?>"><?php echo utf8_encode($row['locationname']) ?></option>
										 <?php   
												  }
												}
											  }
											}
										?>
									</select>
									<!-- <span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;"> -->
									<img src="../images/validation_error.png" style="vertical-align:top;float: right;" title="Please specify a value">
									<!-- </span> -->
									</div>
									</div>
									</div>
									</div>
									</div>
									</div>

									</td>
								
									</tr>
									<tr>

									<td style="width: 30%;text-align: left;">
										<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="reason">
										<div class="field_label label"><label><?php echo ($jsonData['language']=='PT BR')? 'Razão':'Reason' ?> :</label></div>
											<div class="field_data">
												<div class="field_value">
													<div class="field_input_zone field_input_string">
														<select name="selectedreason" style="width: 80%;margin: 4px;"  id="selectedreason">
															<option value=""><?php echo ($jsonData['language']=='PT BR')? 'Selecionar motivo':'Select Reason' ?></option>
															<?php 
																$query = CMDBSource::QueryToArray("Select * from  npreason GROUP BY npreason.npreasonid ORDER BY npreason.npreasonid  DESC");
																if(!empty($query)){
																	foreach ($query as $row) {
																		echo 'ss: '.$rows['reason'];
																?>
																	<option value="<?php echo $row['npreasonid'] ?>" <?php echo ($rows['reason']==$row['reason_name'])? "selected='selected'":"" ?> > <?php echo $row['reason_name']; ?></option>
																<?php  
																	   }
																	}
																?>
														</select>
													
													</div>
												</div>
											</div>
										</div>
									</td>		
									<td colspan="3">
									<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="our_reason">
									<div class="field_label label" style="width: 10%;"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Outra razão':'Other Reason' ?> :</span></div>
									
									<div class="field_data">
									<div class="field_value">
									<div id="field_2_title" class="field_value_container">
									<div class="attribute-edit" data-attcode="title">
									<div class="field_input_zone field_input_string">
									<span style="float:left">
									<textarea name="reason" style="width: 85%;border: 1px solid #e1e1e1;margin-left: 3px;" value="<?php echo $rows['reason']; ?>"><?php echo htmlspecialchars($rows['reason']);?></textarea>
									</span> 
									</div>
									</div>
									</div>
									</div>
									</div>
									</div>

									</td> </tr>
									<tr>
					 <td style="width: 30%;text-align: left;">
				  	<div class="field_container field_small" style="border-bottom:none;">
<div class="field_label label"><span title="">Combustivel Encontrado (Litros) :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<input name="Encontrado" value="<?php echo $rows['fuel_found']; ?>" min="0" onkeypress="return event.charCode != 45" required="" style="width:80%;">
</div></div></div>
</div>
</div>
				  </td>
					<td style="width: 30%;text-align: left;">
				  	<div class="field_container field_small" style="border-bottom:none;">
<div class="field_label label"><span title="">Combustivel Abastecido (Litros) :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<input name="Abastecido" value="<?php echo $rows['fuel_filled']; ?>" min="0" onkeypress="return event.charCode != 45" required="" style="width:80%;">
</div></div></div>
</div>
</div>
				  </td>
				  <td>
				  	
				  </td>

				</tr>
									<tr>
									<td>
									</td>
									<td colspan="2">
									<div class="field_container field_small" style="border-bottom: none!important;height: 80px;" data-attcode="access_type">
									<div class="field_label label" style="width: 22%;"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Tipo de acesso':'Access Type' ?> : </span></div>
									
									<div class="field_data">
									<div class="attribute-edit" data-attcode="title">
									<div class="field_input_zone field_input_string" style="margin-top: 30px;">
									<span style="float:left;">
									<input type="radio" <?php if($rows['accesstype']==="External"){ echo 'checked'; } ?> name="accesstype" id="accesstype" value="External" class="btn1"><span><?php echo ($jsonData['language']=='PT BR')? 'Externa':'External' ?></span>
									<input type="radio" <?php if($rows['accesstype']==="Internal"){ echo 'checked'; } ?> name="accesstype" id="accesstype" value="Internal" class="btn2"><span><?php echo ($jsonData['language']=='PT BR')? 'Interna':'Internal' ?></span>

									</span>
									</div>
									</div>

									</div>
									</div>

									</td>
									<script>
									$(document).ready(function(){
										var internal="Internal";
										var external="External";
										if(internal=="<?php echo $rows['accesstype']; ?>"){
									   $(".internals").show();
										$(".externals").hide();
										}
										if(external=="<?php echo $rows['accesstype']; ?>"){
										$(".externals").show();
										$(".internals").hide();
										
										}
									  $(".btn1").click(function(){
										$(".externals").show();
										$(".internals").hide();
									  });
									  $(".btn2").click(function(){
										$(".internals").show();
										$(".externals").hide();
									  });
									});
								</script>
								</tr>
								<td style="width: 30%;" class="externals">
								<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="provider">
								<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'fornecedor':'Provider' ?>:</span></div>
								
								<div class="field_data">
								<div class="field_value">
								<!--  <div id="field_2_title" class="field_value_container">
									<div class="attribute-edit" data-attcode="title"> -->
								<div class="field_input_zone field_input_string">
								  
										<select name="provider" style="width: 80%;margin: 4px;" id="">
										<option value=""><?php echo ($jsonData['language']=='PT BR')? 'selecionar provedor':'select provider' ?></option>
										<?php  $query = "SELECT (ntservice.id) as id,ntservice.name FROM ntservice join ntlnkprovidercontracttoservice on ntservice.id=ntlnkprovidercontracttoservice.service_id GROUP BY ntservice.name ORDER BY ntservice.id DESC";
													if($query!=''){
									$result = mysqli_query($conf, $query);
									if ($result) {
									//echo $query;
										if(mysqli_num_rows($result)>0){
											//print_r(mysqli_fetch_all($result, MYSQLI_ASSOC));
											//$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_all($result, MYSQLI_ASSOC));
											/**/    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
										<option <?php if($rows['provider']===$row['id']){ echo 'selected'; } ?> value="<?php echo $row['id'] ?>"><?php echo utf8_encode($row['name']) ?></option>
										 <?php   }
													}
											}
																			} ?>
									</select>
								   </div>
									<!-- </div>
								</div> -->
								</div>
								</div>
								</div>

								</td>
								<td style="width: 30%;" class="externals">
								<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="service">
								<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'serviço':'Service' ?> :</span></div>
								
								 <div class="field_data">
								<div class="field_value">
								<div id="field_2_title" class="field_value_container">
								<div class="attribute-edit" data-attcode="title">
								<div class="field_input_zone field_input_string">
								<select name="service" style="width: 80%;" id="service">
								<option><?php echo ($jsonData['language']=='PT BR')? 'selecione Serviço':'select Service' ?></option>
									 <?php  $query = "SELECT (ntcontract.id) as id,ntcontract.name FROM ntcontract join  ntlnkprovidercontracttoservice on  ntcontract.id=ntlnkprovidercontracttoservice.providercontract_id where ntcontract.finalclass='ProviderContract' GROUP BY ntcontract.id ORDER BY ntcontract.id  DESC";
													if($query!=''){
									$result = mysqli_query($conf, $query);
									if ($result) {
								//echo $query;
										if(mysqli_num_rows($result)>0){
											//print_r(mysqli_fetch_all($result, MYSQLI_ASSOC));
											//$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_all($result, MYSQLI_ASSOC));
											/**/    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
									   <option <?php if($rows['service']===$row['id']){ echo 'selected'; } ?> value="<?php echo $row['id'] ?>"><?php echo utf8_encode($row['name']) ?></option>
										 <?php   }
													}
											}
																			} ?>
								</select> 
								</div>
								</div>
								</div>
								</div>
								</div>
								</div>

								</td>


								<td style="width: 30%;" class="externals">
								<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="employee">
								<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Aberto por':'Responsible Person' ?> :</span></div>
								
								<div class="field_data">
								<div class="field_value">
								<div id="field_2_title" class="field_value_container">
									<div class="attribute-edit" data-attcode="title">
										<div class="field_input_zone field_input_string">
										<select name="extemployee" style="width: 80%;">
										<option value=""><?php echo ($jsonData['language']=='PT BR')? 'Selecionar Pessoa Responsável':'Select Responsible Person' ?></option>
										 <?php  $query = "SELECT * FROM ntcontact join ntperson on ntcontact.id=ntperson.id AND ntcontact.finalclass ='Person' ORDER BY ntperson.id DESC";
													if($query!=''){
									$result = mysqli_query($conf, $query);
									if ($result) {
							//echo $query;
										if(mysqli_num_rows($result)>0){
											//print_r(mysqli_fetch_all($result, MYSQLI_ASSOC));
											//$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_all($result, MYSQLI_ASSOC));
											/**/    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
										<option <?php if($rows['extemployee']===$row['id']){ echo 'selected'; } ?> value="<?php echo $row['id'] ?>">
										<?php echo utf8_encode($row['first_name'].' '.$row['name']) ?></option>
										 <?php   }
													}
											}
																			} ?>
									</select> 
									<!-- <span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;"> -->
									<img src="../images/validation_error.png" style="vertical-align:top;float: right;" title="Please specify a value">
									<!-- </span> -->
									 </div>
									</div>
								</div>
								</div>
								</div>
								</div>

								</td>  
								</tr>
								<tr>
								<td colspan="3">
									<br/>
								</td>
								</tr>
								<tr>
								<td style="width: 30%;" class="externals">
								<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="reported_to">
								<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Reportado para':'Reported To' ?> :</span>
								</div>
								<div class="field_data">
								<div class="field_value">
								<div id="field_2_title" class="field_value_container">
									<div class="attribute-edit" data-attcode="title">
										<div class="field_input_zone field_input_string">
										<select name="extreportedto" style="width: 80%;">
										<option value=""><?php echo ($jsonData['language']=='PT BR')? 'Selecione Reported TO':'Select Reported TO' ?></option>
										 <?php  $query = "SELECT * FROM ntcontact join ntperson on ntcontact.id=ntperson.id AND ntcontact.finalclass ='Person' ORDER BY ntperson.id DESC";
													if($query!=''){
									$result = mysqli_query($conf, $query);
									if ($result) {
							//echo $query;
										if(mysqli_num_rows($result)>0){
											//print_r(mysqli_fetch_all($result, MYSQLI_ASSOC));
											//$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_all($result, MYSQLI_ASSOC));
											/**/    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
										   
										<option <?php if($rows['extreportedto']===$row['id']){ echo 'selected'; } ?> value="<?php echo $row['id'] ?>">
										<?php echo utf8_encode($row['first_name'].' '.$row['name']) ?></option>
										 <?php   }
													}
											}
																			} ?>
									</select> 
									<!-- <span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;"> -->
									<img src="../images/validation_error.png" style="vertical-align:top;float:right;" title="Please specify a value">
									<!-- </span> -->
									 </div>
									</div>
								</div>
								</div>
								</div>
								</div>

								</td>  
								</tr>
								<tr>
								<td style="width: 30%;" class="internals">
								<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="area">
								<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Área':'Area' ?> :</span></div>
								<div class="field_data">
								<div class="field_value">
								<div id="field_2_title" class="field_value_container">
									<div class="attribute-edit" data-attcode="title">
										<div class="field_input_zone field_input_string">
										<select name="movicelarea" style="margin: 4px;width: 80%;" id="selectma1">
										<option><?php echo ($jsonData['language']=='PT BR')? 'selecione a área':'select area' ?></option>
										 <?php  $query = "SELECT * FROM ntorganization ORDER BY id DESC";
													if($query!=''){
									$result = mysqli_query($conf, $query);
									if ($result) {
							//echo $query;
										if(mysqli_num_rows($result)>0){
											//print_r(mysqli_fetch_all($result, MYSQLI_ASSOC));
											//$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_all($result, MYSQLI_ASSOC));
											/**/    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
										<option <?php if($rows['movicelarea']===$row['id']){ echo 'selected'; } ?> value="<?php echo $row['id'] ?>"><?php echo utf8_encode($row['name']) ?></option>
										 <?php   }
													}
											}
																			} ?>
									</select> 
								  </div>
									</div>
								</div>
								</div>
									</div>
								</div>

								</td>
								<td style="width: 30%;" class="internals">
								<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="employee">
								<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Aberto por':'Responsible Person' ?> :</span></div>
								
								<div class="field_data">
								<div class="field_value">
								<div id="field_2_title" class="field_value_container">
									<div class="attribute-edit" data-attcode="title">
										<div class="field_input_zone field_input_string">
										<select name="employee" style="width: 80%;" id="inemployee">
										<option value=""><?php echo ($jsonData['language']=='PT BR')? 'Selecionar Pessoa Responsável':'Select Responsible Person' ?></option>
										 <?php  $query = "SELECT (ntcontact.id) as id,(ntcontact.name) as name,(ntperson.first_name) as first_name from ntcontact join ntperson on ntcontact.id=ntperson.id AND ntcontact.finalclass ='Person' where ntcontact.org_id='".$rows['movicelarea']."' ORDER BY ntperson.id DESC";

													if($query!=''){
									$result = mysqli_query($conf, $query);
									
									if ($result) {
							//echo $query;
										if(mysqli_num_rows($result)>0){
											//print_r(mysqli_fetch_all($result, MYSQLI_ASSOC));
											//$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_all($result, MYSQLI_ASSOC));
											/**/    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
										<option <?php if($rows['employee']===$row['id']){ echo 'selected'; } ?> value="<?php echo $row['id'] ?>"><?php echo utf8_encode($row['first_name'].' '.$row['name']) ?></option>
										 <?php   }
													}
											}
																			} ?>
									</select> 
									<!-- <span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;"> -->
									<img src="../images/validation_error.png" style="vertical-align:top;float: right;" title="Please specify a value">
									<!-- </span> -->
									 </div>
									</div>
								</div>
								</div>
								</div>
								</div>

								</td>  
								<td style="width: 30%;" class="internals">
								<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="reported_to">
								<!--<div class="field_label label"><span title="">Reported To :</span></div>-->
								<div class="field_label label"><label><?php echo ($jsonData['language']=='PT BR')? 'Reportado para':'Reported To' ?> :</label></div>
								<div class="field_data">
								<div class="field_value">
								<div id="field_2_title" class="field_value_container">
									<div class="attribute-edit" data-attcode="title">
										<div class="field_input_zone field_input_string">
										<select name="reportedto" style="width: 80%;">
										<option value=""><?php echo ($jsonData['language']=='PT BR')? 'Selecione Reportado para':'Select Reported To' ?></option>
										 <?php  $query = "SELECT * FROM ntcontact join ntperson on ntcontact.id=ntperson.id AND ntcontact.finalclass ='Person' ORDER BY ntperson.id DESC";
													if($query!=''){
									$result = mysqli_query($conf, $query);
									if ($result) {
							//echo $query;
										if(mysqli_num_rows($result)>0){
											//print_r(mysqli_fetch_all($result, MYSQLI_ASSOC));
											//$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_all($result, MYSQLI_ASSOC));
											/**/    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
										<option <?php if($rows['reportedto']===$row['id']){ echo 'selected'; } ?> value="<?php echo $row['id'] ?>"><?php echo utf8_encode($row['first_name'].' '.$row['name']) ?></option>
										 <?php   }
													}
											}
																			} ?>
									</select> 
									<!-- <span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;"> -->
									<img src="../images/validation_error.png" style="vertical-align:top;float: right;" title="Please specify a value">
									<!-- </span> -->
									 </div>
									</div>
								</div>
								</div>
								</div>
								</div>

								</td>  
								<td style="width: 30%;">
									<br/>
								</td> 
								</tr>
								</tr>
								</table>
								  
									<?php   
											}
									      }
										}
									  }
									?>
									<!-- <button type="button" class="action cancel" style="margin-top: 23px;"><a href="https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity">Cancel</a></button>
									 <input type="submit" class="createSite activsubtn" value="Update" > -->
							
						</div>
								<br><br><br><br>
       
							<!--    <input type="button" class="createSite" value="Create" style="padding: 6px 26px 6px 26px;background-color: #F17422;color: #ffffff;cursor: pointer;float:right;">-->
                    </div>
						<!--    <input type="button" class="createSite" value="Create" style="padding: 6px 26px 6px 26px;background-color: #F17422;color: #ffffff;cursor: pointer;float:right;">-->
				</div>
			
	
			<!-- ***************** Tab 1 End ************** -->	
			</div> <!-- tabs div close -->
		</form>	<!--- Main Form Closed --->
	</div><!-- wizcontainer close --->
	</form> <!---update activity form closed ---->
</div><!-- UI-layoutd iv close -->	

<!---************************* Update activity Acript ***********************--->
<script>
function resetforms() {
  document.getElementById("form_add_activity").reset();
}
</script>
<script type="text/javascript">
	$('#form_add_activity').submit(function(event) {
		event.preventDefault();
		$.ajax({
			url: 'updateactivity.php',
			data: $(this).serialize(),
			type: 'POST',
			success: function(res){
				console.log(res);
				if(res){
					alert('NDR Details Updated Successfully');
					window.location.href = "https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=viewactivity&id=<?php echo $_GET['id'] ?>";
				}else{
					alert('Unale to update NDR');
				}
			},
			error:function(xhr){
				console.log(xhr);
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
                              $('#nsite_munciple').html(html);
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
                              $('#site_locality').html(html);
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
        <script type="text/javascript">
            $(document).ready(function () {
                $('#provider').on('change', function () {
                  $('#service').find('option').not(':first').remove();
                  
                  
                    var provider_id = $(this).val();
                   // alert(provider_id);
                    if (provider_id) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajaxdropdown.php',
                            dataType: "html",
                            data: JSON.stringify({'provider_id': provider_id}),
                            success: function (html) {
                              //console.log(html);
                              $('#service').html(html);
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
		
<!--************* End Update Activity Acript ******************-->

<!--***************** Affected site script *************-->

		
<script type="text/javascript">

	$(document).ready(function(){
		$('#siteTbl').datatable();
	});
	
	var affectedSites = new Array();
    <?php 
    if(!empty($addedSites)){
    	foreach($addedSites as $key => $val){ 
	?>
        affectedSites.push('<?php echo $val; ?>');
    <?php 
		}
	} 
	?>

	var addedSiteTemp = [];
	if ( ! $.fn.DataTable.isDataTable( '#siteTblAdd' ) ) {
		  $('#siteTblAdd').DataTable({
			 	"pagingType": "full_numbers",
				"pageLength": 10,
			});
		}
		
	$('#affsite_list_btnAdd').on('click',function(){

		$( "#addAffectedComponentDialog" ).dialog({
			height: 470,
			width: 950
		});

		if ( ! $.fn.DataTable.isDataTable( '#siteTblAdd' ) ) {
		  $('#siteTblAdd').DataTable({
			 	"pagingType": "full_numbers",
				"pageLength": 10,
			});
		}

		$(document).on('change',".aftsitechk",function(){
			var siteid = $(this).val();
			if($(this).is(':checked')){
				affectedSites.push(siteid);
				/*$.ajax({
					url: 'otherFields.php',
					data: {'field':'getChildSites','site_id':siteid},
					type: 'POST',
					dataType: 'json',
					success: function(res){
						console.log(res);
						if(res.flag){
							$.each(res.sites,function(k,v){
								affectedSites.push(v);
							});
						}
					}
				});*/

			}else{
				affectedSites = jQuery.grep(affectedSites, function(value) {
				  return value != siteid;
				});
			}

			if ($(".aftsitechk:checked").length > 0){
			   $("#btn_ok_aftsite_list").removeAttr('disabled');
			}else{
			  $("#btn_ok_aftsite_list").attr('disabled','disabled');
			}
		});

		$("#btn_ok_aftsite_list").on("click",function(){
			addedSiteTemp = [];
			$.ajax({
				url: 'addSiteAttr.php',
				data: {'attr':'getSingleSites','sites':affectedSites},
				type: 'POST',
				//dataType: 'json',
				success: function(res){
					//if(res.flag){
						//$("#siteTBody").html(res.info);
						$("#siteTBody").html(res);
						$('#addAffectedComponentDialog').dialog('close');
						$("#affectedsite_list_btnRemove").attr('disabled','disabled');
					//}
				}
			});
		});

	});

	$(document).on('click',".select_all",function(){
		if ($(".select_all:checked").length > 0){
		   $("#affectedsite_list_btnRemove").removeAttr('disabled');
		   $(".sitemaster").each(function (index, obj) {
		       addedSiteTemp.push($(this).val());
		    });
		}else{
		  addedSiteTemp = [];	
		  $("#affectedsite_list_btnRemove").attr('disabled','disabled');
		}
	});

	$(document).on('click',".sitemaster",function(){
		if ($(".sitemaster:checked").length > 0){
		   $("#affectedsite_list_btnRemove").removeAttr('disabled');
		}else{
		  $("#affectedsite_list_btnRemove").attr('disabled','disabled');
		}
		var siteid = $(this).val();
		if($(this).is(':checked')){
			addedSiteTemp.push(siteid);
		}else{
			addedSiteTemp = jQuery.grep(addedSiteTemp, function(value) {
			  return value != siteid;
			});
		}
	});
	
	$("#affectedsite_list_btnRemove").on('click',function(){
		$.each(addedSiteTemp,function(key,val){
			addedSiteTemp = [];
			$(".select_all").removeAttr("checked");
			$(".tr_"+val).remove();
		})
	});
</script>
 <script type="text/javascript">
            $(document).ready(function () {
                $('#selectma1').on('change', function () {
                  $('#inemployee').find('option').not(':first').remove();
                    var orgid = $(this).val();
					//alert(orgid);
                    if (orgid) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajaxdropdown.php',
                            dataType: "html",
                            data: JSON.stringify({'orgid': orgid}),
                            success: function (html) {
                              //console.log(html);
							  //alert(html);
                              $('#inemployee').html(html);
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
<!--***************** End Affected site script *************-->