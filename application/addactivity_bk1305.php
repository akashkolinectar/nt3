
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
<script>
    //function call_addActivity(){
    
    $(document).on('submit','#form_add_activity',function(event){

		event.preventDefault();

			var x = document.forms["form_add_activity"]["description"].value;
			if (x == "") {
				alert("Please enter decription");
				return false;
			}
			var p = document.forms["form_add_activity"]["site_province"].value;
			if (p == "") {
				alert("Please select Province");
				return false;
			}
			var m= document.forms["form_add_activity"]["site_munciple"].value;
			if (m == "") {
				alert("Please select Munciple");
				return false;
			}
			var l= document.forms["form_add_activity"]["site_locality"].value;
			if (l == "") {
				alert("Please select Locality");
				return false;
			}
			if ($('input[name="accesstype"]:checked').length == 0) {
		        alert('please select Access Type');
		        return false; } 
		        var value= $("input:radio[id=accesstype]:checked").val(); 
		      
		    if (value=="Internal") {
		        var ei= document.forms["form_add_activity"]["inemployee"].value;
			if (ei == "") {
				alert("Please select Internal Employee");
				return false;
			}
			var eri= document.forms["form_add_activity"]["reportedto"].value;
			if (eri == "") {
				alert("Please select Internal Reported To");
				return false;
			}
	    }
	    if (value=="External") {
		        var ee= document.forms["form_add_activity"]["extemployee"].value;
			if (ee == "") {
		    alert("Please select External Employee");
		    return false;
		  }
		  var er= document.forms["form_add_activity"]["extreportedto"].value;
		  if (er == "") {
		    alert("Please select External Reported To");
		    return false;
		  }
        }
      else {
        
         $.ajax({
         type: 'POST',
         url: 'addactivity.php',
         dataType: "json",
         //data: JSON.stringify($("#form_add_activity").serializeArray()),
         data: $(this).serialize(),
         success: function (data) {
            if(data==2){
                alert('NDR Already Exist');
                window.location = "https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity";
            }
            if(data==1){
                //var answer = confirm("Data saved Successfully ! \n You want to redirect activity page?")
                var answer = confirm("NDR Added Successfully!")
                if (answer){
                    // alert("Bye bye!")
                    window.location = "https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity";
                }
                else{
                    // alert("Thanks for sticking around!")
                }
                 // alert('Activity Deleted Successfully');
                 //  window.location.href = "https://nt3test.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity";
                }
                else 
                    if(data ==-1)
                    {
                    	alert("NDR Already exist");
                    // if(answer1){
                      // window.location = "https://nt3test.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=addactivity";
                       // }
                }
                /*else{
                    //failed
                    
                }*/
         },
          error: function (jqXHR, status, error) {
            console.log(jqXHR);
            console.log(status);
            console.log(error);
    }
     });

    
}
    });
 </script>
<?php /********** Province ***********/
require_once('../webservices/wbdb.php');
?>  


<div class="ui-layout-content" style="overflow: auto; position: relative; visibility: visible;">
    <h2 style="font-family: Tahoma, Verdana, Arial, Helvetica!important;
		color: #422462!important;font-weight: bold!important;font-size: 12pt!important;">
		<img src="https://nt3.nectarinfotel.com/images/activityview.png" style="vertical-align:middle;width: 32px;">&nbsp;Create NDR (Número De Registo)</h2>
	<div class="wizContainer">
		<form action="addactivity.php" method="post" id="form_add_activity"><p></p>
			<input type="hidden" name="" value="">
			<button type="button" class="action cancel" onclick="window.location.href='https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity'">Cancel</button>&nbsp;&nbsp;&nbsp;&nbsp;
			<button type="submit" class="action" id="form_add_activity"><span> Create </span></button>
			<!-- <button type="button" class="action" id="form_add_activity" onclick="call_addActivity()"><span> Create </span></button> -->
			<!-- <button type="button" name="next_action" value="ev_assign" class="action" style="background-color: #f17422;"><span>Modify</span></button> -->
			
		<div id="tabs">
				<ul>
					<li><a href="#tabs-01">NDR Information</a></li>
					<li><a href="#tabs-02">Affected Site</a></li>
				</ul>
<!-- ************* Tab 1 Start ***********-->

			<div id="tabs-01">
				<div class="ui-layout-content" style="overflow: auto; position: relative; height: 400px; visibility: visible;">
					<!--<h1 style="font-family: Tahoma, Verdana, Arial, Helvetica!important;
					color: #422462!important;font-weight: bold!important;font-size: 12pt!important;">
					<img src="https://nt3dg.nectarinfotel.com/images/addactivity.png" style="vertical-align:middle;    width: 32px;">&nbsp;
					Registration of NDR to Sites</h1>-->
					
											
											
				<div class="formactivity" style="border: none;padding: 10px;">
															
				<div class="table-responsive">
				<!-- <form action="addactivity.php" id="form_add_activity" method="post"> -->
				<!--<form action="" id="form_add_activity" method="post">-->
				<table class="table">
				<tr>
				<td colspan="3" style="text-align: left;width: 100%">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label"><span title="">Description :</span></div>
				<div class="field_data">
				<div class="field_value">
				<div id="field_2_title" class="field_value_container">
				<div class="attribute-edit" data-attcode="title">
					<input name="description" type="text" required="" style="width: 95%;">
					<img src="../images/validation_error.png" style="vertical-align:top;float: right;margin-right:11px;" title="Please specify a value">
				<!-- <div class="field_input_zone field_input_string">
				<span style="float:left"></span>
				</div> -->
				
				</div>
				</div>
				</div>
				</div>
				</div>
				</td>
				</tr>
				<tr><td colspan="3"> <br/></td></tr>
				<tr>
				<td style="width: 30%;text-align: left;">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label"><span title="">Province :</span></div>
				<div class="field_data">
				<div class="field_value">
				<!--  <div id="field_2_title" class="field_value_container">
				<div class="attribute-edit" data-attcode="title"> -->
				<div class="field_input_zone field_input_string">
				<select name="province" style="width: 80%;margin: 4px;" id="site_province">
				<option value="">Select province</option>
				<?php  
				//echo "test"; 
				$query = "SELECT * FROM ntsiteprovince WHERE is_active = 1 ORDER BY province_id ASC";
				if($query!=''){
				$result = mysqli_query($conf, $query);
				if ($result) {
				if(mysqli_num_rows($result)>0){
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
				<option value="<?php echo $row['province_id'] ?>"><?php echo $row['province'] ?></option>
				<?php   }
				}
				}
				} ?>
				</select>
				<img src="../images/validation_error.png" style="vertical-align:top;" title="Please specify a value">
				<!-- <span class="field_input_btn" style="float: right;"><img class="newFunctionalCIpro" id="mini_add_2_caller_id" style="border: 0;
					margin-top: 9px;margin-left: 0px!important;margin-right: 7px!important;cursor: pointer;" src="../images/mini_add.gif?t=1561626249.8396" onclick=""></span> -->
				</div>
				</div>
				<!--  </div>
				</div> -->
				</div>
				</div>
				</td>
				<td style="width: 30%;text-align: left;">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label"><span title="">Munciple :</span></div>
				<div class="field_data">
				<div class="field_value">
				<!--  <div id="field_2_title" class="field_value_container">
				<div class="attribute-edit" data-attcode="title"> -->
				<div class="field_input_zone field_input_string">
				<select name="site_munciple" style="width: 80%;margin: 4px;" id="site_munciple">
				<option value=""> -- Select One --</option>
				</select>
				<img src="../images/validation_error.png" style="vertical-align:top;" title="Please specify a value">
				<!-- <span class="field_input_btn" style="float: right;"><img class="site_munciple" id="mini_add_2_caller_id" style="border: 0;
				margin-top: 9px;margin-left: 0px!important;margin-right: 7px!important;cursor: pointer;" src="../images/mini_add.gif?t=1561626249.8396" onclick=""></span> -->
				</div>
				</div>
				<!--  </div>
				</div> -->
				</div>
				</div>
				</td>
				<td style="width: 30%;text-align: left;">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label"><span title="">Location :</span></div>
				<div class="field_data">
				<div class="field_value">
				<!--<div id="field_2_title" class="field_value_container">
				<div class="attribute-edit" data-attcode="title"> -->
				<div class="field_input_zone field_input_string">
				<select name="location" style="width: 80%;margin: 4px;" id="site_locality">
				<option value="">Select Location</option>
				</select>
				<img src="../images/validation_error.png" style="vertical-align:top;" title="Please specify a value">
				<!-- <span class="field_input_btn" style="float: right;"><img class="newFunctionalCIloc" id="mini_add_2_caller_id" style="border: 0;
				margin-top: 9px;margin-left: 0px!important;margin-right: 7px!important;cursor: pointer;" src="../images/mini_add.gif?t=1561626249.8396" onclick=""></span> -->
				</div>
				<!--  </div></div> -->
				</div>
				</div>
				</div>
				</td>  
				</tr>
				<tr><td colspan="3"><br></td></tr>
				<tr>
					<td style="width: 30%;text-align: left;">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label"><span title="">Reason:</span></div>
				<div class="field_data">
				<div class="field_value">
				<div class="field_input_zone field_input_string">
				<select name="selectedreason" style="width: 80%;margin: 4px;"  id="selectedreason">
				<option value="">Select Reason</option>
				<?php  $query = "Select * from  npreason GROUP BY npreason.npreasonid ORDER BY npreason.npreasonid  DESC";
					if($query!=''){
						$result = mysqli_query($conf, $query);
						if ($result) {

							if(mysqli_num_rows($result)>0){
									while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
							<option value="<?php echo $row['npreasonid'] ?>"><?php echo $row['reason_name'] ?></option>
							 <?php   }
										}
								}
																} ?>
				</select>
				
				</div>
				</div>
				</div>
				</div>
				</td>
				<td style="width: 30%;text-align: left;">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label" style="width: 10%;"><span title="">Other Reason :</span></div>
				<div class="field_data">
				<div class="field_value">
				<div id="field_2_title" class="field_value_container">
				<div class="attribute-edit" data-attcode="title">
				<div class="field_input_zone field_input_string">
				<span style="float:left">
				<textarea name="reason" id="site_priority_comment" style="width: 190px;border: 1px solid #e1e1e1;"></textarea>
				</span> 
				</div>
				</div>
				</div>
				</div>
				</div>
				</div>
				</td>   
				</tr>
				<tr>
				<td>
				</td>
				</td>
				<td colspan="2" style="text-align: left;">
				<div class="field_container field_small" style="border-bottom: none!important;height: 80px;">
				<div class="field_label label" style="width: 18%;"><span title="">Access Type : </span></div>
				<div class="field_data">
				<div class="attribute-edit" data-attcode="title">
				<div class="field_input_zone field_input_string" style="margin-top: 30px;">
				<span style="float:left;">
				<input type="radio" name="accesstype" id="accesstype" value="Internal" class="btn2">Internal
				<input type="radio" name="accesstype" id="extaccesstype" value="External" class="btn1" >External
				</span>
				</div>
				</div>
				</div>
				</div>
				</td>
				</tr>
				<script>
				$(document).ready(function(){
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
				<tr>
				<td style="width: 30%;display:none;text-align: left;" class="externals">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label"><span title="">Provider:</span></div>
				<div class="field_data">
				<div class="field_value">
				<div class="field_input_zone field_input_string">
				<select name="provider" style="width: 80%;margin: 4px;"  id="provider">
				<option value="">Select Provider</option>
				<?php  $query = "SELECT (ntcontract.id) as id,name FROM ntcontract join  ntlnkprovidercontracttoservice on  ntcontract.id=ntlnkprovidercontracttoservice.providercontract_id where ntcontract.finalclass='ProviderContract' GROUP BY ntcontract.id ORDER BY ntcontract.id  DESC";
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
				<a href="https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=ProviderContract"> 
				<span class="field_input_btn" style="float: right;">
				<img class="" id="" style="border: 0;margin-top: 9px;margin-left: 0px!important;margin-right: 8px!important;cursor: pointer;" src="../images/mini_add.gif?t=1561626249.8396" onclick=""></span></a>
				</div>
				</div>
				</div>
				</div>
				</td>
				<td style="width: 30%;display:none;text-align: left;" class="externals">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label"><span title="">Service :</span></div>
				<div class="field_data">
				<div class="field_value">
				<!-- <div id="field_2_title" class="field_value_container">
				<div class="attribute-edit" data-attcode="title"> -->
				<div class="field_input_zone field_input_string">
				<select name="service" style="width: 77%;" id="service">
				<option value=""> -- Select One --</option>
				</select> 
				<span class="field_input_btn" style="float: right;">
				<a href="https://nt3.nectarinfotel.com/pages/UI.php?operation=new&class=Service&c[menu]=Service">
				<img class="" id="mini_add_2_caller_id" style="border: 0;margin-top: 9px;margin-left: 0px!important;margin-right: 16px!important;cursor: pointer;" src="../images/mini_add.gif?t=1561626249.8396" onclick=""></span>
				</div>
				<!-- </div>
				</div> -->
				</div>
				</div>
				</div>
				</td>
				<td style="width: 30%;display:none;text-align: left;" class="externals">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label"><span title="">Employee :</span></div>
				<div class="field_data">
				<div class="field_value">
				<div id="field_2_title" class="field_value_container">
				<div class="attribute-edit" data-attcode="title">
				<div class="field_input_zone field_input_string">
				<select name="extemployee" id="extemployee" style="width: 80%;">
				<option value="">Select Employee</option>
				<?php  //$query = "SELECT * FROM npemployee";
				$query = "SELECT * FROM ntcontact join ntperson on ntcontact.id=ntperson.id AND ntcontact.finalclass ='Person' ORDER BY ntperson.id DESC";
				if($query!=''){
				$result = mysqli_query($conf, $query);
				if ($result) {
				if(mysqli_num_rows($result)>0){
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
				<option value="<?php echo $row['id'] ?>"><?php echo $row['first_name'] ?> <?php echo $row['name'] ?></option>
				<?php   }
				}
				}
				} ?>
				</select> 
				<img src="../images/validation_error.png" style="vertical-align:top;" title="Please specify a value">
				</div>
				</div>
				</div>
				</div>
				</div>
				</div>
				</td> 
				</tr>
				<tr><td colspan="3"><br/></td></tr>
				<tr>
				<td colspan="3" style="display:none;text-align: left;" class="externals">
				<a href="https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=MyShortcuts_5" style="float: right;cursor: pointer;">Add Employee</a>
				</td>
				</tr>
				<tr>
				<td style="width: 30%;display:none;text-align: left;" class="externals">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label"><span title="">Reported To :</span></div>
				<div class="field_data">
				<div class="field_value">
				<div id="field_2_title" class="field_value_container">
				<div class="attribute-edit" data-attcode="title">
				<div class="field_input_zone field_input_string">
				<select name="extreportedto" id="extreportedto" style="width: 80%;">
				<option value="">Select Reported To</option>
				<?php  //$query = "SELECT * FROM npemployee";
				$query = "SELECT * FROM ntcontact join ntperson on ntcontact.id=ntperson.id AND ntcontact.finalclass ='Person' ORDER BY ntperson.id DESC";
				if($query!=''){
				$result = mysqli_query($conf, $query);
				if ($result) {
				if(mysqli_num_rows($result)>0){
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
				<option value="<?php echo $row['id'] ?>"><?php echo $row['first_name'] ?> <?php echo $row['name'] ?></option>
				<?php   }
				}
				}
				} ?>
				</select> 
				<img src="../images/validation_error.png" style="vertical-align:top;" title="Please specify a value">
				</div>
				</div>
				</div>
				</div>
				</div>
				</div>
				</td>
				</tr>
				<tr>
				<td style="width: 30%;display:none;text-align: left;" class="internals">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label"><span title="">Area :</span></div>
				<div class="field_data">
				<div class="field_value">
				<!--<div id="field_2_title" class="field_value_container">
				<div class="attribute-edit" data-attcode="title"> -->
				<div class="field_input_zone field_input_string">
				<select name="movicelarea" style="margin: 4px;width: 80%;" id="selectma1">
				<option value="">Select Area</option>
				<?php  $query = "SELECT * FROM ntorganization ORDER BY id DESC";
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
				<span class="field_input_btn" style="float: right;">
				<a href="https://nt3.nectarinfotel.com/pages/UI.php?operation=new&class=Organization&c[menu]=Organization">
				<img class="" id="mini_add_2_caller_id" style="border: 0;margin-top: 9px;margin-left: 0px!important;margin-right: 9px!important;cursor: pointer;" src="../images/mini_add.gif?t=1561626249.8396" onclick=""></a></span>
				</div>
				</div>
				<!--  </div>
				</div> -->
				</div>
				</div>
				</td>   
				<td style="width: 30%;display:none;text-align: left;" class="internals">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label"><span title="">Employee :</span></div>
				<div class="field_data">
				<div class="field_value">
				<div id="field_2_title" class="field_value_container">
				<div class="attribute-edit" data-attcode="title">
				<div class="field_input_zone field_input_string">

				<select name="employee" id="inemployee" style="width: 80%;">
				<option value="">Select Employee</option>
				 <?php  //$query = "SELECT * FROM npemployee";
				$query = "SELECT * FROM ntcontact join ntperson on ntcontact.id=ntperson.id AND ntcontact.finalclass ='Person' ORDER BY ntperson.id DESC";
										if($query!=''){
				$result = mysqli_query($conf, $query);
				if ($result) {

				if(mysqli_num_rows($result)>0){
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
				<option value="<?php echo $row['id'] ?>"><?php echo $row['first_name'] ?> <?php echo $row['name'] ?></option>
				 <?php   }
				}
				}
				} ?>
				</select> 
				<img src="../images/validation_error.png" style="vertical-align:top;" title="Please specify a value">
				</div>
				</div>
				</div>
				</div>
				</div>
				</div>
				</td> 
				<td style="width: 30%;display:none;text-align: left;" class="internals">
				<div class="field_container field_small" style="border-bottom: none!important;">
				<div class="field_label label"><span title="">Reported To :</span></div>
				<div class="field_data">
				<div class="field_value">
				<div id="field_2_title" class="field_value_container">
				<div class="attribute-edit" data-attcode="title">
				<div class="field_input_zone field_input_string">

				<select name="reportedto" id="reportedto" style="width: 80%;">
				<option value="">Select Reported TO</option>
				<?php  //$query = "SELECT * FROM npemployee";
				$query = "SELECT * FROM ntcontact join ntperson on ntcontact.id=ntperson.id AND ntcontact.finalclass ='Person' ORDER BY ntperson.id DESC";
				if($query!=''){
				$result = mysqli_query($conf, $query);
				if ($result) {
				if(mysqli_num_rows($result)>0){
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
				<option value="<?php echo $row['id'] ?>"><?php echo $row['first_name'] ?> <?php echo $row['name'] ?></option>
				<?php   }
				}
				}
				} ?>
				</select> 
				<img src="../images/validation_error.png" style="vertical-align:top;" title="Please specify a value">
				</div>
				</div>
				</div>
				</div>
				</div>
				</div>
				</td> 
				</tr>
				</table>
				<!-- <button type="button" class="action cancel" style="margin-top: 23px;">
				<a href="https://nt3dg.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity" style="text-decoration: none;">Cancel</a></button> -->
				<!--  <input type="submit" class="createSite activsubtn" value="create" >-->
				<!--<input type="button" class="createSite activsubtn" value="create" id="form_add_activity" onclick="call_addActivity()">-->
				<!-- <input type="button" class="createSite activsubtn" value="I Create" id="form_add_activity" onclick="call_addActivity()"> -->
				
				</div>
				<br><br><br><br>
				</div>
				 </div>
				<!--Dialog Start Edited by Mahesh -->

				<div id="editFunctionalCIDialog" class="modal">
				<h1>Add Services</h1>
				<!-- <label>  : </label><br/>-->
				<!--<span class="form_validation">
				<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
				</span><br/><br/>-->
				<!--<label> New  : </label><br/>-->
				<!-- <input type="text" name="functionalci_new_name" id="functionalci_new_name">                                     -->
				<input type="text" name="servicep" id="servicep" onfocus="this.value=''">
				<span class="form_validation">
				<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
				</span>
				<input type="button" onclick="addservice()" class="editFunctionalCI" value="Add"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
				<div class="msgLoad" style="padding-top: 10px;float:right;">
				</div>
																								
				</div>

				<div id="editFunctionalCIDialogma" class="modal">
				<h1>Add Movicel Area</h1>
				<!-- <label>  : </label><br/>-->
				<!--<span class="form_validation">
				<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
				</span>
				<br/><br/>-->
				<!-- <label> New  : </label><br/>-->
				<!-- <input type="text" name="functionalci_new_name" id="functionalci_new_name">                                     -->
				<input type="text" name="movicelp" id="movicelp" onfocus="this.value=''"> 
				<span class="form_validation">
				<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
				</span>
				<input type="button" onclick="addmovicel()" class="editFunctionalCIma" value="Add"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
				<div class="msgLoad" style="padding-top: 10px;float:right;">
				</div>
				</div>
				<div id="editFunctionalCIDialogcy" class="modal">
				<h1>Add Company</h1>
				<!--  <label>  : </label><br/>-->
				<!--  <span class="form_validation">
				<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
				</span>
				<br/><br/>-->
				<!-- <label> New  : </label><br/>-->
				<!--<input type="text" name="functionalci_new_name" id="functionalci_new_name">                                     -->
				<input type="text" name="companyp" id="companyp" onfocus="this.value=''"> 
				<span class="form_validation">
				<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
				</span>
				<input type="button" onclick="addcompany()" class="editFunctionalCIcy" value="Add"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
				<div class="msgLoad" style="padding-top: 10px;float:right;">
				</div>
																								
				</div>
				<div id="editFunctionalCIDialogloc" class="modal">
				<h1>Add Location</h1>
				<!-- <label>  : </label><br/>-->
				<!--  <span class="form_validation">
				<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
				</span>
				<br/><br/>-->
				<!--<label> New  : </label><br/>-->
				<!--  <input type="text" name="functionalci_new_name" id="functionalci_new_name">                                     -->
				<input type="text" name="locationp" id="locationp" onfocus="this.value=''"> 
				<span class="form_validation">
				<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
				</span>
				<input type="button" onclick="addlocation()" class="editFunctionalCIloc" value="Add"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
				<div class="msgLoad" style="padding-top: 10px;float:right;">
																								
																							</div>
																								
				</div>
				<div id="editFunctionalCIDialogpro" class="modal">
				<h1>Add Province</h1>
				<!--  <label>  : </label><br/>-->
				<!--   <span class="form_validation">
				<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
				</span>
				<br/><br/>-->
				<!-- <label> New  : </label><br/>-->
				<!-- <input type="text" name="functionalci_new_name" id="functionalci_new_name">   -->
				 <input type="text" name="provincep" id="provincep" onfocus="this.value=''"> 
				<span class="form_validation">
				<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
				</span>
				<input type="button" onclick="addprovince()" class="editFunctionalCIpro" value="Add"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
				<div class="msgLoad" style="padding-top: 10px;float:right;">
				</div>
				</div>
			</div>
			
<!-- ************* Tab 1 End ***********-->
			
<!-- ************* Tab 2 Start ***********-->
			
            <div id="tabs-02">
            	<div id="linkedset_sites_list">
				<table class="listResults siteTbl" id="siteTbl">
				<thead>
				<tr>
				<th title="Select All / Deselect All">
				<input class="select_all" onclick="CheckAll('#linkedset_sites_list .selection', this.checked);" type="checkbox" value="1" ></th><th class="header">Site Code</th><th class="header">Site Name</th><th class="header">Province</th><th class="header">Responsible Area</th><th class="header">Created Date</th></tr></thead><tbody id="siteTBody">
				
				<tr><td colspan='6' style='text-align: center;'>The list is empty, use the \"Add...\" button to add affected sites.</td></tr>
				<?php
				$addedSites = array();
				$q1 = "SELECT S.site_id,S.site_name,S.responsible_area,S.created_date,S.site_code,P.province FROM ntsites S LEFT JOIN ntsiteprovince P ON P.province_id=S.province WHERE S.parent_site=".$_GET['id']." AND S.is_active = 1";
				$result1 = $conf->query($q1);
				if($result1){
				if($result1->num_rows>0){
				while($aDBInfo = mysqli_fetch_array($result1,MYSQLI_ASSOC)){

				echo "<tr class='tr_".$aDBInfo['site_id']."'><td><input class=\"selection sitemaster\" data-remote-id=\"".$aDBInfo['site_id']."\" data-link-id=\"\" data-unique-id=\"".$i."\" type=\"checkbox\" value=\"".$aDBInfo['site_id']."\" name=\"removesites[]\"> <input type='hidden' name='sites[]' value='".$aDBInfo['site_id']."'> </td><td><a href='javascript:void(0)' class='siteDetails' id='".$aDBInfo['site_id']."'>".$aDBInfo['site_code']."</td><td>".$aDBInfo['site_name']."</td><td>".$aDBInfo['province']."</td><td>".$aDBInfo['responsible_area']."</td><td>".date('d M Y (h:i a)',strtotime($aDBInfo['created_date']))."</td></tr>";

				array_push($addedSites, $aDBInfo['site_id']);
				}
				}else{
				echo "<tr><td colspan='6' style='text-align: center;'>The list is empty, use the \"Add...\" button to add affected sites.</td></tr>";
				}
				}
				?>
				</tbody>
				</table>
				</div>&nbsp;&nbsp;&nbsp;
				<input id='affectedsite_list_btnRemove' type='button' value='Remove selected objects' disabled='disabled'>&nbsp;&nbsp;&nbsp;
				<input id="affsite_list_btnAdd" type="button" value="Add Affected Site...">
				

				<div id="addAffectedComponentDialog" class="modal">
	<h1 style="display: contents!important;">Add Affected Site </h1>
				<div id="linkedset_main_sites_list">
				<input type="hidden" id="2_main_sites_list" value="[]">
				<input type="hidden" name="attr_main_sites_list" value="">
				
				<div id="linkedset_main_sites_list">
				<table class="listResults siteTbl" id="siteTblAdd" style="min-width: 900px;">
				<thead>
				<tr>
				<th title="Select All / Deselect All">
				<input class="select_all aftsitechk" onclick="CheckAll('#linkedset_main_sites_list .selection', this.checked);" type="checkbox" value="1" >
				</th>
				<th class="header">Site Code</th>
				<th class="header">Site Name</th>
				<th class="header">Province</th>
				<th class="header">Responsible Area</th>
				<th class="header">Created Date</th>
				</tr>
				</thead>
				<tbody id="mainSiteTBody">
				<?php

				$q2 = "SELECT * FROM ntsites LEFT JOIN ntsiteprovince ON ntsites.province=ntsiteprovince.province_id WHERE ntsites.is_active = 1 ORDER BY ntsites.created_date DESC";
				$result2 = $conf->query($q2);
				if($result2){
				if($result2->num_rows>0){
				$i = 0;
				while ($aDBInfo = mysqli_fetch_array($result2,MYSQLI_ASSOC)) {
				//$selected = "";
				$selected= in_array($aDBInfo['site_id'],$addedSites)? 'selected="selected"':'';
				echo "<tr><td><input class=\"selection aftsitechk\" data-remote-id=\"".$aDBInfo['site_id']."\" data-link-id=\"\" data-unique-id=\"".$i."\" type=\"checkbox\" value=\"".$aDBInfo['site_id']."\" name=\"allsites[]\" ".$selected."> </td><td><a href='javascript:void(0)' class='siteDetails' id='".$aDBInfo['site_id']."'>".$aDBInfo['site_code']."</td><td>".$aDBInfo['site_name']."</td><td>".$aDBInfo['province']."</td><td>".$aDBInfo['responsible_area']."</td><td>".date('d M Y (h:i a)',strtotime($aDBInfo['created_date']))."</td></tr>";
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
				</div>
				<input type="button" value="Cancel" onclick="$('#addAffectedComponentDialog').dialog('close');">
				<input id="btn_ok_aftsite_list" disabled="disabled" type="button" value="Add">
				</div>

			</div>
<!-- ***************** Tab 2 End ************** -->

				
			</div> <!-- ***************** Whole Tab End ************** -->
				
		</form>
	</div>
</div>

<!-- ***************** Start Activity tab script ********************-->

<script>
$(".newFunctionalCI").on("click",function(){
  
        $( "#editFunctionalCIDialog" ).dialog();
    });
</script>
<script>
$(".newFunctionalCIma").on("click",function(){
  
        $( "#editFunctionalCIDialogma" ).dialog();
    });
</script>
<script>
$(".newFunctionalCIcy").on("click",function(){
  
        $( "#editFunctionalCIDialogcy" ).dialog();
    });
</script>
<script>
$(".newFunctionalCIloc").on("click",function(){
  
        $( "#editFunctionalCIDialogloc" ).dialog();
    });
</script>
<script>
$(".newFunctionalCIpro").on("click",function(){
  
        $( "#editFunctionalCIDialogpro" ).dialog();
    });
</script>


<script>
	function addservice(){
	var servicep=$("#servicep").val();
	if(servicep	==''){
		 alert('Please fill all mandatory fields');
	}else{
		$.ajax({
         type: 'POST',
         url: 'addservice.php',
         dataType: "json",
         data: JSON.stringify({'servicep': servicep }),
         success: function (data) {
            
             if(data){
                 alert('Service Already exists');
               $('#editFunctionalCIDialog').dialog('close');
             }else{
                alert('Service Added Successfully');
				$('#select1 option:first').after(`<option selected="selected" value="${servicep}"> 
                ${servicep} 
                </option>`); 
         $('#editFunctionalCIDialog').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>
                                         
<script>
   function addmovicel(){
	var movicelp=$("#movicelp").val();
	if(movicelp==''){
		 alert('Please fill all mandatory fields');
	}else{
        $.ajax({
         type: 'POST',
         url: 'addmovicel.php',
         dataType: "json",
         data: JSON.stringify({'movicelp': movicelp }),
         success: function (data) {
             if(data){
                 alert('Movicel Area Already exists');
               $('#editFunctionalCIDialogma').dialog('close');
             }else{
					alert('Movicel Area Added Successfully');
					$('#selectma1 option:first').after(`<option selected="selected" value="${movicelp}"> 
                    ${movicelp} 
                                  </option>`); 
         $('#editFunctionalCIDialogma').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>
 
 <script>
   function addcompany(){
	var companyp=$("#companyp").val();
	if(companyp==''){
		 alert('Please fill all mandatory fields');
	}else{
				   $.ajax({
         type: 'POST',
         url: 'addcompany.php',
         dataType: "json",
         data: JSON.stringify({'companyp': companyp }),
         success: function (data) {
           if(data){
                 alert('Provider Already exists');
               $('#editFunctionalCIDialogcy').dialog('close');
             }else{
                 alert('Provider Added Successfully');
      $('#selectcy1 option:first').after(`<option selected="selected" value="${companyp}"> 
                                       ${companyp} 
                                  </option>`); 
         $('#editFunctionalCIDialogcy').dialog('close');
    return false;
 }
         }
     });

    }
    }
 </script>
 <script>
   function addlocation(){
	var locationp=$("#locationp").val();
	if(locationp==''){
		 alert('Please fill all mandatory fields');
	}else{
			 $.ajax({
         type: 'POST',
         url: 'addlocation.php',
         dataType: "json",
         data: JSON.stringify({'locationp': locationp }),
         success: function (data) {
           if(data){
                 alert('Location Already exists');
               $('#editFunctionalCIDialogloc').dialog('close');
             }else{
                 alert('Location Added Successfully');
					$('#selectloc1 option:first').after(`<option selected="selected" value="${locationp}"> 
                                       ${locationp} 
                                  </option>`); 
					$('#editFunctionalCIDialogloc').dialog('close');
				return false;
			}
         }
     });

    }
    }
 </script>
 <script>
   function addprovince(){
	var provincep=$("#provincep").val();
	if(provincep==''){
		 alert('Please fill all mandatory fields');
	}else{
			 
               $.ajax({
         type: 'POST',
         url: 'addprovince.php',
         dataType: "json",
         data: JSON.stringify({'provincep': provincep }),
         success: function (data) {
             if(data=='-1'){
                 alert('Province Already exists');
               $('#editFunctionalCIDialogpro').dialog('close');
             }else{
                 alert('Province Added Successfully');
        $('#selectpro1 option:first').after(`<option selected="selected" value="${provincep}"> 
                                       ${provincep} 
                                  </option>`); 
         $('#editFunctionalCIDialogpro').dialog('close');
    return false;
         }
          }
     });
      
    } 
    }
 </script>

 
 <script>
 function resetforms() {
    $(".externals").hide();
    $(".internals").hide();
  document.getElementById("form_add_activity").reset();
}
</script>
<script type="text/javascript">
            $(document).ready(function () {
                $('#site_province').on('change', function () {
                  $('#site_munciple').find('option').not(':first').remove();
                  $('#site_locality').find('option').not(':first').remove();
                  
                    var province_id = $(this).val();
                   // alert(province_id);
                    if (province_id) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajaxdropdown.php',
                            dataType: "html",
                            data: JSON.stringify({'province_id': province_id}),
                            success: function (html) {
                              //console.log(html);
                              $('#site_munciple').html(html);
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
                $('#site_munciple').on('change', function () {
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
                              console.log(html);
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
                  
                  
                    var proid = $(this).val();
					//alert(proid);
                   // var strArray = provider_id.split('_');
				//var proid=strArray[0];
				//var orgid=strArray[1];
                   // alert(provider_id);
                    if (proid) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajaxdropdown.php',
                            dataType: "html",
                            data: JSON.stringify({'provider_id': proid}),
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
		
<script language="javascript" type="text/javascript">
    $(document).ready(function() {
        //$("#tabs").tabs({active: document.tabTest.currentTab.value});
        $("#tabs").tabs();
	$("#tabs > ul").bind("tabsshow", function(event, ui) { 
		window.location.hash = ui.tab.hash;
	})
        /*$('#tabs a').click(function(e) {
            var curTab = $('.ui-tabs-active');
            curTabIndex = curTab.index();
            document.tabTest.currentTab.value =;
        });*/
    });
	
</script>
		
<!-- ***************** end Activity tab script ********************-->

<!--***************** Affected site script *************-->

		
<script type="text/javascript">

	$(document).ready(function(){
		$('#siteTbl').datatable();
	});

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
 