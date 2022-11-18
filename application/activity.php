<?php 
include('../webservices/wbdb.php');

$postData = file_get_contents("php://input");
$jsonData = json_decode($postData,TRUE);

?>
<style>
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
th.header {
    padding-right: 7px;
}
h2 {
    color: #422462;
    font-weight: bold;
}
</style>
<!--<script language="javascript" type="text/javascript">
    $(document).ready(function() {
        //$("#tabs").tabs({active: document.tabTest.currentTab.value});
        $("#tabs").tabs();
	$("#tabs > ul").bind("tabsshow", function(event, ui) { 
		window.location.hash = ui.tab.hash;
	})
 
    });
	
</script>-->
<p class="page-header"><img src="https://nt3.nectarinfotel.com/images/activityview.png" style="vertical-align:middle;width: 26px;"> <?php echo ($jsonData['language']=='PT BR')? 'Lista de NDR':'List Of NDR' ?>(Número De Registo)</p>

<!--<div class="ui-layout-content" style="overflow: auto; position: relative; visibility: visible;">
 <h2 style="font-family: Tahoma, Verdana, Arial, Helvetica!important;
		color: #422462!important;font-weight: bold!important;font-size: 12pt!important;">
		<img src="https://nt3.nectarinfotel.com/images/activityview.png" style="vertical-align:middle;width: 32px;">&nbsp;Activity Information</h2>
	<div class="wizContainer">
			<form action="addactivity.php" method="post" id="form_add_activity"><p></p>
			<input type="hidden" name="" value="">
			<button type="button" class="action cancel">Cancel</button>&nbsp;&nbsp;&nbsp;&nbsp;
			<button type="submit" class="action"><span> Create </span></button>
			<button type="submit" name="next_action" value="ev_assign" class="action" style="background-color: #f17422;"><span>Modify</span></button>
			
		<div id="tabs">
				<ul>
					<li><a href="#tabs-01">Activity</a></li>
					<li><a href="#tabs-02">Affected Site</a></li>
					
				</ul>-->
				
			
<!-- ************* Tab 1 Start ***********-->

			<!--<div id="tabs-01">-->
				<div id="activity" class="display_block sf_results_area">
					<table id="datatable_activity" class="datatable nt3-datatable">
						<tbody>
							<tr>
								<td>
									<table style="width:100%;">
										<tbody>
											<tr>
												<td class="menucontainer">								
													<div class="actions_button">
														<a href="https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=addactivity"><?php echo ($jsonData['language']=='PT BR')? 'Nova...':'New...' ?></a>
													</div>
													<button type="button" class="ndrexport" style="color: #fff;cursor: pointer;font-family: Tahoma, sans-serif;font-size: 12px;text-decoration: none;line-height: 22px;display: block;padding: 1px 20px 8px 20px;background-color: #f17422;border-radius: 3px;
														float: right;margin-right: 8px;height: 26px;border: 1px solid #ffff;"><span><?php echo ($jsonData['language']=='PT BR')? 'Export...':'Export...' ?></span></button>
													<div class="actions_button icon_actions_button" title="Refresh"><span class="refresh-button fa fa-refresh" onclick="window.location.reload();"></span></div>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>

					<!--Edited by Priya Activity display ---->
					<div class="table-responsive">
					 
						<table class="table listResults table-striped activtabl1" id="siteTbl">
							<thead>
							<tr> 
								<!-- <th class="header" style="display:none;">
									<?php //echo ($jsonData['language']=='PT BR')? 'Eu iria':'Id' ?>
								</th> -->
								<th class="header">NDR</th>
								<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Descrição':'Description' ?></th>
								<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Site':'Site' ?></th>
								<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?></th>
								<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Municipio':'Municipal' ?></th>
								<!-- <th class="header">Location</th> -->
								<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Motivo':'Reason' ?></th>
								<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Tipo de acesso':'Access Type' ?></th>	
								<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Fuel (Liters)':'Fuel (Liters)' ?></th>	
								<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'fornecedor':'Provider' ?></th>
								<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'serviço':'Service' ?></th>
								<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Área':'Area' ?></th>
								<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Criado por Equipa Responsavel':'Created by Responsible Team' ?></th>  	
								<th class="header" style="width:60px;"><?php echo ($jsonData['language']=='PT BR')? 'Data de criação':'Created Date' ?></th>
								<th class="header" style="width:90px!important;"><?php echo ($jsonData['language']=='PT BR')? 'Açao':'Action' ?></th>
							</tr>
						</thead>
						<tbody>
								<?php	
										/*echo'
											<tr>
											
												<td style="border-bottom: 1px solid #dcdcdc;width: 100px;">
												<a href="https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=viewactivity&id='.$row['activityid'].'">'.$row['actvitycode'].'</a>
												</td>
												<td>'.$row['description'].'</td>
												<td>'.$row['site_name'].'</td>
                                                <td>'.utf8_encode($row['province']).'</td>
												<td>'.utf8_encode($row['munciple']).'</td>
												
												<td>'.utf8_encode($row['reason_name']).'</td>
												<td>'.$row['accesstype'].'</td>
												<td> Found:'.$row['fuel_found'].'</br>Filled:'.$row['fuel_filled'].'</td>
												<td>'.utf8_encode($row['provider']).'</td>
												<td>'.utf8_encode($row['service']).'</td>
												<td>'.utf8_encode($row['intarea']).'</td>
												
												
												<td>'. ($row['accesstype']==='Internal'? utf8_encode($row['first_name'].' '.$row['name']):utf8_encode($row['first_name'].' '.$row['name'])).'</td> 
												
												
												<td>'.date('d M Y (h:i a)',strtotime($row['created_date'])).'</td>
												<td style="border-bottom: 1px solid #dcdcdc;">'.
												($row['result']=='Close'? '<span style="color:red;font-weight:600">'.($jsonData['language']=='PT BR'?'Fechadas':'Closed').'</span>':'<a href="https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=editbyactivity&id='.$row['activityid'].'">'.($jsonData['language']=='PT BR'?'editar':'edit').'</a> | <a href="#" onclick="deleteactivity('.$row['activityid'].')">'.($jsonData['language']=='PT BR'?'Excluir':'Delete').'</a>').'</td>
											</tr>
										';
									}*/
								?>
								</tbody>
					

							</table>
							
					   </div>
					</div>
			<!--</div>-->
					 
<!-- ***************** Tab 1 closed *********************--->
			<div id="modmsc" class="modal">
            	<h2>Export NDR</h2>
<div class="col-md-12">
	<div class="col-md-6">
		<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="provider">
				<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Fornecedor':'Provider' ?> :</span></div>
				<div class="field_data">
				<div class="field_value">
				<div class="field_input_zone field_input_string">
				<select name="provider" style="width: 95%;margin: 4px;"  id="provider">
				<option value=""><?php echo ($jsonData['language']=='PT BR')? 'Selecionar Fornecedor':'Select Provider' ?></option>
				<?php  $query = "SELECT (ntservice.id) as id,name FROM ntservice join ntlnkprovidercontracttoservice on ntservice.id=ntlnkprovidercontracttoservice.service_id GROUP BY ntservice.name";
					if($query!=''){
						$result = mysqli_query($conf, $query);
						if ($result) {

							if(mysqli_num_rows($result)>0){
									while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
							<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
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
          <div class="col-md-6">
				<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="service">
				<div class="field_label label">
					<span title="">
						<?php echo ($jsonData['language']=='PT BR')? 'Razão':'Reason' ?> :
					</span>
				</div>
				<div class="field_data">
				<div class="field_value">
				<div class="field_input_zone field_input_string">
					<ul>
						<?php  $query = "SELECT npreasonid,reason_name FROM npreason";
							if($query!=''){
								$result = mysqli_query($conf, $query);
								if ($result) {
									if(mysqli_num_rows($result)>0){
										while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { 
						?>
								<li><input type="checkbox" name="reason[]" id='<?php echo $row['npreasonid'] ?>' value="<?php echo $row['npreasonid'] ?>"> <label for="<?php echo $row['npreasonid'] ?>"><?php echo $row['reason_name'] ?></label></li>
						<?php 
										}
									}
								}
							}
						?>
					</ul>
				</div>
				</div>
				</div>
				</div>
			</div>
		<div class="col-md-12">
			<div class="col-md-6">
				<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="service">
				<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'From Date':'From Date' ?> :</span></div>
				<div class="field_data">
				<div class="field_value">
				<div class="field_input_zone field_input_string">
			<input type="date" name="fromdate" id="fromdate" > 
		</div>
	</div>
</div>
</div>
</div>
<div class="col-md-6">
				<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="service">
				<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'To Date':'To Date' ?> :</span></div>
				<div class="field_data">
				<div class="field_value">
				<div class="field_input_zone field_input_string">
			 <input type="date" name="todate" id="todate" > 
		</div>
	</div>
</div>
</div>
</div>
</div>
                                       
<input type="button" onclick="exportndr()" class="ndrexport" value="Export"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
<div class="msgLoad" style="padding-top: 10px;float:right;"></div>
</div>

<script>

 	$(document).ready(function(){
		$('#siteTbl').DataTable({
	      'processing': true,
	      'serverSide': true,
	      'serverMethod': 'post',
	      'ajax': {
	          'url':'/application/activityajax.php'
	      },
	      'columns': [
	         { data: 'actvitycode' },
	         { data: 'description' },
	         { data: 'site' },
	         { data: 'province' },
	         { data: 'muncipal' },
	         { data: 'reason_name' },
	         { data: 'accesstype' },
	         { data: 'fuel' },
	         { data: 'provider' },
	         { data: 'service' },
	         { data: 'area' },
	         { data: 'created_by' },
	         { data: 'created_date' },
	         { data: 'action' },
	      ]
	   });
	});

	/*$(document).ready(function(){
		$('#siteTbl').DataTable({	 			
			 "order": [[ 0, 'desc' ]],
			 "visible":[[ 0, false ]],
		 
			});
	});*/

	$(".ndrexport").on("click",function(){
    	$( "#modmsc" ).dialog();
    });
	//  	$(".search_sites").on("click",function(){
	// 	var prov = $(this).siblings("#search_province").val();
	// 	console.log(prov);
	// 	$.ajax({
	// 		url: "addSiteAttr.php",
	// 		data: {"attr":"search_site","search":"province","search_val":prov},
	// 		type: "POST",
	// 		dataType: "json",
	// 		success: function(res){
	// 			if(res.flag){
	// 				$("#siteTBody").html(res.info);
	// 			}
	// 		}
	// 	});
	// });
	 </script>
	
	 <!--End by Priya Activity display-->
	 <!--Start by Mahesh Activity Delete -->
	 <script>
   function deleteactivity(activityid){
	   //alert(activityid);
		if(activityid==''){
			 alert('Error');
		}else{
			   $.ajax({
				 type: 'POST',
				 url: 'deleteactivity.php',
				 dataType: "json",
				 data: JSON.stringify({'activityid': activityid }),
				 success: function (data) {
					  alert('Activity Deleted Successfully');
					  window.location.href = "https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity";
				 }
		 	});
		}
	}

	function exportndr(){

		var providerid=$("#provider").val();
		var serviceid=$("#service").val();
		var fromdate=$("#fromdate").val();
		var todate=$("#todate").val();
		var checkboxes = document.getElementsByName('reason[]');
		var vals = "";
		for (var i=0, n=checkboxes.length;i<n;i++) 
		{
		    if (checkboxes[i].checked) 
		    {
		        vals += ","+checkboxes[i].value;
		       
		    }
		}
		vals=vals.substr(1);

		if((fromdate!='' || todate!='') && (fromdate=='' || todate=='')){
			alert('Please Select From And To Dates');
		}else{

			window.location.href = "https://nt3.nectarinfotel.com/pages/exportndr.php?provider="+providerid+"&reason="+vals+"&from="+fromdate+"&to="+todate;
			//window.location.href = "https://nt3.nectarinfotel.com/pages/exportndr.php?provider="+providerid+"&service="+serviceid+"&from="+fromdate+"&to="+todate;
		}
    }
</script>
					 <!--End by Mahesh Activity Delete -->
	 

<!--***************** Affected site script *************-->

		
<script type="text/javascript">

	var affectedSites = [];
	var addedSiteTemp = [];
	if ( ! $.fn.DataTable.isDataTable( '#siteTblAdd' ) ) {
		  $('#siteTblAdd').DataTable({
			 	"pagingType": "full_numbers",
				"pageLength": 10,
			});
		}
		
	$('#affsite_list_btnAdd').on('click',function(){

		$( "#addAffectedComponentDialog" ).dialog({
			height: 550,
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
				$.ajax({
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
				});

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
				data: {'attr':'getSites','sites':affectedSites},
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
 
<!--***************** End Affected site script *************-->
 
