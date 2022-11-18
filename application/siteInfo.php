<style type="text/css">
    h1{display: none;}
</style>
<?php

include('../webservices/wbdb.php');

$postData = file_get_contents("php://input");
$jsonData = json_decode($postData,TRUE);

if(isset($_GET['id'])){

    $query1 = "SELECT * FROM `ntsites` WHERE `is_active` = 1 AND `site_id`=".$_GET['id'];
    $result1 = mysqli_query($conf, $query1);
    $info = mysqli_fetch_all($result1, MYSQLI_ASSOC);

    //$info[0]['province'] = iconv('UTF-8', 'ISO-8859-1//IGNORE', $info[0]['province']);

    $query2 = "SELECT * FROM `ntsitenetwork` WHERE `is_active` = 1 AND `site_id`=".$_GET['id'];
    $result2 = mysqli_query($conf, $query2);
    $str = array();

    foreach (mysqli_fetch_all($result2, MYSQLI_ASSOC) as $rows) {
        array_push($str, $rows['network']);
    }
    $info[0]['network'] = implode(" , ", $str);
    $site = $info[0];

    /********** Province ***********/
    $query1 = "SELECT province FROM ntsiteprovince WHERE province_id = ".$site['province'];
    $result1 = mysqli_query($conf, $query1);
    if($result1){
        if($result1->num_rows>0){
            $temp = mysqli_fetch_all($result1, MYSQLI_ASSOC);
            $site['province'] = $temp[0]['province'];
        }
    }
   
    /********** Munciple Site ***********/
    $query1 = "SELECT munciple FROM ntsitemunciple WHERE munciple_id = ".$site['munciple'];
    $result1 = mysqli_query($conf, $query1);
    if($result1){
        if($result1->num_rows>0){
            $temp = mysqli_fetch_all($result1, MYSQLI_ASSOC);
            $site['munciple'] = $temp[0]['munciple'];
        }
    }
    /********** Locality Site ***********/

    $query = "SELECT locationname FROM nplocation WHERE locationid = ".$site['locality'];
    $result1 = mysqli_query($conf, $query);
    if($result1){
        if($result1->num_rows>0){
            $temp = mysqli_fetch_all($result1, MYSQLI_ASSOC);
            $site['locality'] = $temp[0]['locationname'];
        }
    }
    

    $siteid = $_GET['id'];
?>
<script language="javascript" type="text/javascript">
    $(document).ready(function() {
        //$("#tabs").tabs({active: document.tabTest.currentTab.value});
        $("#tabs").tabs();
        $("#tabs > ul").bind("tabsshow", function(event, ui) { 
            window.location.hash = ui.tab.hash;
        });
        /*$('#tabs a').click(function(e) {
            var curTab = $('.ui-tabs-active');
            curTabIndex = curTab.index();
            document.tabTest.currentTab.value =;
        });*/
    });
</script>

<div class="ui-layout-content" style="overflow: auto; position: relative; visibility: visible;">
<h2 style="font-family: Tahoma, Verdana, Arial, Helvetica!important;
color: #422462!important;font-weight: bold!important;font-size: 12pt!important;">
<img src="https://nt3.nectarinfotel.com/images/addactivity.png" style="vertical-align:middle;width: 32px;">&nbsp;<?php echo ($jsonData['language']=='PT BR')? 'Informação do Site':'Site Information' ?></h2>
<div class="wizContainer">
<form action="" method="post" onSubmit="">
<p></p>
<input type="hidden" name="operation" value="apply_new">
<button type="button" onclick="window.location.href='https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite'" class="action cancel"><?php echo ($jsonData['language']=='PT BR')? 'Cancelar':'Cancel' ?></button>&nbsp;&nbsp;&nbsp;&nbsp;
<button type="button" class="action" onclick="window.location.href='https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=addSite'"><span> <?php echo ($jsonData['language']=='PT BR')? 'Nova':'New' ?> </span></button>
<button type="button" onclick="window.location.href='https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=siteDetails&id=<?php echo $site['site_id'] ?>'" name="next_action" value="ev_assign" class="action" style="background-color: #f17422;">
<span><?php echo ($jsonData['language']=='PT BR')? 'Modificar':'Modify' ?></span></button>
<!-- <button type="submit" name="next_action" value="ev_assign" class="action"><span>Assign</span></button> -->

<!-- 
     class="modal" style="padding-left: 89px;"
    class="modal-content" style="padding-top: 0px!important;padding-left: 10px!important;padding-right: 10px!important;padding-bottom: 12px!important; margin-left: 300px !important;margin-top: 70px !important;border: 1px solid #ddd!important;border-radius: 3px!important;width: 50%!important;"

    <span class="close siteInfoClose" style="margin-top: 10px!important;font-size: 20px!important;">&times;</span>
                <h4 style="color: #F17422!important;padding-bottom: 10px!important;border-bottom: 1px solid #dcdcdc!important;text-transform:uppercase;"></h4>
-->

  <!-- <form name="tabTest" method="post" action="tab">    
        <input type="hidden" name="currentTab" value="0"/>  -->
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1"><?php echo ($jsonData['language']=='PT BR')? 'Informação do Site':'Site Information' ?></a></li>
                <li><a href="#tabs-2"><?php echo ($jsonData['language']=='PT BR')? 'Site agregado':'Aggregate Site' ?></a></li>
                <li><a href="#tabs-3"><?php echo ($jsonData['language']=='PT BR')? 'Histórico do site':'Site History' ?></a></li>
            </ul>
            <div id="tabs-1">
                <div class="table-responsive" id="siteContent">
                    <table class="table" style="vertical-align:top;width:100%">
                        <tbody>
                            <tr>
                            <td style="vertical-align:top; width:50%">
                            <div class="details">
                            </div>
                        <fieldset>
                        <legend><?php echo ($jsonData['language']=='PT BR')? 'Local':'Site' ?></legend>
                        <div class="details">
                        <div class="field_container field_small">
                        <div class="field_label label">
                        <span title=""><?php echo ($jsonData['language']=='PT BR')? 'Código do site':'Site Code' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                            <?php echo $site['site_code'] ?>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label">
						<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Nome do site':'Site Name' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                            <span style="float:left"><?php echo $site['site_name']; ?></span>
                            <input type="hidden" value="<?php echo $site['site_id'] ?>" name="site_id" id="site_id">
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label">
						<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Rede':'Network' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                             <span style="float:left;padding-right: 57px;"><?php echo $site['network']; ?></span>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label">
						<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Área responsável':'Responsible area' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php echo $site['responsible_area']; ?></span>
                            </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label">
						<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Prioridade':'Priority' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php echo $site['priority']; ?></span>
                            </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label">
						<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Comentário prioritário':'Priority Comment' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left"><?php echo $site['priority_comment']; ?></div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        </fieldset>

                        <fieldset>
                        <legend><?php echo ($jsonData['language']=='PT BR')? 'Modelo':'Model' ?></legend>
                        <div class="details">
                        <div class="field_container field_small">
                        <div class="field_label label">
						<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Tipo de elemento':'Element Type' ?> : </span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                            <span style="float:left;"><?php echo $site['element_type']; ?> </span>
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label">
						<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Fornecedor':'Vendor' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php echo $site['vendor']; ?></span>
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>

                        <div class="field_container field_small">
                        <div class="field_label label">
						<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Modelo':'Model' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                            <span style="float:left;"><?php echo $site['model'] ?>  </span> 
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
                            <span style="float:left;"><?php echo $site['msc'] ?></span>
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
                        <span style="float:left;">  <?php echo $site['mgw']; ?> </span>
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
                            <span style="float:left;"><?php echo $site['bsc'] ?></span>
                            </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>

                        <div class="field_container field_small">
                        <div class="field_label label"><span title="">RNC : </span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                            <span style="float:left;"><?php echo $site['rnc'] ?></span>
                            </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>

                        </fieldset>
                        </td>

                            <td style="vertical-align:top; width:50%">
                            <div class="details">
                            </div>
                        <fieldset>
                        <legend><?php echo ($jsonData['language']=='PT BR')? 'Localização':'Localization' ?></legend>
                        <div class="details">
                        <div class="field_container field_small">
                        <div class="field_label label">
						<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey" style="width: auto;">
                           <span style="float:left;"><?php echo $site['province'] ?></span>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label">
						<span title=""><?php echo ($jsonData['language']=='PT BR')? 'Municipal':'Municipal' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                         <div class="field_input_zone field_input_string">
                            <span style="float:left;"><?php echo $site['munciple'] ?></span>                           
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
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                            <span style="float:left;"><?php echo $site['locality'] ?></span>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Latitude':'Latitude' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left"><?php echo $site['lat'] ?></span>
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
                        <span style="float:left"><?php echo $site['lng'] ?></span> 
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        </fieldset>


                        <fieldset>
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
                        <span style="float:left;"><?php echo $site['phase'] ?></span>
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Data de serviço':'Service Date ' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php echo date('Y-m-d',strtotime($site['service_date'])) ?></span>
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Etapa':'Stage' ?>  : </span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"> <?php echo $site['stage']; ?></span>
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
                        <span style="float:left;"><?php echo $site['sub_stage']; ?></span>
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
                        <span style="float:left;"><?php echo date('Y-m-d',strtotime($site['start_date'])) ?></span>
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>

                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Data final':'End Date' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php echo date('Y-m-d',strtotime($site['end_date'])) ?></span>
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
            </div>


            <div id="tabs-2">
                
                

                <div id="linkedset_sites_list"><table class="listResults siteTbl" id="siteTbl"><thead><tr>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Código do site':'Site Code' ?></th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Nome do site':'Site Name' ?></th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?></th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Área Responsável':'Responsible Area' ?></th>
				<th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Data de criação':'Created Date' ?></th>
				</tr></thead><tbody id="siteTBody">
                
                <?php
                    $queryAgr = "SELECT * FROM ntsites WHERE is_active = 1 AND parent_site=".$site['site_id'];
                    $addedSitesList = $conf->query($queryAgr);

                    if($addedSitesList){

                        if($addedSitesList->num_rows>0){

                            while($siterow = mysqli_fetch_array($addedSitesList,MYSQLI_ASSOC)){

                                $query1 = "SELECT * FROM ntsites WHERE site_id = ".$siterow['site_id'];
                                $result1 = mysqli_query($conf, $query1);
                                $siteDet = mysqli_fetch_all($result1, MYSQLI_ASSOC);

                                $query2 = "SELECT province FROM ntsiteprovince WHERE province_id = ".$siteDet[0]['province'];
                                $result2 = mysqli_query($conf, $query2);
                                $provinceInf = '';
                                if($result2){
                                    $temp = mysqli_fetch_all($result2, MYSQLI_ASSOC);
                                    $provinceInf = $temp[0]['province'];
                                }

                                $siteList .= "<tr><td><a href='https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=NewCI&c[feature]=SiteInformation&id=".$siteDet[0]['site_id']."' target='__change' class='siteDetails' id='".$siteDet[0]['site_id']."'>".$siteDet[0]['site_code']."</a></td><td>".$siteDet[0]['site_name']."</td><td>".$provinceInf."</td><td>".$siteDet[0]['responsible_area']."</td><td>".date('d M Y',strtotime($siteDet[0]['created_date']))."</td></tr>";
                            }
                        }
                        else{
                            $siteList .= '<tr><td colspan="5" style="text-align: center;">'.($jsonData['language']=='PT BR'?'Nenhum site disponível':'No sites available').'</td></tr>';
                        }
                        echo $siteList;
                    }
                ?>
                </tbody></table></div>

            </div>
            

            <div id="tabs-3">
                <table class="listResults" style="width: 100%;">
                    <thead>
                        <th><?php echo ($jsonData['language']=='PT BR')? 'Encontro':'Date' ?></th>
                        <th><?php echo ($jsonData['language']=='PT BR')? 'Do utilizador':'User' ?></th>
                        <th><?php echo ($jsonData['language']=='PT BR')? 'Alterar':'Changes' ?></th>
                    </thead>
                    <tbody>
                        <?php
                            $query1 = "SELECT associated_user,created_date,class_id FROM `ntsitehistory` WHERE `is_active` = 1 AND `site_id` = $siteid GROUP BY class_id ORDER BY created_date DESC";
                            $result1 = mysqli_query($conf, $query1);
                            while($aDBInfo = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
                        ?>
                            <tr>
                                <td> <?php echo $aDBInfo['created_date']; ?> </td>
                                <td> <?php echo $aDBInfo['associated_user']; ?> </td>
                                <td>
                                    <?php 
                                        $query2 = "SELECT action,finalclass FROM `ntsitehistory` WHERE `is_active` = 1 AND `site_id` = $siteid AND class_id = ".$aDBInfo['class_id']." ORDER BY created_date DESC";
                                        $result2 = mysqli_query($conf, $query2);
                                        echo "<ul>";
                                        while($aDBInfo2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
                                            switch ($aDBInfo2['action']) {
                                                case 'created': echo "<li>Object Created</li>";break;
                                                case 'Updated':
                                                case 'modified': echo "<li>Object Modified</li>";break;
                                                case 'assigned': echo "<li>Site Assigned To Ticket ".$aDBInfo['class_id']."</li>";break;
                                                case 'revoked': echo "<li>Site revoked From Ticket ".$aDBInfo['class_id']."</li>";break;
                                                 case 'RevokedParent': 
                                                 case 'AssignedParent': 
                                                 $q1 = "SELECT site_name FROM ntsites WHERE site_id=".$aDBInfo['class_id'];
                                                 $res1 = $conf->query($q1);
                                                 $sitename = '';
                                                 if($res1){
                                                    if($res1->num_rows>0){
                                                        $arr1 = mysqli_fetch_all($res1,MYSQLI_ASSOC);
                                                        $sitename = $arr1[0]['site_name'];
                                                    }
                                                 }
                                                 $action = $aDBInfo2['action']=='AssignedParent'? 'Assigned':'Revoked';
                                                 echo "<li>$action Parent Site : $sitename</li>";break;
                                                default: break;
                                            }
                                        }
                                        echo "</ul>";
                                    ?>
                                </td>
                            </tr>
                        <?php           
                            }
                        ?>
                    </tbody>
                </table>
                
            </div>
        </div>

    </form>
    </div>
</div>
<?php }else{
    echo "<h2> Site Not Found </h2>";
} ?>

    <!-- </form> -->

<!-- <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="0" aria-controls="tab_00" aria-labelledby="ui-id-1" aria-selected="true" aria-expanded="true">
    <a href="http://localhost/nt3live/pages/UI.php?operation=new&amp;class=Incident&amp;c%5Bmenu%5D=NewIncident#tab_00" class="tab ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-1"><span>Properties</span></a>
</li>
<li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tab_01" aria-labelledby="ui-id-2" aria-selected="false" aria-expanded="false">
    <a href="http://localhost/nt3live/pages/UI.php?operation=new&amp;class=Incident&amp;c%5Bmenu%5D=NewIncident#tab_01" class="tab ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-2"><span>Affected elements</span></a>
</li>
</ul> -->