
<?php /********** Province ***********/
include('../webservices/wbdb.php');

$postData = file_get_contents("php://input");
$jsonData = json_decode($postData,TRUE);
                    
?>
<style type="text/css">
	mark{background:orange;color:black;}
	h1{display: none;}
</style>
<!-- <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/plug-ins/1.10.20/features/searchHighlight/dataTables.searchHighlight.css"> -->
<div class="ui-layout-content" style="overflow: auto; position: relative; visibility: visible;">
    <h2 style="font-family: Tahoma, Verdana, Arial, Helvetica!important;
    color: #422462!important;font-weight: bold!important;font-size: 12pt!important;"><img src="https://nt3.nectarinfotel.com/images/listicon.png" style="vertical-align:middle; width: 26px;"><?php echo ($jsonData['language']=='PT BR')? 'Lista de sites':'Site List' ?></h2>
    <div class="actions_button">
		<a href="https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=addSite" style="margin-top: 6px;">
		<?php echo ($jsonData['language']=='PT BR')? 'Nova...':'New...' ?></a>
									</div>
									<div class="actions_button">

		<a href="exportMaxSiteDown.php" style="margin-top: 6px;"><?php echo ($jsonData['language']=='PT BR')? 'Export Max Site Down':'Export Max Site Down' ?></a>
		
		<a href="export.php" style="margin-top: 6px;"><?php echo ($jsonData['language']=='PT BR')? 'Exportar todos os sites':'Export All Sites' ?></a>									</div>

		<div class="actions_button">
			<a href="javascript:void(0)" id="exportST" style="margin-top: 6px;"><?php echo ($jsonData['language']=='PT BR')? 'Exportar sites tickets':'Export Site Tickets' ?></a>
			</div>
		</div>

		<div id="exportSiteTicket" class="modal">
	        <h2>Export Site Tickets</h2>
			<div class="col-md-12">
				<div class="col-md-6">
					<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="service">
						<div class="field_label label">
							<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Data de':'From Date' ?> :</span>
						</div>
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
						<div class="field_label label">
							<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Data para':'To Date' ?> :</span>
						</div>
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
			<input type="button" onclick="exportSiteTicket()" class="exportSTModal" value="Export"  style="padding: 5px 17px 5px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
		  	<div class="msgLoad" style="padding-top: 10px;float:right;"></div>
		</div>
<?php
$allSitesList = CMDBSource::QueryToArray("SELECT * FROM ntsites join ntsiteprovince on ntsites.province=ntsiteprovince.province_id WHERE ntsites.is_active = 1 ORDER BY ntsites.created_date DESC"); ?>
<!--						<button type="button" class="action addsite" id="addsite">Add Site</button><br/><br/><div id="linkedset_sites_list"><input type="hidden" id="2_sites_list" value="[]"><input type="hidden" name="attr_sites_list" value="">-->
            
		<div id="linkedset_sites_list">
            <table class="listResults siteTbl" id="siteTbl"><thead><tr>

            	<th title="Select All / Deselect All">
            		<input class="select_all" onclick="CheckAll('#linkedset_sites_list .selection', this.checked);" type="checkbox" value="1" >
            	</th>

            		<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Código do site':'Site Code' ?></th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Nome do site':'Site Name' ?></th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?></th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Área Responsável':'Responsible Area' ?></th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Data de criação':'Created Date' ?></th>
					<th><?php echo ($jsonData['language']=='PT BR')? 'Açao':'Action' ?></th>
					</tr></thead><tbody id="siteTBody">
						<?php
                                                                $_GET['id']="5";
						$i = 0;
						if(!empty($allSitesList)){

							$addedSites = array();
		
                                                        $allSitesList = CMDBSource::QueryToArray("SELECT ntsites.site_id,ntsites.site_code,ntsites.site_name,ntsiteprovince.province,ntsites.responsible_area,ntsites.created_date FROM ntsites join ntsiteprovince on ntsites.province=ntsiteprovince.province_id WHERE ntsites.is_active = 1 ORDER BY ntsites.created_date DESC");
							foreach ($allSitesList as $aDBInfo) {
								
								$selected = (in_array($aDBInfo['site_id'],$addedSites))? "checked='checked'":"";
								//echo $selected;
                                                                ?>
								<tr><td><input class="selection chkId" data-remote-id="<?php echo $aDBInfo['site_id'] ?>" data-unique-id="<?php echo $i ?>" type="checkbox" value="<?php echo $aDBInfo['site_id'] ?>" name="sites[]" <?php echo $selected ?>> </td>
									<td>
										<a href='<?php echo "https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=NewCI&c[feature]=SiteInformation&id=".$aDBInfo['site_id']; ?>' class='siteDetailsFinal' id="<?php echo $aDBInfo['site_id'] ?>">
											<?php echo $aDBInfo['site_code'] ?>
									</td>
									<td><?php echo $aDBInfo['site_name'] ?></td>
									<td><?php echo $aDBInfo['province'] ?></td><td class="ignore"><span><?php echo $aDBInfo['responsible_area'] ?></span></td><td class="ignore"><span><?php echo date('d M Y (h:i a)',strtotime($aDBInfo['created_date'])) ?></span></td>
									<td class="ignore"><span><a href="https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=siteDetails&id=<?php echo $aDBInfo['site_id']; ?>"><?php echo ($jsonData['language']=='PT BR')? 'Editar':'Edit' ?></a>|<a href="#" onclick="deletesite(<?php echo $aDBInfo['site_id']; ?>)"><?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?></a></span></td></tr>
								<?php $i++;
							}
						}else{
							$siteList .= "<tr><td colspan='3' style='text-align: center;'>No sites available</td></tr>";
						}
                                                ?>
                                                            </tbody></table>
</div>
                                                             <br/>
                                                            <input type="button" class="action delselsitebtn" name="removeSite" id="removeSite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir site selecionado':'Delete Selected Site' ?>" disabled="disabled">
                                                            <input type="button" class="action delselsitebtn" name="exportSite" id="exportSite" value="<?php echo ($jsonData['language']=='PT BR')? 'Exportar site selecionado':'Export Selected Site' ?>" disabled="disabled">

                                                        </div></div>
                                                            
                                                            <div id="siteInfoModal" class="modal" style="padding-left: 89px;">
<div class="modal-content" style="padding-top: 0px!important;padding-left: 10px!important;padding-right: 10px!important;padding-bottom: 12px!important; margin-left: 300px !important;margin-top: 70px !important;border: 1px solid #ddd!important;border-radius: 3px!important;width: 50%!important;">
		<span class="close siteInfoClose" style="margin-top: 10px!important;font-size: 20px!important;">&times;</span>
		<h4 style="color: #F17422!important;padding-bottom: 10px!important;border-bottom: 1px solid #dcdcdc!important;text-transform:uppercase;"></h4>
							  <div class="table-responsive" id="siteContent">
							    	<table class="table table-borderless"  style="width: 100%!important;">
										<tbody class="tbd">
										
										</tbody>
										</table>
							    	</div>											    
								  </div>
								</div>
                           <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    
    <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>

    <!-- <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.20/features/searchHighlight/dataTables.searchHighlight.min.js"></script> -->

    <!-- <script type="text/javascript" src="https://bartaz.github.io/sandbox.js/jquery.highlight.js"></script> -->
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mark.js/8.11.1/mark.min.js"></script> -->
    <script type="text/javascript" src="../js/datatable.mark.js"></script>

	<script>

		$("#exportST").on("click",function(){
	    	$("#exportSiteTicket" ).dialog();
	    });
      	function exportSiteTicket(){

			var fromdate=$("#fromdate").val();
			var todate=$("#todate").val();

			if(fromdate=='' || todate==''){
				alert('Please Select From And To Dates');
			}else{
				window.location.href = "https://nt3.nectarinfotel.com/pages/siteTicketExport.php?from="+fromdate+"&to="+todate;
			}
      		//siteTicketExport.php
      	}

		var table = $('#siteTbl').DataTable( {
				dom: 'Bfrtip',
			            buttons: [{
			                extend: 'excel',
			                text: 'Excel',
			                className: 'exportExcel',
			                filename: 'Site_Details_Excel',
			                exportOptions: { modifier: { page: 'all'} }
			            },
			           /* {
			                extend: 'csv',
			                text: 'CSV',
			                className: 'exportExcel',
			                filename: 'Site_Details_Csv',
			                exportOptions: { modifier: { page: 'all'} }
			            }*/
			        ],
			    "columnDefs": [
			        { "targets": [4,5,6], "searchable": false }
			    ],
			    "aoColumnDefs": [{
			        'bSortable': false,
			        'aTargets': [0]
			    }],
			    /*mark:{
			    	exclude: [
				        '.ignore'
				    ]
			    }*/
			   // searchHighlight: true
			});                 
	 	var siteList = [];
	 	$(document).on('change','.chkId',function(){
	 		if($(this).is(':checked')==true){
	 			siteList.push($(this).val());
	 		}else{
	 			var selfval = $(this).val();
	 			siteList = jQuery.grep(siteList, function(value) {
				  return value != selfval;
				});
	 		}
	 		if(siteList.length>0){
	 			$("#removeSite").removeAttr("disabled");
	 			$("#exportSite").removeAttr("disabled");
	 		}else{
	 			$("#removeSite").attr("disabled","disabled");
	 			$("#exportSite").attr("disabled","disabled");
	 		}
	 		//console.log(siteList);
	 	});

	 	$(document).on('change','.select_all',function(){
	 		if($(this).is(':checked')==true){
	 			$("#removeSite").removeAttr("disabled");
	 			$("#exportSite").removeAttr("disabled");
	 		}else{
	 			$("#removeSite").attr("disabled","disabled");
	 			$("#exportSite").attr("disabled","disabled");
	 		}

	 		var cells = table.column(0).nodes(), // Cells from 1st column
		        state = this.checked;
	 		var active = table.rows({filter: 'applied'}).every( function () {
			    var data = this.data();
			    if(state){
			    	siteList.push($(data[0]).val());
			    }else{
		    		var selfval = $(data[0]).val();
		 			siteList = jQuery.grep(siteList, function(value) {
					  return value != selfval;
					});
			    }
			    //data.querySelector("input[type='checkbox']").checked = state;
			});


			for (var i = 0; i < cells.length; i += 1) {
 				cells[i].querySelector("input[type='checkbox']").checked = state;
	 		}


	 		
		        
		   /* for (var i = 0; i < cells.length; i += 1) {
		    	
		    	if(active[0].includes(cells[i].querySelector("input[type='checkbox']").value)){
	        		cells[i].querySelector("input[type='checkbox']").checked = state;
		    	}
		    		console.log(active[0].includes(cells[i].querySelector("input[type='checkbox']").value)+" : "+cells[i].querySelector("input[type='checkbox']").value+" : "+active[0])
	        	if(state){
	        		siteList.push(cells[i].querySelector("input[type='checkbox']").value);
		        }else{
		        	var selfval = cells[i].querySelector("input[type='checkbox']").value;
		 			siteList = jQuery.grep(siteList, function(value) {
					  return value != selfval;
					});
		        }
		    }*/
	 	});


	 	$("#removeSite").on("click",function(){
	 		var confr = confirm("Are You Sure? You Want To Delete Selected Sites?");
	 		if(confr){
	 			$.ajax({
		 			url: "otherFields.php",
		 			data: {'field':'removeSites','siteList':siteList},
		 			type: 'POST',
		 			success: function(res){
		 				if(res){
		 					alert('Site Deleted Successfully');
		 					window.location.href = "https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite";
		 				}else{
		 					alert('Unable To Delete Site');
		 				}
		 			}
		 		});
	 		}
	 	});
//Export SIte
	 	$("#exportSite").on("click",function(){
	 		
	 		$.ajax({
	 			url: "otherFields.php",
	 			data: {'field':'exportSites','siteList':siteList},
	 			type: 'POST',
	 			success: function(res){
	 				/*if(res){
	 					alert('Site Exported Successfully');
	 					var uri = 'data:application/csv;charset=UTF-8,' + encodeURIComponent(res);
        				window.open(uri, 'Site.csv');
	 					//window.location.href = "https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite";
	 				}else{
	 					alert('Unable To Export Site');
	 				}*/

	 				if (res.filename != undefined && res.filename.length > 0) {
			            var uri = 'https://nt3.nectarinfotel.com/webservices/' + encodeURIComponent(res.filename);
        				window.open(uri, 'Site.csv');
			        }
	 			}
	 		})
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
         
          <script>
                 var modalSiteInfo = document.getElementById("siteInfoModal");
								var siteInfobtn = document.getElementsByClassName("siteDetails"); 
								var siteInfoClose = document.getElementsByClassName("siteInfoClose")[0];
								$(document).on("click" ,'.siteDetails', function(){
								//$(".siteDetails").on("click",function(){
									$(".ui-layout-resizer").css("position","static");
									var id = $(this).attr("id");
									$.ajax({
										url: "addSiteAttr.php",
										type:"POST",
										data: {"attr":"siteInfo","site_id":id},
										dataType: "json",
									success: function(res){
											console.log(res);
											if(res.flag){
												$("#siteInfoModal h4").html(res.info.site_name);
												//var i=4;
												//var j = 4;
												$(".tbd").html("");
                                                                                               
												//$.each(res.info, function(key, value) {
														
														//console.log(key+\'And \'+value);
														//if(i%4==0){
															//j = i;
															//$(".tbd").append("<tr class=\'trCls"+j+"\'>");
														//}
														//$(".tbd .trCls"+j).append(\'<td class="mod_tbltd"> <label><span style="color: #696969!important;font-weight: bold!important;">\'+key.replace(/_/g, " ")+\' : </span> <span> \'+ value +\' </span></label> </td>\');
														//if(i%5==0){
															//$(".tbd").append("</tr>");
														//}
														//i++;
													//});
                                                                                                        
                                                                                                        // NEW CODE
                                                                                                        
                                                                                                var stage_data=res.info;
                                                                                                
                                                  var new_data="         <tr> "+
                                 "<td class=\'mod_tbltd\' colspan=\'2\' style=\'color:#262262;font-weight:bold;border-bottom: 1px solid #dcdcdc;\'>Site</td>"+
"<td class=\'mod_tbltd\' style=\'border-bottom: 1px solid #dcdcdc;\'><a href=\'UI.php?c%5Bmenu%5D=siteDetails&amp;id="+stage_data["site_id"]+"\' type=\'button\' id=\'editSite\' class=\'action 152\'>Modify</a></td>"+
				" </tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Site ID : </span>"+
					" <span>"+stage_data["site_id"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Site Name :</span>"+
					" <span>"+stage_data["site_name"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Network : </span>"+
					" <span>"+stage_data["network"]+"</span></label></td>"+
				" </tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Responsible area :</span><span>"+stage_data["responsible_area"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Priority :</span>"+
					" <span>"+stage_data["priority"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
				" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Site Code  :</span><span>"+stage_data["site_code"]+"</span></label></td></tr>"+
					
                     "<tr><td class=\'mod_tbltd\' colspan=\'3\'>"+
                      " <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Priority Comment :  </span><span>"+stage_data["priority_comment"]+"</span></label></td></tr>"+
					
				" <tr>"+
				" <td class=\'mod_tbltd\' colspan=\'3\'></td>"+
				" </tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\' colspan=\'3\' style=\'color:#262262;font-weight:bold;border-bottom: 1px solid #dcdcdc;\'>Localization</td>"+
				" </tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Province : </span>"+
					" <span>"+stage_data["province"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Munciple :  </span>"+
					" <span>"+stage_data["munciple"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Locality : </span>"+
					" <span>"+stage_data["locationname"]+"</span></label></td>"+
				" </tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Lattidude : </span><span>"+stage_data["lat"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'><label><span style=\'color: #696969!important;font-weight: bold!important;\'>Longitude : </span><span>"+stage_data["lng"]+"</span></label>"+
				" </td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'></span> <span> </span> </label></td>"+
				" </tr>"+
				" <tr><td class=\'mod_tbltd\' colspan=\'3\'></td></tr>"+
				" <tr><td class=\'mod_tbltd\' colspan=\'3\' style=\'color:#262262;font-weight:bold;border-bottom: 1px solid #dcdcdc;\'>Model</td></tr>"+
				" <tr><td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Element Type : </span><span>"+stage_data["element_type"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'><label><span style=\'color: #696969!important;font-weight: bold!important;\'>Vendor : </span><span>"+stage_data["vendor"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Model :  </span><span>"+stage_data["model"]+"</span></label></td>"+
				" </tr>"+
				" <tr><td class=\'mod_tbltd\' colspan=\'3\'></td></tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\' colspan=\'3\' style=\'color:#262262;font-weight:bold;border-bottom: 1px solid #dcdcdc;\'>Dependency</td>"+
				" </tr>"+
				" <tr><td class=\'mod_tbltd\'><label><span style=\'color: #696969!important;font-weight: bold!important;\'>MSC :  </span><span>"+stage_data["msc"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
				" 	<label><span style=\'color: #696969!important;font-weight: bold!important;\'>MGW  : </span><span>"+stage_data["mgw"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
				" 	<label><span style=\'color: #696969!important;font-weight: bold!important;\'>BSC  :  </span><span>"+stage_data["bsc"]+"</span></label></td>"+
				" </tr>"+
				" <tr><td class=\'mod_tbltd\' colspan=\'3\'></td></tr>"+
				" <tr><td class=\'mod_tbltd\' colspan=\'3\' style=\'color:#262262;font-weight:bold;border-bottom: 1px solid #dcdcdc;\'>Planning</td>"+
				" </tr>"+
				" <tr><td class=\'mod_tbltd\'><label><span style=\'color: #696969!important;font-weight: bold!important;\'>Phase : </span><span>"+stage_data["phase"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Service Date : </span><span>"+stage_data["service_date"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Stage :</span><span>"+stage_data["stage"]+"</span></label></td>"+
				" </tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\'><label><span style=\'color: #696969!important;font-weight: bold!important;\'>Sub Stage : </span><span>"+stage_data["sub_stage"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Start Date :  </span><span>"+stage_data["start_date"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>End Date :    </span><span>"+stage_data["end_date"]+"</span></label></td>"+
				" </tr>";
                                $(".tbd").append(new_data);

											}
											modalSiteInfo.style.display ="block";

										}
									});
								})
								
								$(".siteInfoClose").on("click",function(){	
									$(".ui-layout-resizer").css("position","absolute");
									modalSiteInfo.style.display = "none"; 
								});

								window.onclick = function(event) { 
									if (event.target == modalSiteInfo) {
										$(".ui-layout-resizer").css("position","absolute");
										modalSiteInfo.style.display = "none";
									}
								}
                                                                </script>
        <!--Start by Mahesh Site Delete -->
	 <script>
   function deletesite(siteid){
	   //alert(siteid);
if(siteid==''){
     alert('Error');
}else{
               $.ajax({
         type: 'POST',
         url: 'deletesite.php',
         dataType: "json",
         data: JSON.stringify({'siteid': siteid }),
         success: function (data) {
  alert('Site Deleted Successfully');
  window.location.href = "https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite";
         }
     });

    }
    }
 </script>
	 <!--End by Mahesh Site Delete -->
                                  
                                         