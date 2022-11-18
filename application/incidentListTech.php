
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
	.notavailable{
	    background-color: gray;
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
.dataTables_processing{
    text-align: center;
    position: absolute;
    margin-top: 300px;
    margin-left: 500px;
    font-weight: 600;
    background: radial-gradient(black, transparent);
    width: 200px;
    height: 37px;
    padding-top: 15px;
    color: #fff;
}
div#networkchk{
	/*position: relative !important;
    left: 16em !important;
    top: 2em !important;*/
}
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">
<div class="ui-layout-content" style="overflow: auto; position: relative; visibility: visible;">
    <h2 style="font-family: Tahoma, Verdana, Arial, Helvetica!important;
    color: #422462!important;font-weight: bold!important;font-size: 12pt!important;">
<img src="http://nt3.nectarinfotel.com/env-production/nt3-incident-mgmt-itil/images/incident.png" style="vertical-align:middle;width: 32px;">&nbsp; Open Incident Technologies</h2>
<br/></br>
<div id="networkchk">
		<table>
			<thead>	
			<th style="padding: 0px 15px;"><h4>Filters: </h4></th>
			<th>
				<input class="technology" rel="capacity" type="checkbox" name="tech[]" value="2G" id="2g"><label for="2g"> 2G </label>
				<input class="technology" rel="capacity" type="checkbox" name="tech[]" value="3G" id="3g"><label for="3g"> 3G </label>
				<input class="technology" rel="capacity" type="checkbox" name="tech[]" value="4G" id="4g"><label for="4g"> 4G </label>
			</th>
			<th style="padding-left: 20px;">
				<select name="reason" id="reason">
					<option value="">All Reasons</option>
					<?php
						$reasons = CMDBSource::QueryToArray("SELECT * FROM ntreason WHERE is_active = 1"); 
						if(!empty($reasons)){
							foreach ($reasons as $rRows) {
								echo "<option value='".$rRows['reason_id']."'>".$rRows['reason']."</option>";
							}
						}
					?>
				</select>
			</th>
			<!-- <input class="technology" rel="capacity" type="checkbox" name="tech[]" value="Onlyonetech" id="Onlyonetech"><label for="Onlyonetech">Only One Technologies</label> -->
			</thead>
		</table>
	</div>

 	<div class="actions_button">
		<a href="http://nt3.nectarinfotel.com/pages/UI.php?operation=new&class=Incident&c%5Bmenu%5D=NewIncident" style="margin-top: 6px;">New...</a>
		<a href="exportOpenInc.php" style="margin-top: 6px;"><?php echo ($postData['language']=='PT BR')? 'Exportar incidentes abertos':'Export All Open Incidents' ?></a>
		<a href="javascript:void(0)" id="exportAT" style="margin-top: 6px;"><?php echo ($postData['language']=='PT BR')? 'Exportar todos os incidentes':'Export All Incidents' ?></a>
		<a href="http://nt3.nectarinfotel.com/pages/openIncPreview.php" target="__change" style="margin-top: 6px;">Painel de Gerência</a>
	</div>

	<div id="exportAllTicket" class="modal">
	        <h2>Export All Incidents</h2>
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
			<input type="button" onclick="exportAllIncidents()" class="exportATModal" value="Export"  style="padding: 5px 17px 5px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
		  	<div class="msgLoad" style="padding-top: 10px;float:right;"></div>
		</div>
	
    <table class="listResults siteTbl" id="incidentTech">
    	<thead>
    		<tr>
    			<th class="header"><?php echo ($postData['language']=='PT BR')? 'Número do bilhete':'Ticket Number' ?></th>
    			<th class="header"><?php echo ($postData['language']=='PT BR')? 'Título':'Title' ?></th>
    			<th class="header"><?php echo ($postData['language']=='PT BR')? 'Província':'Province' ?></th>
    			<th class="header">Site Principal</th>
    			<th><?php echo ($postData['language']=='PT BR')? 'Dependente':'Dependent' ?></th>
    			<th><?php echo ($postData['language']=='PT BR')? 'Razão':'Reason' ?></th>
				<th class="header">2G</th>
    			<th class="header">3G</th>
    			<th class="header">4G</th>
    			<th class="header"><?php echo ($postData['language']=='PT BR')? 'Duração':'Duration' ?></th>
    			<th class="header"><?php echo ($postData['language']=='PT BR')? 'Status':'Status' ?></th>
    		</tr>
    	</thead>
    	
    </table>
    <br/>

    <div id="nextUpdate" class="modal">
        <h2>Next Update</h2>
		<div class="col-md-12">
			<div class="col-md-6">
				<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="service">
					<div class="field_label label">
						<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Próxima Data':'Next Date' ?> :</span>
					</div>
					<div class="field_data">
						<div class="field_value">
							<div class="field_input_zone field_input_string">
								<input type="date" name="nextdate" id="nextdate" min="<?php echo date('Y-m-d') ?>" value="<?php echo date('Y-m-d') ?>"> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="field_container field_small" style="border-bottom: none!important;" data-attcode="service">
					<div class="field_label label">
						<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Tempo':'time' ?> :</span>
					</div>
					<div class="field_data">
						<div class="field_value">
							<div class="field_input_zone field_input_string">
								<input type="time" name="nexttime" id="nexttime" min="<?php echo date('H:i:s') ?>"> 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>                                         
		<input type="button" onclick="nextUpdate()" class="nextUpdateModal" value="Update"  style="padding: 5px 17px 5px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
	  	<div class="msgLoad" style="padding-top: 10px;float:right;"></div>
	</div>
	
<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<!-- <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script> -->
<script type="text/javascript">
	var tech = [];
	var reason = "";


    function nextUpdateLink(id){
    	$("#nextUpdate" ).data('id', id).dialog();
    }
    function nextUpdate(){
    	var ticketID = $("#nextUpdate" ).data('id');
    	var nextDate = $("#nextdate" ).val();
    	var nextTime = $("#nexttime" ).val();
    	
    	$(".nextUpdateModal").attr("disabled","disabled");
    	if(nextDate=="" || nextTime==""){
    		alert("Please Enter Date And Time");
    	}else{
    		if(ticketID!=undefined){
	    		$.ajax({
	    			url: 'otherFields.php',
	    			data: {"id":ticketID,"date":nextDate,"time":nextTime,"field":'nextUpdate'},
	    			type: 'POST',
	    			success: function(res){
	    				$(".nextUpdateModal").removeAttr("disabled");
    					$("#nextUpdate" ).dialog('close');
	    				if(res){
	    					alert('Next Date Updated');
	    					window.location.reload;
	    				}
	    			},
	    			error: function(xhr){
	    				console.log(xhr);
	    			}
	    		})
	    	}else{
	    		alert('Ticket not found');
	    	}
    	}
    }

	if ( ! $.fn.DataTable.isDataTable( '#incidentTech' ) ) {
	var table = $('#incidentTech').DataTable({
			"order": [[ 0, "desc" ]],
			"processing": true,
	        "serverSide": true,
	        'serverMethod': 'post',
	        //"ajax": "incidentListTechAjax.php",
	         "ajax": {
		       'url':'incidentListTechAjax.php',
		       'data': function(data){
		          data.tech = tech;
		          data.reason = reason;
		       }
		    },
	        'columns': [
		         { data: 'ticket_number' },
		         { data: 'title' },
		         { data: 'province' },
		         { data: 'site_principal' },
		         { data: 'dependent' },
		         { data: 'reason' },
		         { data: '2g' },
		         { data: '3g' },
		         { data: '4g' },
		         { data: 'duration' },
		         { data: 'status' },
		      ],
		    "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
		            
		            var statArr = aData['status'].split(" - ");
		            if ( statArr[0] == "Date Expired" )
		            {
			            $('td', nRow).css({
			            	'background-color' : '#ffc9a6',
			            	'color' : '#f17422'
			            });
		            }
		            else if ( statArr[0] == "Date Will Expire Soon" )
		            {
		                $('td', nRow).css({
			            	'background-color' : '#ffff99',
			            	'color' : '#f17422'
			            });
		            }
		        }
		});
	}
 
setInterval( function () {
    table.ajax.reload();
}, 20000 );

$("#reason").change(function() {
	reason = $(this).val();
	table.ajax.reload();
});

$(".technology").change(function() {
  var checked = $(this).val();
  if ($(this).is(':checked')) {
    tech.push(checked);
  } else {
    tech.splice($.inArray(checked, tech),1);
  }
  table.ajax.reload();
});
	/*

	{ data: 'ticket_number' },
		         { data: 'title' },
		         { data: 'province' },
		         { data: 'site_principal' },
		         { data: 'dependent' },
		         { data: 'reason' },
		         { data: '2g' },
		         { data: '3g' },
		         { data: '4g' },
		         { data: 'duration' },


		,
			"processing": true,
		    "serverSide": true,
		    //any other configuration options
		    "ajax": "application/incidentListTech.php"
	*/

	function exportInc(){
		window.location.href="http://nt3.nectarinfotel.com/pages/exportOpenInc.php";
	}

	$("#exportAT").on("click",function(){
    	$("#exportAllTicket" ).dialog();
    });

    function exportAllIncidents(){
    	var fromdate=$("#fromdate").val();
		var todate=$("#todate").val();

		if(fromdate=='' || todate==''){
			alert('Please Select From And To Dates');
		}else{
			window.location.href = "http://nt3.nectarinfotel.com/pages/exportAllIncident.php?from="+fromdate+"&to="+todate;
		}
    }

	/*setInterval(function(){
		if ($.fn.DataTable.isDataTable( '#incidentTech' ) ) {
			
			$('#incidentTech').dataTable().fnClearTable();
    		$('#incidentTech').dataTable().fnDestroy();
			$( "#incidentTech" ).load(window.location.href + " #incidentTech" );
			$('#incidentTech').DataTable({
					"order": [[ 0, "desc" ]]
				});
			}
			console.log('test');
	},20000);*/
</script>