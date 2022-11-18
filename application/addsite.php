<style type="text/css">
	#site_munciple,#site_province,#site_locality{
		width: 210px;
	}
	h1{
		display:none!important;
	}
	/*feild width 20-4-20*/
span.field_input_btn img {
    margin-left: 3px;
}

.ui-layout-content img {
    margin-right: 4px;
}
h2 {
    color: #422462;
    font-weight: bold;
}
</style>
<?php /********** Province ***********/
include('../webservices/wbdb.php');

$postData = file_get_contents("php://input");
$jsonData = json_decode($postData,TRUE);
         
?>
<div id="addSiteModalNew" class=""> 
<div class="table-responsive">

<?php 
	
	/********** Start Parent Site dropdown ***********/
		$parentsiteModule= CMDBSource::QueryToArray("SELECT site_id,site_name FROM ntsites WHERE is_active = 1");
		$parentsite = "<select name='site_province' id='site_province' required><option value=''> -- Select One --</option>";					
		foreach ($parentsiteModule as $aDBInfo) {
			$parentsite .= "<option value='".$aDBInfo['site_id']."'>".$aDBInfo['site_name']."</option>";
		}
		$parentsite .= "</select>";
	/********** End Parent Site dropdown ***********/

  	/********** Province ***********/
		$provinceModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteprovince WHERE is_active = 1");
		$province = "<select name='site_province' id='site_province' required><option value=''> -- Select One --</option>";					
		foreach ($provinceModule as $aDBInfo) {
			$province .= "<option value='".$aDBInfo['province_id']."'>".$aDBInfo['province']."</option>";
		}
		$province .= "</select><span class='field_input_btn sitebtn site_province' style='float:right;padding-top: 10px;' title='Add Province'><img id='mini_add_2_sites' class='cprovince' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_province'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";
	/********** Province for edit munciple ***********/
		 $em_provinceModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteprovince WHERE is_active = 1");
		 $em_province = "<select name='em_site_province' id='em_site_province'><option value=''> -- Select One --</option>";					
		 foreach ($em_provinceModule as $aDBInfo) {
		   $em_province .= "<option value='".$aDBInfo['province_id']."'>".$aDBInfo['province']."</option>";
		 }
		 $em_province .= "</select>";
    /********** Responsible Site ***********/
            
            /********** Province SUb Dropdown for munciple ***********/
						$dprovinceModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteprovince WHERE is_active = 1");
						$dprovince = "<select name='dsite_province' id='dsite_provinces' ><option value=''> -- Select One --</option>";					
						foreach ($dprovinceModule as $aDBInfo) {
							$dprovince .= "<option value='".$aDBInfo['province_id']."'>".$aDBInfo['province']."</option>";
						}
            $dprovince .= "</select>";
            
             /********** Province SUb Dropdown for Locality ***********/
						$lprovinceModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteprovince WHERE is_active = 1");
						$lprovince = "<select name='lsite_provinces' id='lsite_provinces' onchange='getVal(this)'><option value=''> -- Select One --</option>";					
						foreach ($lprovinceModule as $aDBInfo) {
							$lprovince .= "<option value='".$aDBInfo['province_id']."'>".$aDBInfo['province']."</option>";
						}
						$lprovince .= "</select>";
            
             /********** Munciple SUb Dropdown for Locality ***********/
            // $dmuncipleModule = CMDBSource::QueryToArray("SELECT * FROM ntsitemunciple WHERE is_active = 1 ORDER BY munciple DESC");
             $dmunciple = "<select name='dsite_munciple' id='dsite_munciple'><option value=''> -- Select One --</option>";
            //  foreach ($dmuncipleModule as $aDBInfo) {
            //    $dmunciple .= "<option value='".$aDBInfo['munciple']."'>".$aDBInfo['munciple']."</option>";
            //  }
             $dmunciple .= "</select><span class='field_input_btn sitebtn site_munciple' style='float:right;padding-top: 10px;' title='Add Munciple'><img id='mini_add_2_sites' class='cmunciple' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

							/********** Responsible Site ***********/
						$responsibleModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteresponsible WHERE is_active = 1 ORDER BY responsible_area DESC");
						
            $responsible = "<select name='site_responsible' id='site_responsible'><option value=''> -- Select One --</option>";					
						foreach ($responsibleModule as $aDBInfo) {
							$responsible .= "<option value='".$aDBInfo['responsible_area']."'>".$aDBInfo['responsible_area']."</option>";
						}
						$responsible .= "</select><span class='field_input_btn sitebtn site_responsible' style='float:right;padding-top: 10px;' title='Add New Responsible Area'><img id='mini_add_2_sites' class='cresparea' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_responsible'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";
             /********** End Responsible Site ***********/
             	
						/********** Priority Site ***********/
						$priorityModule = CMDBSource::QueryToArray("SELECT * FROM ntsitepriority WHERE is_active = 1 ORDER BY priority DESC");
						$priority = "<select name='site_priority' id='site_priority'><option value=''> -- Select One --</option>";					
						foreach ($priorityModule as $aDBInfo) {
							$priority .= "<option value='".$aDBInfo['priority']."'>".$aDBInfo['priority']."</option>";
						}
						$priority .= "</select><span class='field_input_btn sitebtn site_priority' style='float:right;padding-top: 10px;' title='Add New Priority'><img id='mini_add_2_sites' class='cpriority' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_priority'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";
            /********** End of Priority ***********/

            

            /********** Munciple Site ***********/
						//$muncipleModule = CMDBSource::QueryToArray("SELECT * FROM ntsitemunciple WHERE is_active = 1 ORDER BY munciple DESC");
						$munciple = "<select name='site_munciple' id='site_munciple' required><option value=''> -- Select One --</option>";
						// foreach ($muncipleModule as $aDBInfo) {
						// 	$munciple .= "<option value='".$aDBInfo['munciple']."'>".$aDBInfo['munciple']."</option>";
						// }
						$munciple .= "</select><span class='field_input_btn sitebtn site_munciple' style='float:right;padding-top: 10px;' title='Add Munciple'><img src='../images/indicator.gif' style='float: right;padding-top: 3px;display:none' id='muncipleLoad'><img id='mini_add_2_sites' class='cmunciple' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_munciple'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";


            /********** Locality Site ***********/
						$locality = "<select name='site_locality' id='site_locality' required><option value=''> -- Select One --</option>";
						/*foreach ($localityModule as $aDBInfo) {
							$locality .= "<option value='".$aDBInfo['locationname']."'>".$aDBInfo['locationname']."</option>";
						}*/
						$locality .= "</select><span class='field_input_btn sitebtn site_locality' style='float:right;padding-top: 10px;' title='Add Locality'><img src='../images/indicator.gif' style='float: right;padding-top: 3px;display:none' id='localityLoad'><img id='mini_add_2_sites' class='clocality' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_locality'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";
          
            /********** Element Type Site ***********/
						$elementTypeModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteelementtype WHERE is_active = 1 ORDER BY element_type DESC");
						$elementType = "<select name='site_element_type' id='site_element_type'><option value=''> -- Select One --</option>";					
						foreach ($elementTypeModule as $aDBInfo) {
							$elementType .= "<option value='".$aDBInfo['element_type']."'>".$aDBInfo['element_type']."</option>";
						}
						$elementType .= "</select><span class='field_input_btn sitebtn site_element_type' style='float:right;padding-top: 10px;' title='Add Element Type'><img id='mini_add_2_sites' class='celementtype' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_element_type'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";
            /********** End Element Type Site ***********/
           
						/********** Vendor Site ***********/
						$vendorModule = CMDBSource::QueryToArray("SELECT * FROM ntsitevendor WHERE is_active = 1 ORDER BY vendor DESC");
						$vendor = "<select name='site_vendor' id='site_vendor' required=''><option value=''> -- Select One --</option>";					
						foreach ($vendorModule as $aDBInfo) {
							$vendor .= "<option value='".$aDBInfo['vendor']."'>".$aDBInfo['vendor']."</option>";
						}
						$vendor .= "</select><span class='field_input_btn sitebtn site_vendor' style='float:right;padding-top: 10px;' title='Add Vendor'><img id='mini_add_2_sites' class='cvendor' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_vendor'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";

						/********** Model Site ***********/
						$modelModule = CMDBSource::QueryToArray("SELECT * FROM ntsitemodel WHERE is_active = 1 ORDER BY model DESC");
						$model = "<select name='site_model' id='site_model'><option value=''> -- Select One --</option>";					
						foreach ($modelModule as $aDBInfo) {
							$model .= "<option value='".$aDBInfo['model']."'>".$aDBInfo['model']."</option>";
						}
						$model .= "</select><span class='field_input_btn sitebtn site_model' style='float:right;padding-top: 10px;' title='Add Model'><img id='mini_add_2_sites' class='cmodel' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_model'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";

						/********** MSC Site ***********/
						$mscModule = CMDBSource::QueryToArray("SELECT * FROM ntsitemsc WHERE is_active = 1 ORDER BY msc DESC");
						$msc = "<select name='site_msc' id='site_msc'><option value=''> -- Select One --</option>";					
						foreach ($mscModule as $aDBInfo) {
							$msc .= "<option value='".$aDBInfo['msc']."'>".$aDBInfo['msc']."</option>";
						}
						$msc .= "</select><span class='field_input_btn sitebtn e_site_msc' style='float:right;padding-top: 10px;' title='Add New Priority'><img id='mini_add_2_sites' class='cmsc' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_msc'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";
            /**********End  MSC Site ***********/
            
						/********** MGW Site ***********/
						$mgwModule = CMDBSource::QueryToArray("SELECT * FROM ntsitemgw WHERE is_active = 1 ORDER BY mgw DESC");
						$mgw = "<select name='site_mgw' id='site_mgw'><option value=''> -- Select One --</option>";					
						foreach ($mgwModule as $aDBInfo) {
							$mgw .= "<option value='".$aDBInfo['mgw']."'>".$aDBInfo['mgw']."</option>";
						}
						$mgw .= "</select><span class='field_input_btn sitebtn site_mgw' style='float:right;padding-top: 10px;' title='Add MGW'><img id='mini_add_2_sites' class='cmgw' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_mgw'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";

						/********** BSC Site ***********/
						$bscModule = CMDBSource::QueryToArray("SELECT * FROM ntsitebsc WHERE is_active = 1 ORDER BY bsc DESC");
						$bsc = "<select name='site_bsc' id='site_bsc'><option value=''> -- Select One --</option>";					
						foreach ($bscModule as $aDBInfo) {
							$bsc .= "<option value='".$aDBInfo['bsc']."'>".$aDBInfo['bsc']."</option>";
						}
						$bsc .= "</select><span class='field_input_btn sitebtn site_bsc' style='float:right;padding-top: 10px;' title='Add BSC'><img id='mini_add_2_sites' class='cbsc' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_bsc'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";
           /********** RNC Site ***********/
           $rncModule = CMDBSource::QueryToArray("SELECT * FROM ntsiternc WHERE is_active = 1 ORDER BY rnc DESC");
           $rnc = "<select name='site_rnc' id='site_rnc'><option value=''> -- Select One --</option>";
           foreach ($rncModule as $aDBInfo) {
           $rnc .= "<option value='".$aDBInfo['rnc']."'>".$aDBInfo['rnc']."</option>";
           }
           $rnc .= "</select><span class='field_input_btn sitebtn site_rnc' style='float:right;padding-top: 10px;' title='Add RNC'><img style='border:0;vertical-align:middle;cursor:pointer;' class='crnc' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_rnc'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";
						/********** Phase Site ***********/
						$phaseModule = CMDBSource::QueryToArray("SELECT * FROM ntsitephase WHERE is_active = 1 ORDER BY phase DESC");
						$phase = "<select name='site_phase' id='site_phase'><option value=''> -- Select One --</option>";					
						foreach ($phaseModule as $aDBInfo) {
							$phase .= "<option value='".$aDBInfo['phase']."'>".$aDBInfo['phase']."</option>";
						}
						$phase .= "</select><span class='field_input_btn sitebtn site_phase' style='float:right;padding-top: 10px;' title='Add Phase'><img id='mini_add_2_sites' class='cphase' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_phase'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";					

             
						/********** Stage Site ***********/
						$stageModule = CMDBSource::QueryToArray("SELECT * FROM ntsitestage WHERE is_active = 1 ORDER BY stage DESC");
						$stage = "<select name='site_stage' id='site_stage'><option value=''> -- Select One --</option>";					
						foreach ($stageModule as $aDBInfo) {
							$stage .= "<option value='".$aDBInfo['stage']."'>".$aDBInfo['stage']."</option>";
						}
						$stage .= "</select><span class='field_input_btn sitebtn site_stage' style='float:right;padding-top: 10px;' title='Add Stage'><img id='mini_add_2_sites' class='cstage' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_stage'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";

						/********** Sub Stage Site ***********/
						$subStageModule = CMDBSource::QueryToArray("SELECT * FROM ntsitesubstage WHERE is_active = 1 ORDER BY sub_stage DESC");
						$subStage = "<select name='site_sub_stage' id='site_sub_stage'><option value=''> -- Select One --</option>";					
						foreach ($subStageModule as $aDBInfo) {
							$subStage .= "<option value='".$aDBInfo['sub_stage']."'>".$aDBInfo['sub_stage']."</option>";
						}
						$subStage .= "</select><span class='field_input_btn sitebtn site_sub_stage' style='float:right;padding-top: 10px;' title='Add Sub Stage'><img id='mini_add_2_sites' class='csubstage' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span><span class='field_input_btn edit_site_substage'><img id='mini_edit_brand_id' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/wrench.png?t=1538568981.6184'></span>";
                
            
            //*****Edit Dropdown*************************************************************************************************
            /********** Edit Province ***********/
						$e_provinceModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteprovince WHERE is_active = 1");
						$e_province = "<select name='e_site_province' id='e_site_province' onchange='get_edit_site_province(this.value);'><option value=''> -- Select One --</option>";					
						foreach ($e_provinceModule as $aDBInfo) {
							$e_province .= "<option value='".$aDBInfo['province_id']."'>".$aDBInfo['province']."</option>";
						}
						$e_province .= "</select>";
            /**********End of Edit Province ***********/
            //********Start of Edit Munciple Dropdown********
            
						$e_munciple = "<select name='e_site_munciple' id='e_site_munciple'><option value='' onchange='get_edit_site_munciple(this.value);'> -- Select One --</option>";
						$e_munciple .= "</select>";
            //********End of Edit Muncoiple Dropdown********
            
            /********** Edit Responsible Site ***********/
						$e_responsibleModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteresponsible WHERE is_active = 1 ORDER BY responsible_area DESC");
						
            $e_responsible = "<select name='e_site_responsible' id='e_site_responsible' onchange='get_edit_site_responsible(this.value);'><option value=''> -- Select One --</option>";					
						foreach ($e_responsibleModule as $aDBInfo) {
							$e_responsible .= "<option value='".$aDBInfo['responsible_area']."'>".$aDBInfo['responsible_area']."</option>";
						}
						$e_responsible .= "</select>";
             /********** End of Edit Responsible Site ***********/
             	/********** Edit Priority Site ***********/
						$e_priorityModule = CMDBSource::QueryToArray("SELECT * FROM ntsitepriority WHERE is_active = 1 ORDER BY priority DESC");
						$e_priority = "<select name='e_site_priority' id='e_site_priority' onchange='get_edit_site_priority(this.value);'><option value=''> -- Select One --</option>";					
						foreach ($e_priorityModule as $aDBInfo) {
							$e_priority .= "<option value='".$aDBInfo['priority']."'>".$aDBInfo['priority']."</option>";
						}
						$e_priority .= "</select>";
            /********** End of Edit Priority ***********/
             /**********Edit Element Type Site ***********/
						$e_elementTypeModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteelementtype WHERE is_active = 1 ORDER BY element_type DESC");
						$e_elementType = "<select name='e_site_element_type' id='e_site_element_type' onchange='get_edit_site_element_type(this.value);'><option value=''> -- Select One --</option>";					
						foreach ($e_elementTypeModule as $aDBInfo) {
							$e_elementType .= "<option value='".$aDBInfo['element_type']."'>".$aDBInfo['element_type']."</option>";
						}
						$e_elementType .= "</select>";
            /********** End Edit Element Type Site ***********/
            /**********Edit Vendor Site ***********/
						$e_vendorModule = CMDBSource::QueryToArray("SELECT * FROM ntsitevendor WHERE is_active = 1 ORDER BY vendor DESC");
						$e_vendor = "<select name='e_site_vendor' id='e_site_vendor' required='' onchange='get_edit_site_vendor(this.value);'><option value=''> -- Select One --</option>";					
						foreach ($e_vendorModule as $aDBInfo) {
							$e_vendor .= "<option value='".$aDBInfo['vendor']."'>".$aDBInfo['vendor']."</option>";
						}
						$e_vendor .= "</select>";
          /********** End Edit Vendor ***********/  
          	/**********Edit Model Site ***********/
						$e_modelModule = CMDBSource::QueryToArray("SELECT * FROM ntsitemodel WHERE is_active = 1 ORDER BY model DESC");
						$e_model = "<select name='e_site_model' id='e_site_model' onchange='get_edit_site_model(this.value);'><option value=''> -- Select One --</option>";					
						foreach ($e_modelModule as $aDBInfo) {
							$e_model .= "<option value='".$aDBInfo['model']."'>".$aDBInfo['model']."</option>";
						}
						$e_model .= "</select>";                      
              /********** End Edit MOdel ***********/       
              	/**********Edit MSC Site ***********/
						$e_mscModule = CMDBSource::QueryToArray("SELECT * FROM ntsitemsc WHERE is_active = 1 ORDER BY msc DESC");
						$e_msc = "<select name='e_site_msc' id='e_site_msc' onchange='get_edit_site_msc(this.value);'><option value=''> -- Select One --</option>";					
						foreach ($e_mscModule as $aDBInfo) {
							$e_msc .= "<option value='".$aDBInfo['msc']."'>".$aDBInfo['msc']."</option>";
						}
						$e_msc .= "</select>";
            /**********End Edit MSC Site ***********/    
            /**********Edit MGW Site ***********/
						$e_mgwModule = CMDBSource::QueryToArray("SELECT * FROM ntsitemgw WHERE is_active = 1 ORDER BY mgw DESC");
						$e_mgw = "<select name='e_site_mgw' id='e_site_mgw' onchange='get_edit_site_mgw(this.value);'><option value=''> -- Select One --</option>";					
						foreach ($e_mgwModule as $aDBInfo) {
							$e_mgw .= "<option value='".$aDBInfo['mgw']."'>".$aDBInfo['mgw']."</option>";
						}
						$e_mgw .= "</select>";
            /**********End Edit MGW Site ***********/     
            	/**********Start Edit BSC Site ***********/
						$e_bscModule = CMDBSource::QueryToArray("SELECT * FROM ntsitebsc WHERE is_active = 1 ORDER BY bsc DESC");
						$e_bsc = "<select name='e_site_bsc' id='e_site_bsc' onchange='get_edit_site_bsc(this.value);'><option value=''> -- Select One --</option>";					
						foreach ($e_bscModule as $aDBInfo) {
							$e_bsc .= "<option value='".$aDBInfo['bsc']."'>".$aDBInfo['bsc']."</option>";
						}
						$e_bsc .= "</select>";                       
                    /********** End Edit BSC Site ***********/
                     /**********Start Edit RNC Site ***********/
           $e_rncModule = CMDBSource::QueryToArray("SELECT * FROM ntsiternc WHERE is_active = 1 ORDER BY rnc DESC");
           $e_rnc = "<select name='e_site_rnc' id='e_site_rnc' onchange='get_edit_site_rnc(this.value);'><option value=''> -- Select One --</option>";
           foreach ($e_rncModule as $aDBInfo) {
           $e_rnc .= "<option value='".$aDBInfo['rnc']."'>".$aDBInfo['rnc']."</option>";
           }
           $e_rnc .= "</select>";
                   /**********End Edit RNC Site ***********/
                   	/********** Start Phase Site ***********/
						$e_phaseModule = CMDBSource::QueryToArray("SELECT * FROM ntsitephase WHERE is_active = 1 ORDER BY phase DESC");
						$e_phase = "<select name='e_site_phase' id='e_site_phase' onchange='get_edit_site_phase(this.value);'><option value=''> -- Select One --</option>";					
						foreach ($e_phaseModule as $aDBInfo) {
							$e_phase .= "<option value='".$aDBInfo['phase']."'>".$aDBInfo['phase']."</option>";
						}
						$e_phase .= "</select>";					
  /**********End Edit Phase Site ***********/
  /********** Start Edit Stage Site ***********/
  $e_stageModule = CMDBSource::QueryToArray("SELECT * FROM ntsitestage WHERE is_active = 1 ORDER BY stage DESC");
  $e_stage = "<select name='e_site_stage' id='e_site_stage' onchange='get_edit_site_stage(this.value);'><option value=''> -- Select One --</option>";					
  foreach ($e_stageModule as $aDBInfo) {
    $e_stage .= "<option value='".$aDBInfo['stage']."'>".$aDBInfo['stage']."</option>";
  }
  $e_stage .= "</select>";
  /********** End Edit Stage Site ***********/
  	/********** Start Edit Sub Stage Site ***********/
    $e_subStageModule = CMDBSource::QueryToArray("SELECT * FROM ntsitesubstage WHERE is_active = 1 ORDER BY sub_stage DESC");
    $e_subStage = "<select name='e_site_sub_stage' id='e_site_sub_stage' onchange='get_edit_site_substage(this.value);'><option value=''> -- Select One --</option>";					
    foreach ($e_subStageModule as $aDBInfo) {
      $e_subStage .= "<option value='".$aDBInfo['sub_stage']."'>".$aDBInfo['sub_stage']."</option>";
    }
    $e_subStage .= "</select>";
    	/**********End Edit Sub Stage Site ***********/
                     
                       /********** Start Munciple Site for edit Munciple ***********/
						$em_munciple = "<select name='em_site_munciple' id='em_site_munciple' onchange='get_edit_site_munciple(this.value);'><option value=''> -- Select One --</option>";
						$em_munciple .= "</select>";
                    /********** End Munciple Site for edit Munciple ***********/        
                    //*********Edit Locality******** */
                     /********** Province SUb Dropdown for Edit Locality ***********/
						$el_provinceModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteprovince WHERE is_active = 1");
						$el_province = "<select name='elsite_province' id='elsite_province'><option value=''> -- Select One --</option>";					
						foreach ($el_provinceModule as $aDBInfo) {
							$el_province .= "<option value='".$aDBInfo['province_id']."'>".$aDBInfo['province']."</option>";
						}
						$el_province .= "</select>";
            
             /********** Munciple SUb Dropdown for Edit Locality ***********/
             $el_munciple = "<select name='elsite_munciple' id='elsite_munciple'><option value=''> -- Select One --</option>";  
             $el_munciple .= "</select>";         
              /********** Edit Locality Site ***********/
						$el_locality = "<select name='elsite_locality' id='elsite_locality' onchange='get_edit_site_locality(this.value);'><option value=''> -- Select One --</option>";
						$el_locality .= "</select>";  
                    //**********End of Edit Locality******** */                                   
                    ?>
<script language="javascript" type="text/javascript">
    $(document).ready(function() {
        $("#tabs").tabs();
        $("#tabs > ul").bind("tabsshow", function(event, ui) { 
            window.location.hash = ui.tab.hash;
        });
    });
</script>

<div class="ui-layout-content" style="overflow: auto; position: relative; visibility: visible;">
  	<h2 style="font-family: Tahoma, Verdana, Arial, Helvetica!important;
	    color: #422462!important;font-weight: bold!important;font-size: 12pt!important;">
  	<img src="https://nt3.nectarinfotel.com/images/addactivity.png" style="vertical-align:middle;width: 32px;">&nbsp;
	<?php echo ($jsonData['language']=='PT BR')? 'Novo Site':'New Site' ?></h2>

<div class="wizContainer">
	<span style='float:right;margin-top: 14px;'><a href="https://nt3.nectarinfotel.com/CSV/importcsvformat.csv">
	<?php echo ($jsonData['language']=='PT BR')? "Clique </a> aqui para baixar o arquivo de amostra
":"Click Here </a> to download sample file" ?> </span>

    <img src='../images/indicator.gif' class="importUpload" style="display: none;float: right;padding: 15px 0px 0px 10px;">
  	<form class="form-horizontal" method="post" name="uploadCSV"
	    enctype="multipart/form-data" id="uploadCSV">
	    <div class="input-row" style="float: right;">
	        <label class="col-md-4 control-label"><?php echo ($jsonData['language']=='PT BR')? 'Escolha arquivo CSV':'Choose CSV File' ?>
 			</label> <input
	            type="file" name="site_file" id="site_file" accept=".csv" required>
<button type="submit" id="uploadCSVSubmit" name="import" class="btn-submit" 
style="padding: 5px 23px 5px 23px;border: none;border-radius: 3px;background-color: #f17422;color: #ffffff;cursor: pointer;">
<?php echo ($jsonData['language']=='PT BR')? 'Importar':'Import' ?></button>
	        <br/>
	    </div>
	    <div id="labelError"></div>
	</form>
	<script>
		$("#uploadCSV").on('submit',function(event){
			event.preventDefault();
			var formData = new FormData();
			var fileInput = document.querySelector('form input[type=file]');
			formData.append("import", true);
			formData.append("site_file", fileInput.files[0]);
			$("#site_file").attr('disabled',true);
			$("#uploadCSVSubmit").attr('disabled',true);
			$(".importUpload").css('display','block');
			$.ajax({
    			url: "importsitecsv.php",
    			type: "POST",
    			data: formData,
    			contentType: false,
					processData: false,
    			dataType: "json",
    			success: function(res){
    				/*console.log(res);
    				if(res.flag){

    				}*/
    				$("#site_file").removeAttr('disabled');
    				$("#uploadCSVSubmit").removeAttr('disabled');
    				$(".importUpload").css('display','none');
					alert(res.msg);
    			}
    		});
		});
		
	</script>   
	<br><br>

<form class="siteAdd" action="addsites.php" method="POST">
<div id="tabs">
<ul>
    <li><a href="#tabs-01"><?php echo ($jsonData['language']=='PT BR')? 'Novo Site':'New Site' ?></a></li>
    <li><a href="#tabs-02"><?php echo ($jsonData['language']=='PT BR')? 'Agregados Site':'Aggregate Sites' ?></a></li>
</ul>

<div id="tabs-01">
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
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Identificação de site':'Site ID ' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
	<input type="text" name="site_id" id="site_id" style="width:210px;" required="">

	<img src="../images/validation_error.png" style="vertical-align:top;margin-left: 5px;" title="Please specify a value">
	
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Nome do site':'Site Name' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
	 <span style="float:left">
	 	<input type="text" name="site_name" id="site_name" style="width:210px;" required="">
	 	<img src="../images/validation_error.png" style="vertical-align:top;" title="Please specify a value">
	 </span>
	
	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Rede':'Network' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">

<span style="float:left;padding-right: 57px;">
	<input type="checkbox" name="site_network[]" class="site_network" value="2G" id="2Gnetwork"> <label for="2Gnetwork"> 2G </label>
	<input type="checkbox" name="site_network[]" class="site_network" value="3G" id="3Gnetwork"> <label for="3Gnetwork"> 3G </label>
	<input type="checkbox" name="site_network[]" class="site_network" value="4G" id="4Gnetwork"> <label for="4Gnetwork"> 4G </label>
	</span>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title=""><label><?php echo ($jsonData['language']=='PT BR')? 'Área responsável':'Responsible area' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">

<span style="float:left;"><?php echo $responsible ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Prioridade':'Priority' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;"><?php echo $priority ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label" style="width: 32%;"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Comentário prioritário':'Priority Comment' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left">
	<textarea name="site_priority_comment" id="site_priority_comment" style="width:210px;border: 1px solid #e1e1e1;"></textarea>
	</span>	
	</div>
</div>
</div></div></div>
</div>
</div>
</fieldset>

<fieldset>
<legend><?php echo ($jsonData['language']=='PT BR')? 'Modelo':'Model' ?></legend>
<div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Tipo de elemento':'Element Type' ?>  : </span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
	<span style="float:left;"><?php echo $elementType ?> </span>
</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Fornecedor':'Vendor' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;"><?php echo $vendor ?></span>
<img src="../images/validation_error.png" style="vertical-align:top;" title="Please specify a value">
</div>
</div>
</div></div></div>
</div>
</div>

<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Modelo':'Model' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
	<span style="float:left;"><?php echo $model ?></span>	
	</div>
</div>
</div></div></div>
</div>
</div>
</fieldset>
<fieldset>
<legend><?php echo ($jsonData['language']=='PT BR')? 'Dependência':'Dependency' ?></legend>
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
<span style="float:left;"><?php echo $mgw ?></span>
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

<div class="field_container field_small">
<div class="field_label label"><span title="">RNC :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;"><?php echo $rnc ?></span>
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
<legend><?php echo ($jsonData['language']=='PT BR')? 'Localização':'Localization' ?></legend>
<div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
 <span style="float:left"><?php echo $province ?></span>
 <img src="../images/validation_error.png" style="vertical-align:top;margin-left: 5px;" title="Please specify a value">
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Municipal':'Municipal' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
 <div class="field_input_zone field_input_string">
	<span style="float:left">
								 <?php echo $munciple ?>
	</span> 
	<img src="../images/validation_error.png" style="vertical-align:top;margin-left: 5px;" title="Please specify a value">
					            </div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Localidade':'Locality' ?>  :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
 <div class="field_input_zone field_input_string">
						           	<span style="float:left">
								 <?php echo $locality ?>
								    </span> 
								    <img src="../images/validation_error.png" style="vertical-align:top;margin-left: 5px;" title="Please specify a value">
					            </div>
</div></div></div>
</div>
</div>
<!-- <div class="field_container field_small">
<div class="field_label label"><span title="">Locality :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left">
	<input type="text" name="site_locality" id="site_locality" style="width:225px;">
</span>
	</div>
</div>
</div></div></div>
</div>
</div> -->
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Latitude':'Latitude' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left">
	<input type="text" name="site_lat" id="site_lat" style="width:210px;">
</span>
	</div>
</div>
</div></div></div>
</div>
</div>

<div class="field_container field_small" style="margin-bottom: 27px;">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Longitude':'Longitude' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left">
	<input type="text" name="site_lng" id="site_lng" style="width:210px;">
</span>	
	</div>
</div>
</div></div></div>
</div>
</div>
</fieldset>


<fieldset style="padding-bottom: 45px;">
<legend><?php echo ($jsonData['language']=='PT BR')? 'Planejamento':'Planning' ?></legend>
<div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Estágio':'Phase' ?> :</span></div>
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
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Data de serviço':'Service Date' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;">
	<input type="date" name="site_service_date" id="site_service_date" style="width:210px;">
		
											    	</span>
	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Etapa':'Stage' ?> : </span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;"><?php echo $stage ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Subestágio':'Sub Stage' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;"><?php echo $subStage ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Data de início':'Start Date' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;">
	<input type="date" name="site_start_date" id="site_start_date" style="width:210px;">
										    		</span>
	</div>
</div>
</div></div></div>
</div>
</div>

<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Data final':'End Date' ?>:</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">
<span style="float:left;">
	<input type="date" name="site_end_date" id="site_end_date" style="width:210px;">
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
								    		
</div>
<!-- <button type="button" class="action cancel" onclick="window.location.href='https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite'">Cancel</button>
<input type="submit" class="createSite modifysitebtn" value="<?php //echo ($jsonData['language']=='PT BR')? 'Criar':'Create' ?>"> -->
<br><br>
</div> 
<!-- Div tab-01 close -->

<div id="tabs-02">
	
	<input type="hidden" name="psiteid" value="">
	<table class="listResults siteTbl" id="siteTbl"><thead><tr>
	<th title="Select All / Deselect All">
	<input class="select_all" onclick="CheckAll(\'#linkedset_sites_list .selection\', this.checked); oWidget2_functionalcis_list.OnSelectChange();" type="checkbox" value="1" ></th>
	<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Código do site':'Site Code' ?></th>
	<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Nome do site':'Site Name' ?></th>
	<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?></th>
	<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Área Responsável':'Responsible Area' ?></th>
	<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Data de criação':'Created Date' ?></th>
	</tr></thead><tbody id="siteTBody">
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
				echo "<tr><td colspan='6' style='text-align: center;'>The list is empty, use the \"Add...\" button to add aggregate sites.</td></tr>";
			}
		}
	?>
	</tbody></table>

&nbsp;&nbsp;&nbsp;<input id='affectedsite_list_btnRemove' type='button' value='<?php echo ($jsonData['language']=='PT BR')? 'Remover objetos selecionados':'Remove selected objects' ?>' disabled='disabled'>&nbsp;&nbsp;&nbsp;
<input id="affsite_list_btnAdd" type="button" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar Site Agregados...':'Add Site Aggregates...' ?>"></div></div>

	<div id="addAffectedComponentDialog" class="modal">

  	<h1 style="display: contents!important;">
	<?php echo ($jsonData['language']=='PT BR')? 'Adicionar site agregado':'Add Aggregate Site' ?> </h1>
	<div id="linkedset_main_sites_list"><input type="hidden" id="2_main_sites_list" value="[]">
	<input type="hidden" name="attr_main_sites_list" value="">
  	<table class="listResults siteTbl" id="siteTblAdd" style="min-width: 900px;"><thead><tr>
	<th title="Select All / Deselect All">
	<input class="select_all aftsitechk" onclick="CheckAll(\'#linkedset_main_sites_list .selection\', this.checked); oWidget2_functionalcis_list.OnSelectChange();" type="checkbox" value="1" ></th>
	<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Código do site':'Site Code' ?></th>
	<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Nome do site':'Site Name' ?></th>
	<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?></th>
	<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Área Responsável':'Responsible Area' ?></th>
	<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Data de criação':'Created Date' ?></th>
	</tr></thead><tbody id="mainSiteTBody">
  	<?php

		$q2 = "SELECT ntsites.*,ntsites.created_date as created_date,ntsiteprovince.province as province FROM ntsites LEFT JOIN ntsiteprovince ON ntsites.province=ntsiteprovince.province_id WHERE ntsites.is_active = 1 ORDER BY ntsites.created_date DESC";
		$result2 = $conf->query($q2);
		if($result2){
			if($result2->num_rows>0){
				$i = 0;
				while ($aDBInfo = mysqli_fetch_array($result2,MYSQLI_ASSOC)) {
					//$selected = "";
					$selected= in_array($aDBInfo['site_id'], $addedSites)? 'selected="selected"':'';
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

<input type="button" value="<?php echo ($jsonData['language']=='PT BR')? 'Cancelar':'Cancel' ?>" onclick="$('#addAffectedComponentDialog').dialog('close');">
<input id="btn_ok_aftsite_list" disabled="disabled" type="button" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>">

</div>
<!--  Tab 2 End -->
<br>
<button type="button" class="action cancel" onclick="window.location.href='https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite'"><?php echo ($jsonData['language']=='PT BR')? 'Cancelar':'Cancel' ?></button>
<input type="submit" class="createSite modifysitebtn" value="<?php echo ($jsonData['language']=='PT BR')? 'Criar':'Create' ?>" style="padding: 5px 23px 5px 23px;">

</form>
</div> <!-- Div Main tab close -->



</div>						
</div>
</div>

   
<script type="text/javascript">
	
	$(document).on("submit",".siteAdd",function(e){
		e.preventDefault();
		$.ajax({
			url: 'addsites.php',
			data: $(this).serialize(),
			type: 'POST',
			success: function(res){
				if(res==2){
                alert('Site Already Exist');
                //window.location = "https://nt3dg.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite";
            }
            if(res==1){
               
                var answer = confirm("Site Added Successfully");
                if (res){
                   window.location = "https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite";
                }
                else{
                    
                }
                
                }
                else 
                    if(res ==-1)
                    {
                    	alert("Unable To Add Site");
                }
			}
		});
	});

</script>    

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

        
      
<!-- </div> -->
    <br><br><br><br>
       
                                            </div>

		<div id="siteAttrModalNew" class="modal" style="z-index:99999999;">
						<div class="modal-content modelbox">
							<span class="close closeSiteAttrNew closemodel">&times;</span>
								<h2 class="modelh2"><?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?> </h2>
						<div>
						<div id="dropdwn" style="float:left"></div>
							<div id="textData" style="float:left">
								<label style="padding-top: 10px;float: left;text-transform: capitalize;"></label>
									<input type="text" name="attr" id="attr" style="float: left;">
										<span class="form_validation" style="padding-top: 10px;width: 20px;float: left;"><img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
								    	</span>
							</div>

							<input type="button" class="addSiteAttrNew modelsubmitbtn" id="subm" value="<?php echo ($jsonData['language']=='PT BR')? 'Criar':'Create' ?>" style="float: left;" onclick="">
						<br><br><br><br><br>
					</div>
				</div>
			</div>
      <!--Add Province Modal--->
			<div id="modprovince" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar província':'Add Province' ?></h2>

                                          <input type="text" name="province" id="province" onfocus="this.value=''"> 
                                                                            <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                        </span>
                                        <input type="button" onclick="addprovince()" class="cprovince" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                                                            <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                                                            </div>
                                                                                
</div>
	<!-- End Add Province Modal--->
   <!--Start Edit Province Modal--->
   <div id="edit_modprovince" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar província':'Edit Province' ?></h2>
                                        <div class="field_container field_small">
<div class="field_label label"><span title=""> <?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">

<span style="float:left;"><?php echo $e_province ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="eprovince" id="eprovince" onfocus="this.value=''"> 
                                                                            <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                        </span>
                             <input type="button" onclick="editprovince()" class="edit_cprovince updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                         <input type="button" onclick="deleteprovince()" class="edit_cprovince" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                       
                                                                            <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                                                            </div>
                                                                                
</div>
	<!-- End Edit Province Modal--->
			<!-- Responsible Area Modal--->
			<div id="modresparea" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar área responsável':'Add Responsible Area' ?></h2>

                                          <input type="text" name="resparea" id="resparea" onfocus="this.value=''"> 
                                                                            <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                        </span>
                                        <input type="button" onclick="addresponsibilityarea()" class="cresparea" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                                                            <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                                                            </div>
                                                                                
</div>
	<!-- End Responsible Area Modal--->

	<!-- Edit Responsible Area Modal--->
  <div id="edit_modresparea" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar área responsável':'Edit Responsible Area' ?></h2>
                                        <div class="field_container field_small">
<div class="field_label label" style="min-width: 123px;"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Área responsável':'Responsible area' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">

<span style="float:left;"><?php echo $e_responsible ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="eresparea" id="eresparea" onfocus="this.value=''"> 
                                                                            <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                        </span>
                                        <input type="button" onclick="editresponsibilityarea()" class="edit_site_responsible updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                        <input type="button" onclick="deleteresponsibilityarea()" class="edit_site_responsible deladdsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>">

                                                                            <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                                                            </div>
                                                                                
</div>
	<!-- End of Edit Responsible Area Modal--->

	<!-- Priority Modal--->
	<div id="modpriority" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar prioridade':'Add Priority' ?></h2>

                                          <input type="text" name="priority" id="priority" onfocus="this.value=''"> 
                                                                            <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                        </span>
                                        <input type="button" onclick="addpriority()" class="cpriority" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                                                            <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                                                            </div>
                                                                                
</div>
	<!-- End Priority Modal--->
<!-- Edit Priority Modal--->
	<div id="edit_modpriority" class="modal">
                                       
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar prioridade':'Edit Priority' ?></h2>
                                        <div class="field_container field_small">
<div class="field_label label"><span title=""> <?php echo ($jsonData['language']=='PT BR')? 'Prioridadee':'Priority' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">

<span style="float:left;"><?php echo $e_priority ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="epriority" id="epriority" onfocus="this.value=''"> 
                                                                            <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                        </span>
                                        <input type="button" onclick="editpriority()" class="edit_site_priority updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
 <input type="button" onclick="deletepriority()" class="edit_site_priority deladdsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>">
                                                                            <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                                                            </div>
                                                                                
</div>
	<!-- End Edit Priority Modal--->
	<!-- Element Type Modal--->
	<div id="modelementtype" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar tipo de elemento':'Add Element Type' ?></h2>

                                          <input type="text" name="elementtype" id="elementtype" onfocus="this.value=''"> 
                                                                            <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                        </span>
                                        <input type="button" onclick="addelementtype()" class="celementtype" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                                                            <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                                                            </div>
                                                                                
</div>
	 <!-- End Element Type Modal--->
	 <!-- Edit Element Type Modal--->
	<div id="edit_modelementtype" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar tipo de elemento':'Edit Element Type' ?></h2>
                                        <div class="field_container field_small">
<div class="field_label label" style="min-width: 110px;"><span title=""> <?php echo ($jsonData['language']=='PT BR')? 'Tipo de elemento':'Element Type' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">

<span style="float:left;"><?php echo $e_elementType ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="eelementtype" id="eelementtype" onfocus="this.value=''"> 
                                                                            <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                        </span>
                                        <input type="button" onclick="editelementtype()" class="edit_site_element_type updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                        <input type="button" onclick="deleteelementtype()" class="edit_site_element_type deladdsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>">
                                                                            <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                                                            </div>
                                                                                
</div>
	 <!-- End Edit Element Type Modal--->

	    <!-- Vendor Modal--->
	                                <div id="modvendor" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar fornecedor':'Add Vendor' ?></h2>

                                          <input type="text" name="vendor" id="vendor" onfocus="this.value=''"> 
                                                                            <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                        </span>
                                        <input type="button" onclick="addvendor()" class="cvendor" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Vendor Modal--->
	    <!--Start Edit Vendor Modal--->
     <div id="edit_modvendor" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar fornecedor':'Edit Vendor' ?></h2>
                                      
                                        <div class="field_container field_small">
<div class="field_label label"><span title=""> <?php echo ($jsonData['language']=='PT BR')? 'Fornecedor':'Vendor' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">

<span style="float:left;"><?php echo $e_vendor ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="evendor" id="evendor" onfocus="this.value=''"> 
                                                                            <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                        </span>
                                        <input type="button" onclick="editvendor()" class="edit_site_vendor updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                        <input type="button" onclick="deletevendor()" class="edit_site_vendor deladdsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Edit Vendor Modal--->

	     <!-- model Modal--->
		                     <div id="modmodel" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar modelo':'Add Model' ?></h2>

                                          <input type="text" name="model" id="model" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="addmodel()" class="cmodel" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End model Modal--->
	   <!-- Edit model Modal--->
<div id="edit_modmodel" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar modelo':'Edit Model' ?></h2>
                                        <div class="field_container field_small">
<div class="field_label label"><span title=""> <?php echo ($jsonData['language']=='PT BR')? 'Modelo':'Model' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">

<span style="float:left;"><?php echo $e_model ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="emodel" id="emodel" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="editmodel()" class="edit_site_model updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                        <input type="button" onclick="deletemodel()" class="edit_site_model deladdsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Edit model Modal--->
	   

	    <!-- MSC Modal--->
		<div id="modmsc" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar MSC':'Add MSC' ?></h2>

                                          <input type="text" name="msc" id="msc" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="addmsc()" class="cmsc" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End MSC Modal--->
	    <!--Edit  MSC Modal--->
		<div id="edit_modmsc" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar MSC':'Edit MSC' ?></h2>
                                        <div class="field_container field_small">
<div class="field_label label"><span title=""> MSC :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">

<span style="float:left;"><?php echo $e_msc ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="emsc" id="emsc" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="editmsc()" class="edit_site_msc updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                        <input type="button" onclick="deletemsc()" class="edit_site_msc deladdsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Edit MSC Modal--->

	    <!-- MGW Modal--->
		                            <div id="modmgw" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar MGW':'Add MGW' ?></h2>

                                          <input type="text" name="mgw" id="mgw" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="addmgw()" class="cmgw" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End MGW Modal--->
	   <!-- Edit MGW Modal--->
   <div id="edit_modmgw" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar MGW':'Edit MGW' ?></h2>
                                        <div class="field_container field_small">
<div class="field_label label"><span title=""> MGW :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">

<span style="float:left;"><?php echo $e_mgw ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="emgw" id="emgw" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="editmgw()" class="edit_site_mgw updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                        <input type="button" onclick="deletemgw()" class="edit_site_mgw deladdsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Edit MGW Modal--->
	
       <!-- BSC Modal--->
                                   <div id="modbsc" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar BSC':'Add BSC' ?></h2>

                                          <input type="text" name="bsc" id="bsc" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="addbsc()" class="cbsc" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End BSC Modal--->
	   <!--Edit BSC Modal--->
<div id="edit_modbsc" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar BSC':'Edit BSC' ?></h2>
                                        <div class="field_container field_small">
<div class="field_label label"><span title=""> BSC :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">

<span style="float:left;"><?php echo $e_bsc ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="ebsc" id="ebsc" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="editbsc()" class="edit_site_bsc updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                        <input type="button" onclick="deletebsc()" class="edit_site_bsc deladdsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Edit BSC Modal--->

         <!--Start Add Munciple  Modal--->
                                   <div id="modmunciple" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar Municipal':'Add Munciple' ?></h2>
                                        <div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
 <span style="float:left"><?php echo $dprovince ?></span>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="munciple" id="munciple" onfocus="this.value=''" style="width:242px;"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="addmunciple()" class="cmunciple" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Munciple Modal--->
      <!--Start Edit Munciple  Modal--->
      <div id="edit_modmunciple" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar Municipal':'Edit Municipal' ?></h2>
                                        <div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
 <span style="float:left"><?php echo $em_province ?></span>
</div>
</div></div></div>
</div>
</div>
<div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Municipal':'Municipal' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
 <span style="float:left"><?php echo $em_munciple ?></span>
</div>
</div></div></div>
</div>
</div>
<!-- Dropdown Munciple-->

<!-- End Dropdown Munciple -->

                                          <input type="text" name="emunciple" id="emunciple" onfocus="this.value=''" style="width:242px;"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
     <input type="button" onclick="editmunciple()" class="edit_site_munciple updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                        <input type="button" onclick="deletemunciple()" class="edit_site_munciple" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>" style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Munciple Modal--->

 <!-- Phase  Modal--->
 <div id="modphase" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar fase':'Add Phase' ?></h2>

                                          <input type="text" name="phase" id="phase" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="addphase()" class="cphase" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Phase Modal--->
	   <!-- Edit Phase  Modal--->
 <div id="edit_modphase" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Fase de edição':'Edit Phase' ?></h2>
                                        <div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Estágio':'Phase' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
 <span style="float:left"><?php echo $e_phase ?></span>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="ephase" id="ephase" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="editphase()" class="edit_site_phase updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                        <input type="button" onclick="deletephase()" class="edit_site_phase deladdsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Edit Phase Modal--->

	   <!-- Stage  Modal--->
 <div id="modstage" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar estágio':'Add Stage' ?></h2>

                                          <input type="text" name="stage" id="stage" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="addstage()" class="cstage" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Stage Modal--->
	    <!-- Edit Stage  Modal--->
 <div id="edit_modstage" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar estágio':'Edit Stage' ?></h2>
                                        <div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Etapa':'Stage' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
 <span style="float:left"><?php echo $e_stage ?></span>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="estage" id="estage" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                      <input type="button" onclick="editstage()" class="edit_site_stage updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                      <input type="button" onclick="deletestage()" class="edit_site_stage deladdsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Edit Stage Modal--->
	   <!-- SubStage  Modal--->
 <div id="modsubstage" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar Substage':'Add Substage' ?></h2>

                                          <input type="text" name="substage" id="substage" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="addsubstage()" class="csubstage" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End SubStage Modal--->
	    <!-- Edit SubStage  Modal--->
       <div id="edit_modsubstage" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar Substage':'Edit Substage' ?></h2>
                                        <div class="details">
<div class="field_container field_small">
<div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Substage':'Substage' ?> :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
 <span style="float:left"><?php echo $e_subStage ?></span>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="esubstage" id="esubstage" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="editsubstage()" class="edit_site_substage updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update'?>">
                                        <input type="button" onclick="deletesubstage()" class="edit_site_substage deladdsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                    </div>
	   <!-- End Edit SubStage Modal--->
	    <!-- RNC  Modal--->
                                     <div id="modrnc" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar RNC':'Add RNC' ?></h2>

                                          <input type="text" name="rnc" id="rnc" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="addrnc()" class="crnc" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End RNC Modal--->
	   <!-- Edit RNC  Modal--->
     <div id="edit_modrnc" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar RNC':'Edit RNC' ?></h2>
                                        <div class="field_container field_small">
<div class="field_label label"><span title=""> RNC :</span></div>
<div class="field_data">
<div class="field_value">
<div class="field_value_container">
<div class="attribute-edit">
<div class="field_input_zone field_input_extkey">
<div class="field_select_wrapper">

<span style="float:left;"><?php echo $e_rnc ?></span>
	</div>
</div>
</div></div></div>
</div>
</div>
                                          <input type="text" name="ernc" id="ernc" onfocus="this.value=''"> 
                                          <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                          </span>
                                        <input type="button" onclick="editrnc()" class="edit_site_rnc updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                        <input type="button" onclick="deleternc()" class="edit_site_rnc deladdsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>">
                                      <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                      </div>
                                                                                
                                   </div>
	   <!-- End Edit RNC Modal--->
	   <!-- Locality Modal--->
			                       <div id="modlocality" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Adicionar localidade':'Add Locality' ?></h2>
                                        <div class="details">
                                        <div class="field_container field_small">
                                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?> :</span></div>
                                        <div class="field_data">
                                        <div class="field_value">
                                        <div class="field_value_container">
                                        <div class="attribute-edit">
                                        <div class="field_input_zone field_input_extkey">
                                        <span style="float:left"><?php echo $lprovince ?></span>
                                      </div>
                                    </div></div></div>
                              </div>
                           </div>

                           
                                        <div class="details">
                                        <div class="field_container field_small">
                                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Municipal':'Munciple' ?> :</span></div>
                                        <div class="field_data">
                                        <div class="field_value">
                                        <div class="field_value_container">
                                        <div class="attribute-edit">
                                        <div class="field_input_zone field_input_extkey">
                                        <span style="float:left"><?php echo $dmunciple ?></span>
                                      </div>
                                    </div></div></div>
                              </div>
                           </div>
                           
                    <input type="text" name="locality" id="locality" style="width:242px;" onfocus="this.value=''"> 
                                                                            <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                        </span>
                                        <input type="button" onclick="addlocality()" class="clocality" value="<?php echo ($jsonData['language']=='PT BR')? 'Adicionar':'Add' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">
                                                                            <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                                                            </div>
                                                                                
</div>
	<!-- End Locality Modal--->
    <!--Start Edit Locality Modal--->
    <div id="edit_modlocality" class="modal">
                                        <h2><?php echo ($jsonData['language']=='PT BR')? 'Editar localidade':'Edit Locality' ?></h2>
                                        <div class="details">
                                        <div class="field_container field_small">
                                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?> :</span></div>
                                        <div class="field_data">
                                        <div class="field_value">
                                        <div class="field_value_container">
                                        <div class="attribute-edit">
                                        <div class="field_input_zone field_input_extkey">
                                        <span style="float:left"><?php echo $el_province ?></span>
                                      </div>
                                    </div></div></div>
                              </div>
                           </div>

                           
                                        <div class="details">
                                        <div class="field_container field_small">
                                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Municipal':'Municipal' ?> :</span></div>
                                        <div class="field_data">
                                        <div class="field_value">
                                        <div class="field_value_container">
                                        <div class="attribute-edit">
                                        <div class="field_input_zone field_input_extkey">
                                        <span style="float:left"><?php echo $el_munciple ?></span>
                                      </div>
                                    </div></div></div>
                              </div>
                           </div>
                           <div class="details">
                                        <div class="field_container field_small">
                                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Localidade':'Locality' ?> :</span></div>
                                        <div class="field_data">
                                        <div class="field_value">
                                        <div class="field_value_container">
                                        <div class="attribute-edit">
                                        <div class="field_input_zone field_input_extkey">
                                        <span style="float:left"><?php echo $el_locality ?></span>
                                      </div>
                                    </div></div></div>
                              </div>
                           </div>
                           
                    <input type="text" name="elocality" id="elocality" style="width:242px;" onfocus="this.value=''"> 
                                                                            <span class="form_validation">
                                            <img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
                                        </span>
                                        <input type="button" onclick="editlocality()" class="edit_site_locality updaddsite" value="<?php echo ($jsonData['language']=='PT BR')? 'Atualizar':'Update' ?>">
                                        <input type="button" onclick="deletelocality()" class="edit_site_locality" value="<?php echo ($jsonData['language']=='PT BR')? 'Excluir':'Delete' ?>"  style="padding: 5px 17px 4px 17px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;float: right;">                                   
                                                                            <div class="msgLoad" style="padding-top: 10px;float:right;">
                                                                                
                                                                            </div>
                                                                                
</div>
<!-- End Edit Locality Modal--->
			<script>
$(".cresparea").on("click",function(){
   $( "#modresparea" ).dialog();
    });

	$(".cpriority").on("click",function(){
    $( "#modpriority" ).dialog();
    });

	$(".celementtype").on("click",function(){
    $( "#modelementtype" ).dialog();
    });

	$(".cvendor").on("click",function(){
    $( "#modvendor" ).dialog();
    });

	$(".cmodel").on("click",function(){
    $( "#modmodel" ).dialog();
    });

	$(".cmsc").on("click",function(){
    $( "#modmsc" ).dialog();
    });

	$(".cmgw").on("click",function(){
    $( "#modmgw" ).dialog();
    });

	$(".cbsc").on("click",function(){
    $( "#modbsc" ).dialog();
    });

    $(".crnc").on("click",function(){
    $( "#modrnc" ).dialog();
    });

	$(".cmunciple").on("click",function(){
    $( "#modmunciple" ).dialog();
    });

	$(".cphase").on("click",function(){
    $( "#modphase" ).dialog();
    });

	$(".cstage").on("click",function(){
    $( "#modstage" ).dialog();
    });

	$(".csubstage").on("click",function(){
    $( "#modsubstage" ).dialog();
    });

     $(".clocality").on("click",function(){
    $( "#modlocality" ).dialog();
    });
    $(".cprovince").on("click",function(){
    $( "#modprovince" ).dialog();
    });
//*****End of Add Dropdown*********
    // ******Edit Dropdown Start*******
    $(".edit_site_responsible").on("click",function(){
    $( "#edit_modresparea" ).dialog();
    });
    $(".edit_site_priority").on("click",function(){
    $( "#edit_modpriority" ).dialog();
    });
    $(".edit_site_munciple").on("click",function(){
    $( "#edit_modmunciple" ).dialog();
    });
    $(".edit_site_locality").on("click",function(){
    $( "#edit_modlocality" ).dialog();
    });
    $(".edit_site_element_type").on("click",function(){
    $( "#edit_modelementtype" ).dialog();
    });
    $(".edit_site_vendor").on("click",function(){
    $( "#edit_modvendor" ).dialog();
    });
    $(".edit_site_model").on("click",function(){
    $( "#edit_modmodel" ).dialog();
    });
    $(".edit_site_msc").on("click",function(){
    $( "#edit_modmsc" ).dialog();
    });
    $(".edit_site_mgw").on("click",function(){
    $( "#edit_modmgw" ).dialog();
    });
    $(".edit_site_bsc").on("click",function(){
    $( "#edit_modbsc" ).dialog();
    });
    $(".edit_site_rnc").on("click",function(){
    $( "#edit_modrnc" ).dialog();
    });
    $(".edit_site_phase").on("click",function(){
    $( "#edit_modphase" ).dialog();
    });
    $(".edit_site_stage").on("click",function(){
    $( "#edit_modstage" ).dialog();
    });
    $(".edit_site_substage").on("click",function(){
    $( "#edit_modsubstage" ).dialog();
    });
    $(".edit_site_province").on("click",function(){
    $( "#edit_modprovince" ).dialog();
    });
</script>
<script>
function get_edit_site_responsible(val){
 //alert(val);
 $('input[name="eresparea"]').val(val);
}
function get_edit_site_priority(val){
 //alert(val);
 $('input[name="epriority"]').val(val);
}

function get_edit_site_element_type(val){
 //alert(val);
 $('input[name="eelementtype"]').val(val);
}

function get_edit_site_vendor(val){
 //alert(val);
 $('input[name="evendor"]').val(val);
}
function get_edit_site_model(val){
 //alert(val);
 $('input[name="emodel"]').val(val);
}
function get_edit_site_msc(val){
 //alert(val);
 $('input[name="emsc"]').val(val);
}
function get_edit_site_mgw(val){
 //alert(val);
 $('input[name="emgw"]').val(val);
}
function get_edit_site_bsc(val){
 //alert(val);
 $('input[name="ebsc"]').val(val);
}
function get_edit_site_msc(val){
 //alert(val);
 $('input[name="emsc"]').val(val);
}
function get_edit_site_rnc(val){
 //alert(val);
 $('input[name="ernc"]').val(val);
}
function get_edit_site_phase(val){
 //alert(val);
 $('input[name="ephase"]').val(val);
}
function get_edit_site_stage(val){
// alert(val);
 $('input[name="estage"]').val(val);
}
function get_edit_site_substage(val){
 //alert(val);
 $('input[name="esubstage"]').val(val);
}
function get_edit_site_province(val){
  //alert(val);
      $.ajax({
         type: 'POST',
         url: 'ajaxdropdown.php',
         dataType: "json",
         data: JSON.stringify({'province_edit_id':val}),
         success: function (data) {
             if(data){
              // alert(data.jprovince);
              $('input[name="eprovince"]').val(data.jprovince);
             }
         }
         });
      
}

function get_edit_site_munciple(val){
  //alert(val);
      $.ajax({
         type: 'POST',
         url: 'ajaxdropdown.php',
         dataType: "json",
         data: JSON.stringify({'munciple_edit_id':val}),
         success: function (data) {
             if(data){
             //  alert(data.jmunciple);
              $('input[name="emunciple"]').val(data.jmunciple);
             }
         }
         });
}

function get_edit_site_locality(val){
  //alert(val);
      $.ajax({
         type: 'POST',
         url: 'ajaxdropdown.php',
         dataType: "json",
         data: JSON.stringify({'locality_edit_id':val}),
         success: function (data) {
             if(data){
              // alert(data.jlocality);
              $('input[name="elocality"]').val(data.jlocality);
             }
         }
         });
}
</script>


<!--Start of Edit Province -->

<script>
   function editprovince(){
    var fprovince=$("#e_site_province").val();
    var province=$("#eprovince").val();

if(province==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addprovince.php',
         dataType: "json",
         data: JSON.stringify({'fprovince': fprovince,'eprovince': province }),
         success: function (data) {
          //alert("Value for 'c':"+ data.jprovinceid);
                 alert('Province Updated Successfully'); 	
                                 $("#site_province option[value='"+ data.jprovinceid +"']").remove();
                                  $("#e_site_province option[value='"+ data.jprovinceid +"']").remove();
                                  $("#dsite_provinces option[value='"+ data.jprovinceid +"']").remove();
                                  $("#lsite_provinces option[value='"+ data.jprovinceid + "']").remove();
                                  $("#em_site_province option[value='"+ data.jprovinceid + "']").remove();
         $('#site_province option:first').after(`<option selected="selected" value="${data.jprovinceid}"> 
                                       ${province} 
                                  </option>`); 
         $('#e_site_province option:first').after(`<option selected="selected" value="${data.jprovinceid}"> 
                                       ${province} 
                                  </option>`);         
         $('#dsite_provinces option:first').after(`<option selected="selected" value="${data.jprovinceid}"> 
                                       ${province} 
                                  </option>`);  
         $('#lsite_provinces option:first').after(`<option selected="selected" value="${data.jprovinceid}"> 
                                       ${province} 
                                  </option>`);       
        $('#em_site_province option:first').after(`<option selected="selected" value="${data.jprovinceid}"> 
                                       ${province} 
                                  </option>`); 
                                  
         $('#edit_modprovince').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>

 <!--End of Edit Province -->
 <!-- Start Edit Munciple -->
 <script>
   function editmunciple(){
    var e_provinceid=$("#em_site_province").val();	
    var e_muncipleid=$("#em_site_munciple").val();
    var emunciple=$("#emunciple").val();
//alert(e_provinceid);
//alert(e_muncipleid);
//alert(emunciple);
if(e_provinceid=='' && e_muncipleid=='' && emunciple=='' ){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addmunciple.php',
         dataType: "json",
         data: JSON.stringify({'e_provinceid':e_provinceid,'e_muncipleid': e_muncipleid,'emunciple':emunciple }),
         success: function (data) {
             if(data==-1){
                 alert('Munciple Already exists');
               $('#modmunciple').dialog('close');
             }else{
              //alert("Value for 'c':"+ data.jmuncipleid );
                 alert('Munciple Updated Successfully'); 	
         $('#site_munciple option:first').after(`<option selected="selected" value="${data.jmuncipleid}"> 
                                       ${emunciple} 
                                  </option>`); 
        $('#e_site_munciple option:first').after(`<option selected="selected" value="${data.jmuncipleid}"> 
                                       ${emunciple} 
                                  </option>`); 
         $('#dsite_munciple option:first').after(`<option selected="selected" value="${data.jmuncipleid}"> 
                                       ${emunciple} 
                                  </option>`); 
       
                               
         $('#edit_modmunciple').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>
 <!-- ENd Edit Munciple -->

<!-- Start Edit Locality -->
<script>
   function editlocality(){
    var el_muncipleid=$("#elsite_munciple").val();	
    var el_localityid=$("#elsite_locality").val();
    var elocality=$("#elocality").val();
//alert(el_muncipleid);
//alert(el_localityid);
//alert(elocality);
if(el_muncipleid=='' && el_localityid=='' && elocality=='' ){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addlocality.php',
         dataType: "json",
         data: JSON.stringify({'el_muncipleid': el_muncipleid,'el_localityid':el_localityid,'elocality':elocality }),
         success: function (data) {
             if(data==-1){
                 alert('Locality Already exists');
               $('#modlocality').dialog('close');
             }else{
             // alert("Value for 'c':"+ data.jlocalityid );
                 alert('Locality Updated Successfully'); 	
         $('#site_locality option:first').after(`<option selected="selected" value="${data.jlocalityid}"> 
                                       ${elocality} 
                                  </option>`); 
        $('#elsite_locality option:first').after(`<option selected="selected" value="${data.jlocalityid}"> 
                                       ${elocality} 
                                  </option>`);                    
         $('#edit_modlocality').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>
 <!-- ENd Edit Locality -->

<script>
   function editresponsibilityarea(){
    
    var fresparea=$("#e_site_responsible").val();
    var resparea=$("#eresparea").val();

if(resparea==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addresparea.php',
         dataType: "json",
         data: JSON.stringify({'fresparea':fresparea,'eresparea': resparea }),
         success: function (data) {
           
                 alert('Responsible Area Updated Successfully');
         $('#site_responsible option:first').after(`<option selected="selected" value="${resparea}"> 
                                       ${resparea} 
                                  </option>`); 
                                  $('#e_site_responsible option:first').after(`<option selected="selected" value="${resparea}"> 
                                       ${resparea} 
                                  </option>`);    
                                  $('input[name="eresparea"]').val(resparea);                     
                                  $("#site_responsible option[value='"+data+"']").remove();
                                  $("#e_site_responsible option[value='"+data+"']").remove();
                                  
         $('#edit_modresparea').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Update Priority -->
 <!--Update Priority -->
 <script>
   function editpriority(){
    var fpriority=$("#e_site_priority").val();
    var priority=$("#epriority").val();

if(priority==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addpriority.php',
         dataType: "json",
         data: JSON.stringify({'fpriority': fpriority,'epriority': priority }),
         success: function (data) {
            
                 alert('Priority Updated Successfully');
         $('#site_priority option:first').after(`<option selected="selected" value="${priority}"> 
                                       ${priority} 
                                  </option>`); 
                                  $('#e_site_priority option:first').after(`<option selected="selected" value="${priority}"> 
                                       ${priority} 
                                  </option>`);                         
                                  $("#site_priority option[value='"+data+"']").remove();
                                  $("#e_site_priority option[value='"+data+"']").remove();
                                  $('input[name="epriority"]').val(priority);
         $('#edit_modpriority').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Update Priority -->
 <!-- Start Update Element Type-->
 <script>
   function editelementtype(){
  
    var felementtype=$("#e_site_element_type").val();
    var elementtype=$("#eelementtype").val();

if(elementtype==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addelementtype.php',
         dataType: "json",
         data: JSON.stringify({'felementtype': felementtype,'eelementtype': elementtype }),
         success: function (data) {
           
                 alert('Element Type Updated Successfully'); 	
         $('#site_element_type option:first').after(`<option selected="selected" value="${elementtype}"> 
                                       ${elementtype} 
                                  </option>`); 
                                  $('#e_site_element_type option:first').after(`<option selected="selected" value="${elementtype}"> 
                                       ${elementtype} 
                                  </option>`);     
                                    $("#site_element_type option[value='"+ data +"']").remove();
                                  $("#e_site_element_type option[value='"+ data +"']").remove();
                                  $('input[name="eelementtype"]').val(elementtype); 
         $('#edit_modelementtype').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Update Element Type -->
 <!--Start Update Vendor -->
 <script>
   function editvendor(){
    var fvendor=$("#e_site_vendor").val();
    var vendor=$("#evendor").val();

if(vendor==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addvendor.php',
         dataType: "json",
         data: JSON.stringify({'fvendor': fvendor ,'evendor': vendor }),
         success: function (data) {
           
                 alert('Vendor Updated Successfully'); 	
         $('#site_vendor option:first').after(`<option selected="selected" value="${vendor}"> 
                                       ${vendor} 
                                  </option>`); 
                                  $('#e_site_vendor option:first').after(`<option selected="selected" value="${vendor}"> 
                                       ${vendor} 
                                  </option>`);                         
                                  $("#site_vendor option[value='"+data+"']").remove();
                                  $("#e_site_vendor option[value='"+data+"']").remove();
         $('#edit_modvendor').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
  <!--End Update Venor -->
  <!--Start Update Model -->
  <script>
   function editmodel(){
    var fmodel=$("#e_site_model").val();
var model=$("#emodel").val();

if(model==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addmodel.php',
         dataType: "json",
         data: JSON.stringify({'fmodel': fmodel,'emodel': model }),
         success: function (data) {
            
                 alert('Model Updated Successfully'); 	
                 $('#site_model option:first').after(`<option selected="selected" value="${model}"> 
                                       ${model} 
                                  </option>`); 
         $('#e_site_model option:first').after(`<option selected="selected" value="${model}"> 
                                       ${model} 
                                  </option>`);                         
                                  $("#site_model option[value='"+data+"']").remove();
                                  $("#e_site_model option[value='"+data+"']").remove();
                                  
         $('#edit_modmodel').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End Update Venor -->
 <!--Start Update MSC -->
 <script>
   function editmsc(){
    var fmsc=$("#e_site_msc").val();
    var msc=$("#emsc").val();

if(msc==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addmsc.php',
         dataType: "json",
         data: JSON.stringify({'fmsc': fmsc,'emsc': msc }),
         success: function (data) {
            
                 alert('MSC Updated Successfully'); 	
         $('#site_msc option:first').after(`<option selected="selected" value="${msc}"> 
                                       ${msc} 
                                  </option>`); 
                                  $('#e_site_msc option:first').after(`<option selected="selected" value="${msc}"> 
                                       ${msc} 
                                  </option>`);                         
                                  $("#site_msc option[value='"+data+"']").remove();
                                  $("#e_site_msc option[value='"+data+"']").remove();
                                  $('input[name="emsc"]').val(msc);
         $('#edit_modmsc').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Edit MSC -->
 <!--Start of Edit MGW -->
 <script>
   function editmgw(){
	
var mgw=$("#emgw").val();
var fmgw=$("#e_site_mgw").val();
if(mgw==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addmgw.php',
         dataType: "json",
         data: JSON.stringify({'fmgw': fmgw,'emgw': mgw }),
         success: function (data) {
            
                 alert('MGW Updated Successfully'); 	
         $('#site_mgw option:first').after(`<option selected="selected" value="${mgw}"> 
                                       ${mgw} 
                                  </option>`); 
                                  $('#e_site_mgw option:first').after(`<option selected="selected" value="${mgw}"> 
                                       ${mgw} 
                                  </option>`);                         
                                  $("#site_mgw option[value='"+data+"']").remove();
                                  $("#e_site_mgw option[value='"+data+"']").remove();
         $('#edit_modmgw').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Edit MGW -->
 <!--Start of Edit BSC -->
 <script>
   function editbsc(){
    var fbsc=$("#e_site_bsc").val();
var bsc=$("#ebsc").val();
//alert(fbsc);
//alert(bsc);
if(bsc==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addbsc.php',
         dataType: "json",
         data: JSON.stringify({'fbsc': fbsc,'ebsc': bsc }),
         success: function (data) {
             
                 alert('BSC Updated Successfully'); 	
         $('#site_bsc option:first').after(`<option selected="selected" value="${bsc}"> 
                                       ${bsc} 
                                  </option>`); 
                                  $('#e_site_bsc option:first').after(`<option selected="selected" value="${bsc}"> 
                                       ${bsc} 
                                  </option>`);                         
                                  $("#site_bsc option[value='"+data+"']").remove();
                                  $("#e_site_bsc option[value='"+data+"']").remove();
                                
         $('#edit_modbsc').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Edit BSC -->
 <!--Start of Edit RNC -->
 <script>
   function editrnc(){
    var frnc=$("#e_site_rnc").val();
var rnc=$("#ernc").val();

if(rnc==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addrnc.php',
         dataType: "json",
         data: JSON.stringify({'frnc': frnc,'ernc': rnc }),
         success: function (data) {
             
                 alert('RNC Updated Successfully'); 	
         $('#site_rnc option:first').after(`<option selected="selected" value="${rnc}"> 
                                       ${rnc} 
                                  </option>`); 
                                  $('#e_site_rnc option:first').after(`<option selected="selected" value="${rnc}"> 
                                       ${rnc} 
                                  </option>`);                         
                                  $("#site_rnc option[value='"+data+"']").remove();
                                  $("#e_site_rnc option[value='"+data+"']").remove();
         $('#edit_modrnc').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Edit RNC -->
 <!--Start of Edit Phase -->

  <script>
   function editphase(){
    var fphase=$("#e_site_phase").val();
     var phase=$("#ephase").val();
//alert(fphase);
if(phase==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addphase.php',
         dataType: "json",
         data: JSON.stringify({'fphase': fphase,'ephase': phase }),
         success: function (data) {
           
                 alert('Phase Updated Successfully'); 	
         $('#site_phase option:first').after(`<option selected="selected" value="${phase}"> 
                                       ${phase} 
                                  </option>`); 
                                  $('#e_site_phase option:first').after(`<option selected="selected" value="${phase}"> 
                                       ${phase} 
                                  </option>`);                         
                                  $("#site_phase option[value='"+data+"']").remove();
                                  $("#e_site_phase option[value='"+data+"']").remove();
         $('#edit_modphase').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>

 <!--End of Edit Phase -->
 <!--Start of Edit Stage -->

 <script>
   function editstage(){
    var fstage=$("#e_site_stage").val();
var stage=$("#estage").val();

if(stage==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addstage.php',
         dataType: "json",
         data: JSON.stringify({'fstage': fstage,'estage': stage }),
         success: function (data) {
            
                 alert('Stage Updated Successfully'); 	
         $('#site_stage option:first').after(`<option selected="selected" value="${stage}"> 
                                       ${stage} 
                                  </option>`); 
                                  $('#e_site_stage option:first').after(`<option selected="selected" value="${stage}"> 
                                       ${stage} 
                                  </option>`);                         
                                  $("#site_stage option[value='"+data+"']").remove();
                                  $("#e_site_stage option[value='"+data+"']").remove();
         $('#edit_modstage').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>

 <!--End of Edit Stage -->
 <!--Start of Edit Sub_Stage -->

 <script>
   function editsubstage(){
    var fsubstage=$("#e_site_sub_stage").val();
    var substage=$("#esubstage").val();

if(substage==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addsubstage.php',
         dataType: "json",
         data: JSON.stringify({'fsubstage': fsubstage,'esubstage': substage }),
         success: function (data) {
            
                 alert('Substage Updated Successfully'); 	 
         $('#site_sub_stage option:first').after(`<option selected="selected" value="${substage}"> 
                                       ${substage} 
                                  </option>`); 
                                  $('#e_site_sub_stage option:first').after(`<option selected="selected" value="${substage}"> 
                                       ${substage} 
                                  </option>`);                         
                                  $("#site_sub_stage option[value='"+data+"']").remove();
                                  $("#e_site_sub_stage option[value='"+data+"']").remove();
         $('#edit_modsubstage').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>

 <!--End of Edit Sub_Stage -->

 
 <!--******************************************Delete******************* -->
 
 <!-- Start Delete Province -->
 <script>
   function deleteprovince(){
    
    var province=$("#e_site_province").val();
    
if(province==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addprovince.php',
         dataType: "json",
         data: JSON.stringify({'dprovince':province}),
         success: function (data) {
           alert('Province Deleted Successfully');
           document.getElementById("eprovince").value = "";   
           $("#site_province option[value='"+data+"']").remove();
           $("#e_site_province option[value='"+data+"']").remove();
           $("#dsite_provinces option[value='"+data+"']").remove();
           $("#lsite_provinces option[value='"+data+"']").remove();
                                
         $('#edit_modprovince').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!-- Start Delete Munciple -->
 <script>
   function deletemunciple(){
    
    var dmuncipleid=$("#em_site_munciple").val();
    //alert(dmuncipleid);
if(dmuncipleid==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addmunciple.php',
         dataType: "json",
         data: JSON.stringify({'dmuncipleid':dmuncipleid}),
         success: function (data) {
           alert('Munciple Deleted Successfully');
           document.getElementById("emunciple").value = "";   
           $("#site_munciple option[value='"+data+"']").remove();
           $("#e_site_munciple option[value='"+data+"']").remove();
           $("#dsite_munciple option[value='"+data+"']").remove();
           $("#lsite_munciple option[value='"+data+"']").remove();
           $("#em_site_munciple option[value='"+data+"']").remove();                 
           $('#edit_modmunciple').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Delete  Munciple -->
 <!-- Start Delete Locality -->
 <script>
   function deletelocality(){
    
    var dlocalityid=$("#elsite_locality").val();
   // alert(dlocalityid);
if(dlocalityid==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addlocality.php',
         dataType: "json",
         data: JSON.stringify({'dlocalityid':dlocalityid}),
         success: function (data) {
           alert('Locality Deleted Successfully');
           document.getElementById("elocality").value = "";   
           $("#site_locality option[value='"+data+"']").remove();
           $("#elsite_locality option[value='"+data+"']").remove();
                         
           $('#edit_modlocality').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Delete  Locality -->
 <!--Start Delete Responsibility Area -->
 <script>
   function deleteresponsibilityarea(){
    var dresparea=$("#e_site_responsible").val();
    
if(dresparea==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addresparea.php',
         dataType: "json",
         data: JSON.stringify({'dresparea': dresparea }),
         success: function (data) {
            
                 alert('Responsible Area Deleted Successfully');
                 document.getElementById("eresparea").value = "";      
                                  $("#site_responsible option[value='"+data+"']").remove();
                                  $("#e_site_responsible option[value='"+data+"']").remove();
         $('#edit_modresparea').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of  Delete Responsibility Area -->
 <!--Start Delete Priority -->
 <script>
   function deletepriority(){
    var priority=$("#e_site_priority").val();
    
if(priority==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addpriority.php',
         dataType: "json",
         data: JSON.stringify({'dpriority': priority }),
         success: function (data) {
            
                 alert('Priority Deleted Successfully');
                 document.getElementById("epriority").value = "";      
                                  $("#site_priority option[value='"+data+"']").remove();
                                  $("#e_site_priority option[value='"+data+"']").remove();
         $('#edit_modpriority').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Delete Priority -->
 <!-- Start Delete Element Type-->
 <script>
   function deleteelementtype(){
  
    var elementtype=$("#e_site_element_type").val();
  

if(elementtype==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addelementtype.php',
         dataType: "json",
         data: JSON.stringify({'delementtype': elementtype }),
         success: function (data) {
           
                 alert('Element Type Deleted Successfully'); 	
                 document.getElementById("eelementtype").value = "";   
                                  $("#site_element_type option[value='"+data+"']").remove();
                                  $("#e_site_element_type option[value='"+data+"']").remove(); 
         $('#edit_modelementtype').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Delete Element Type -->
 <!--Start Delete Vendor -->
 <script>
   function deletevendor(){
    var vendor=$("#e_site_vendor").val();
  
if(vendor==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addvendor.php',
         dataType: "json",
         data: JSON.stringify({'dvendor': vendor }),
         success: function (data) {
           
                 alert('Vendor Deleted Successfully'); 	   
                 document.getElementById("evendor").value = "";    
                                  $("#site_vendor option[value='"+data+"']").remove();
                                  $("#e_site_vendor option[value='"+data+"']").remove();
         $('#edit_modvendor').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
  <!--End Delete Venor -->
 <!--Start Delete Model -->
 <script>
   function deletemodel(){
    var dmodel=$("#e_site_model").val();


if(dmodel==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addmodel.php',
         dataType: "json",
         data: JSON.stringify({'dmodel': dmodel}),
         success: function (data) {
            
                 alert('Model Deleted Successfully'); 	
                 document.getElementById("emodel").value = "";                
                                  $("#site_model option[value='"+data+"']").remove();
                                  $("#e_site_model option[value='"+data+"']").remove();
                                  
         $('#edit_modmodel').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End Delete Venor -->
 <!--Start Ddelete MSC -->
 <script>
   function deletemsc(){
    var dmsc=$("#e_site_msc").val();
if(dmsc==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addmsc.php',
         dataType: "json",
         data: JSON.stringify({'dmsc': dmsc}),
         success: function (data) {
            
                 alert('MSC Deleted Successfully'); 	
                 document.getElementById("emsc").value = "";     
         $("#site_msc option[value='"+data+"']").remove();
         $("#e_site_msc option[value='"+data+"']").remove();
                                
         $('#edit_modmsc').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Delete MSC -->
 <!--Start of Delete MGW -->
 <script>
   function deletemgw(){
var dmgw=$("#e_site_mgw").val();
if(dmgw==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addmgw.php',
         dataType: "json",
         data: JSON.stringify({'dmgw': dmgw}),
         success: function (data) {
            
                 alert('MGW Deleted Successfully'); 	
                 document.getElementById("emgw").value = "";     
                $("#site_mgw option[value='"+data+"']").remove();
                $("#e_site_mgw option[value='"+data+"']").remove();
         $('#edit_modmgw').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Delete MGW -->
 <!--Start of Delete BSC -->
 <script>
   function deletebsc(){
    var dbsc=$("#e_site_bsc").val();
if(dbsc==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addbsc.php',
         dataType: "json",
         data: JSON.stringify({'dbsc': dbsc}),
         success: function (data) {
             
                 alert('BSC Deleted Successfully'); 	
                           document.getElementById("ebsc").value = ""; 
                                  $("#site_bsc option[value='"+data+"']").remove();
                                  $("#e_site_bsc option[value='"+data+"']").remove();
                                
         $('#edit_modbsc').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Delete BSC -->
 <!--Start of Delete RNC -->
 <script>
   function deleternc(){
    var drnc=$("#e_site_rnc").val();


if(drnc==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addrnc.php',
         dataType: "json",
         data: JSON.stringify({'drnc': drnc }),
         success: function (data) {
                 alert('RNC Deleted Successfully'); 	  
                  document.getElementById("ernc").value = "";        
                                  $("#site_rnc option[value='"+data+"']").remove();
                                  $("#e_site_rnc option[value='"+data+"']").remove();
         $('#edit_modrnc').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End of Delete RNC -->
  <!--Start of Delete Phase -->

  <script>
   function deletephase(){
    var dphase=$("#e_site_phase").val();
if(dphase==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addphase.php',
         dataType: "json",
         data: JSON.stringify({'dphase': dphase }),
         success: function (data) {
           
                 alert('Phase Deleted Successfully'); 	
                           document.getElementById("ephase").value = ""; 
                                  $("#site_phase option[value='"+data+"']").remove();
                                  $("#e_site_phase option[value='"+data+"']").remove();
         $('#edit_modphase').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>

 <!--End of Delete Phase -->
 <!--Start Delete Stage> -->
 <script>
 function deletestage(){
    var dstage=$("#e_site_stage").val();

if(dstage==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addstage.php',
         dataType: "json",
         data: JSON.stringify({'dstage': dstage }),
         success: function (data) {
            
                 alert('Stage Deleted Successfully'); 	
                 document.getElementById("estage").value = "";
                                  $("#site_stage option[value='"+data+"']").remove();
                                  $("#e_site_stage option[value='"+data+"']").remove();
         $('#edit_modstage').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End Delete stage> -->
 <!--Start Delete Subsctage> -->
 <script>
   function deletesubstage(){
    var dsubstage=$("#e_site_sub_stage").val();

if(substage==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addsubstage.php',
         dataType: "json",
         data: JSON.stringify({'dsubstage': dsubstage }),
         success: function (data) {
                 alert('Substage Deleted Successfully'); 	 
                 document.getElementById("esubstage").value = "";                 
         $("#site_sub_stage option[value='"+data+"']").remove();
         $("#e_site_sub_stage option[value='"+data+"']").remove();
         $('#edit_modsubstage').dialog('close');
    return false;
             
         }
     });
 }
    }       
 </script>
 <!--End Delete Subsctage> -->
 
 

 <!-- Add Province -->
 <script>

 
   function addprovince(){
	
  var province=$("#province").val();
  //alert(province);
  if(province==''){
       alert('Please fill all mandatory fields');
  }else{
                 $.ajax({
           type: 'POST',
           url: 'addprovince.php',
           dataType: "json",
           data: JSON.stringify({'provincep': province }),
           success: function (data) {
               if(data==-1){
                   alert('Province Already exists');
                 $('#modprovince').dialog('close');
               }else{
                   alert('Province Added Successfully');
           $('#site_province option:first').after(`<option selected="selected" value="${data.jprovinceid}"> 
                                         ${province} 
                                    </option>`); 
          $('#e_site_province option:first').after(`<option selected="selected" value="${data.jprovinceid}"> 
                                         ${province} 
                                    </option>`); 
           $('#dsite_provinces option:first').after(`<option selected="selected" value="${data.jprovinceid}"> 
                                         ${province} 
                                    </option>`); 
           $('#lsite_provinces option:first').after(`<option selected="selected" value="${data.jprovinceid}"> 
                                       ${province} 
                                  </option>`);  
             $('input[name="eprovince"]').val(province);   
           $('#modprovince').dialog('close');
      return false;
               }
           }
       });
   }
      }       
   </script>
<!-- End Add Province -->


<script>
   function addresponsibilityarea(){
	
var resparea=$("#resparea").val();

if(resparea==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addresparea.php',
         dataType: "json",
         data: JSON.stringify({'resparea': resparea }),
         success: function (data) {
             if(data){
                 alert('Responsibility Area Already exists');
               $('#modresparea').dialog('close');
             }else{
                 alert('Responsible Area Added Successfully');
         $('#site_responsible option:first').after(`<option selected="selected" value="${resparea}"> 
                                       ${resparea} 
                                  </option>`); 
         $('#e_site_responsible option:first').after(`<option selected="selected" value="${resparea}"> 
                                       ${resparea} </option>`);
                                       $('input[name="eresparea"]').val(resparea);   

         $('#modresparea').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>
 <script>
   function addpriority(){
	
var priority=$("#priority").val();

if(priority==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addpriority.php',
         dataType: "json",
         data: JSON.stringify({'priority': priority }),
         success: function (data) {
             if(data){
                 alert('Priority Already exists');
               $('#modpriority').dialog('close');
             }else{
                 alert('Priority Added Successfully');
         $('#site_priority option:first').after(`<option selected="selected" value="${priority}"> 
                                       ${priority} 
                                  </option>`); 
         $('#e_site_priority option:first').after(`<option selected="selected" value="${priority}"> 
                                       ${priority} 
                                  </option>`); 
         $('input[name="epriority"]').val(priority);   
         $('#modpriority').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>
<script>
   function addelementtype(){
	
var elementtype=$("#elementtype").val();

if(elementtype==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addelementtype.php',
         dataType: "json",
         data: JSON.stringify({'elementtype': elementtype }),
         success: function (data) {
             if(data){
                 alert('Element Type Already exists');
               $('#modelementtype').dialog('close');
             }else{
                 alert('Element Type Added Successfully'); 	
         $('#site_element_type option:first').after(`<option selected="selected" value="${elementtype}"> 
                                       ${elementtype} 
                                  </option>`); 
         $('#e_site_element_type option:first').after(`<option selected="selected" value="${elementtype}"> 
                                       ${elementtype} 
                                  </option>`); 
                                  $('input[name="eelementtype"]').val(elementtype);       
         $('#modelementtype').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>

<script>
   function addvendor(){
	
var vendor=$("#vendor").val();

if(vendor==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addvendor.php',
         dataType: "json",
         data: JSON.stringify({'vendor': vendor }),
         success: function (data) {
             if(data){
                 alert('Vendor Already exists');
               $('#modvendor').dialog('close');
             }else{
                 alert('Vendor Added Successfully'); 	
         $('#site_vendor option:first').after(`<option selected="selected" value="${vendor}"> 
                                       ${vendor} 
                                  </option>`); 
         $('#e_site_vendor option:first').after(`<option selected="selected" value="${vendor}"> 
                                       ${vendor} 
                                  </option>`); 
                                  $('input[name="evendor"]').val(vendor);      
         $('#modvendor').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>

<script>
   function addmodel(){
	
var model=$("#model").val();

if(model==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addmodel.php',
         dataType: "json",
         data: JSON.stringify({'model': model }),
         success: function (data) {
             if(data){
                 alert('Model Already exists');
               $('#modmodel').dialog('close');
             }else{
                 alert('Model Added Successfully'); 	
         $('#site_model option:first').after(`<option selected="selected" value="${model}"> 
                                       ${model} 
                                  </option>`); 
         $('#e_site_model option:first').after(`<option selected="selected" value="${model}"> 
                                       ${model} 
                                  </option>`); 
                                  $('input[name="emodel"]').val(model); 
         $('#modmodel').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>

<script>
   function addmsc(){
	
var msc=$("#msc").val();

if(msc==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addmsc.php',
         dataType: "json",
         data: JSON.stringify({'msc': msc }),
         success: function (data) {
             if(data){
                 alert('MSC Already exists');
               $('#modmsc').dialog('close');
             }else{
                 alert('MSC Added Successfully'); 	
         $('#site_msc option:first').after(`<option selected="selected" value="${msc}"> 
                                       ${msc} 
                                  </option>`); 
          $('#e_site_msc option:first').after(`<option selected="selected" value="${msc}"> 
                                       ${msc} 
                                  </option>`);  
                                   $('input[name="emsc"]').val(msc);
         $('#modmsc').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>

<script>
   function addmgw(){
	
var mgw=$("#mgw").val();

if(mgw==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addmgw.php',
         dataType: "json",
         data: JSON.stringify({'mgw': mgw }),
         success: function (data) {
             if(data){
                 alert('MGW Already exists');
               $('#modmgw').dialog('close');
             }else{
                 alert('MGW Added Successfully'); 	
         $('#site_mgw option:first').after(`<option selected="selected" value="${mgw}"> 
                                       ${mgw} 
                                  </option>`); 
          $('#e_site_mgw option:first').after(`<option selected="selected" value="${mgw}"> 
                                       ${mgw} 
                                  </option>`); 
                                  $('input[name="emgw"]').val(mgw);
         $('#modmgw').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>

<script>
   function addbsc(){
	
var bsc=$("#bsc").val();

if(bsc==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addbsc.php',
         dataType: "json",
         data: JSON.stringify({'bsc': bsc }),
         success: function (data) {
             if(data){
                 alert('BSC Already exists');
               $('#modbsc').dialog('close');
             }else{
                 alert('BSC Added Successfully'); 	
         $('#site_bsc option:first').after(`<option selected="selected" value="${bsc}"> 
                                       ${bsc} 
                                  </option>`); 
          $('#e_site_bsc option:first').after(`<option selected="selected" value="${bsc}"> 
                                       ${bsc} 
                                  </option>`);
                                  $('input[name="ebsc"]').val(bsc);
         $('#modbsc').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>




<script>
   function addmunciple(){
	
var munciple=$("#munciple").val();
var province_id=$("#dsite_provinces").val();
//alert(province_id);
if(munciple==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addmunciple.php',
         dataType: "json",
         data: JSON.stringify({'province_id':province_id,'munciple': munciple }),
         success: function (data) {
             if(data==-1){
                 alert('Munciple Already exists');
               $('#modmunciple').dialog('close');
             }else{
              //alert("Value for 'c':"+ data.jmuncipleid +"\nValue for 'a': " + data.jprovinceid + "\nValue for 'b': " + data.jprovince);
                 alert('Munciple Added Successfully'); 	
         $('#site_munciple option:first').after(`<option selected="selected" value="${data.jmuncipleid}"> 
                                       ${munciple} 
                                  </option>`); 
                                  $('#e_site_munciple option:first').after(`<option selected="selected" value="${data.jmuncipleid}"> 
                                       ${munciple} 
                                  </option>`); 
                                  $('#dsite_munciple option:first').after(`<option selected="selected" value="${data.jmuncipleid}"> 
                                       ${munciple} 
                                  </option>`); 
                                  $('#lsite_provinces option:first').after(`<option selected="selected" value="${data.jprovinceid}"> 
                                       ${data.jprovince} 
                                  </option>`); 
         $('#modmunciple').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>
<script>
   function addlocality(){
   
var munciple_id=$("#dsite_munciple").val();
var province_id=$("#lsite_provinces").val();
var locality=$("#locality").val();
//alert(munciple_id);
//alert(province_id);
//alert(locality);
if(locality==''){
     alert('Please fill all mandatory fields');
}else{
        $.ajax({
         type: 'POST',
         url: 'addlocality.php',
         dataType: "json",
         data: JSON.stringify({'munciple_id': munciple_id,'province_id':province_id,'locality':locality }),
         success: function (data) {
             if(data==-1){
                 alert('Locality Already exists');
               $('#modlocality').dialog('close');
             }else{
              //alert("Value for  'b': " + data.jlocalityid);
                 alert('Locality Added Successfully'); 	
         $('#site_locality option:first').after(`<option selected="selected" value="${data.jlocalityid}"> 
                                       ${locality} 
                                  </option>`); 
         $('#e_site_locality option:first').after(`<option selected="selected" value="${data.jlocalityid}"> 
                                       ${locality} 
                                  </option>`); 
         $('#modlocality').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>

 <script>
   function addphase(){
	
var phase=$("#phase").val();

if(phase==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addphase.php',
         dataType: "json",
         data: JSON.stringify({'phase': phase }),
         success: function (data) {
             if(data){
                 alert('Phase Already exists');
               $('#phase').dialog('close');
             }else{
                 alert('Phase Added Successfully'); 	
         $('#site_phase option:first').after(`<option selected="selected" value="${phase}"> 
                                       ${phase} 
                                  </option>`); 
          $('#e_site_phase option:first').after(`<option selected="selected" value="${phase}"> 
                                       ${phase} 
                                  </option>`); 
                                  $('input[name="ephase"]').val(phase);
         $('#modphase').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>

<script>
   function addstage(){
	
var stage=$("#stage").val();

if(stage==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addstage.php',
         dataType: "json",
         data: JSON.stringify({'stage': stage }),
         success: function (data) {
             if(data){
                 alert('Stage Already exists');
               $('#stage').dialog('close');
             }else{
                 alert('Stage Added Successfully'); 	
         $('#site_stage option:first').after(`<option selected="selected" value="${stage}"> 
                                       ${stage} 
                                  </option>`); 
         $('#e_site_stage option:first').after(`<option selected="selected" value="${stage}"> 
                                       ${stage} 
                                  </option>`); 
                                  $('input[name="estage"]').val(stage);
         $('#modstage').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>

<script>
   function addsubstage(){
	
var substage=$("#substage").val();

if(substage==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addsubstage.php',
         dataType: "json",
         data: JSON.stringify({'substage': substage }),
         success: function (data) {
             if(data){
                 alert('Substage Already exists');
               $('#substage').dialog('close');
             }else{
                 alert('Substage Added Successfully'); 	
         $('#site_sub_stage option:first').after(`<option selected="selected" value="${substage}"> 
                                       ${substage} 
                                  </option>`); 
          $('#e_site_sub_stage option:first').after(`<option selected="selected" value="${substage}"> 
                                       ${substage} 
                                  </option>`); 
                                  $('input[name="esubstage"]').val(substage);
         $('#modsubstage').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>
 <script>
   function addrnc(){
	
var rnc=$("#rnc").val();

if(rnc==''){
     alert('Please fill all mandatory fields');
}else{
               $.ajax({
         type: 'POST',
         url: 'addrnc.php',
         dataType: "json",
         data: JSON.stringify({'rnc': rnc }),
         success: function (data) {
             if(data){
                 alert('RNC Already exists');
               $('#rnc').dialog('close');
             }else{
                 alert('RNC Added Successfully'); 	
         $('#site_rnc option:first').after(`<option selected="selected" value="${rnc}"> 
                                       ${rnc} 
                                  </option>`); 
          $('#e_site_rnc option:first').after(`<option selected="selected" value="${rnc}"> 
                                       ${rnc} 
                                  </option>`); 
                                  $('input[name="ernc"]').val(rnc);
         $('#modrnc').dialog('close');
    return false;
             }
         }
     });
 }
    }       
 </script>

 <script type="text/javascript">
            $(document).ready(function () {
                $('#site_province').on('change', function () {
                 /* $('#site_munciple').find('option').not(':first').remove();
                  $('#site_locality').find('option').not(':first').remove();*/

                    var province_id = $(this).val();
                    $('#site_munciple').attr("disabled","disabled");
                    $('#site_locality').attr("disabled","disabled");
                    $("#muncipleLoad").css("display","block");
                    $("#localityLoad").css("display","block");
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
	                            $('#site_locality').html("<option value=''> -- Select One -- </option>");
	                            $('#site_munciple').removeAttr("disabled");
	                			$('#site_locality').removeAttr("disabled");
	                            $("#muncipleLoad").css("display","none");
	                    		$("#localityLoad").css("display","none");
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
                 /* $('#site_locality').find('option').not(':first').remove();*/
                  $("#localityLoad").css("display","block");
                  $('#site_locality').attr("disabled","disabled");
                    var munciple_id = $(this).val();
                    //alert(munciple_id);
                    if (munciple_id) {
                      $.ajax({
                            type: 'POST',
                            url: 'ajaxdropdown.php',
                            dataType: "html",
                            data: JSON.stringify({'munciple_id': munciple_id}),
                            success: function (html) {
                                $('#site_locality').html(html);
                                $('#city1').html('<option value="">Select Munciple first</option>');
                                $('#site_locality').removeAttr("disabled");
                              	$("#localityLoad").css("display","none");
                            }
                        });
                    } else {
                        $('#tehesil1').html('<option value="">Select country first</option>');
                        $('#city1').html('<option value="">Select state first</option>');
                    }
                });
            });
        </script>
        <!-- Popup Munciple & Province -->

        <script>
        function getVal(opts)
        {
          $('#dsite_munciple').find('option').not(':first').remove();
                    var province_id = $("select#lsite_provinces option").filter(":selected").val();
                   
                    if (province_id) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajaxdropdown.php',
                            dataType: "html",
                            data: JSON.stringify({'province_id': province_id}),
                            success: function (html) {
                              //console.log(html);
                              $('#dsite_munciple').html(html);
                                //$('#site_munciplesss').html(html);
                                //$('#tehesil1').html('<option value="">Select District first</option>');
                            }
                        });
                    } else {
                        $('#district1').html('<option value="">Select country first</option>');
                        $('#tehesil1').html('<option value="">Select state first</option>');
                    }
        }
            // $(document).ready(function () {
            //     $('#dsite_provinces').on('change', function () {
                
            //     });
            // });
        </script>
        <script>
            $(document).ready(function () {
                $('#dsite_munciple').on('change', function () {
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
        <!-- Start Edit Munciple-->
         <script>
             $(document).ready(function () {
                $('#em_site_province').on('change', function () {
                  // $('#site_munciple').find('option').not(':first').remove();
                  // $('#site_locality').find('option').not(':first').remove();
                  
                    var province_id = $(this).val();
                    //console.log(province_id);
                   //alert(province_id);
                    if (province_id) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajaxdropdown.php',
                            dataType: "html",
                            data: JSON.stringify({'province_id': province_id}),
                            success: function (html) {
                              //console.log(html);
                              $('#em_site_munciple').html(html);
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
        <!-- END Edit Munciple-->
        <!--- For Locality Popup -->
        <script type="text/javascript">
            $(document).ready(function () {
                $('#elsite_province').on('change', function () {
                  $('#elsite_munciple').find('option').not(':first').remove();
                  $('#elsite_locality').find('option').not(':first').remove();
                  
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
                              $('#elsite_munciple').append(html);
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
                $('#elsite_munciple').on('change', function () {
                  $('#elsite_locality').find('option').not(':first').remove();
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
                              $('#elsite_locality').append(html);
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
        <!-- Popup Munciple & Province -->
        <!--  End For Locality Poup NT3LIVE-->