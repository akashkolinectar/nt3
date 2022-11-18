<?php 

/********** Province ***********/

include('../webservices/wbdb.php'); 
/*include('../pages/UI.php'); */

$postData = file_get_contents("php://input");
$jsonData = json_decode($postData,TRUE);

?>
<script language="javascript" type="text/javascript">
    $(document).ready(function() {
        $("#tabs").tabs();
        $("#tabs > ul").bind("tabsshow", function(event, ui) { 
            window.location.hash = ui.tab.hash;
        });
    });
</script>
<div class="ui-layout-content" style="overflow: auto; position: relative; height: auto; visibility: visible;">
    <h1><img src="https://nt3.nectarinfotel.com/images/addactivity.png" style="vertical-align:middle;    width: 32px;">&nbsp;<?php echo ($jsonData['language']=='PT BR')? 'Status da notificação de falha na entrega':'NDR Status' ?></h1>
    
   <!--  <button type="button" class="action cancel">Cancel</button>&nbsp;&nbsp;&nbsp;&nbsp;
      <button type="submit" class="action"><span>Apply</span></button>
      <button type="submit" name="next_action" value="ev_assign" class="action" style="background-color: #f17422;"><span>Modify</span></button> -->
      <?php
        $userid = 0;
        $q1 = "SELECT contactid FROM ntpriv_user WHERE login = '".$jsonData['auth_user']."'";
        $res1 = mysqli_query($conf, $q1);
        if($res1){
          if($res1->num_rows>0){
            $usr = mysqli_fetch_all($res1,MYSQLI_ASSOC);
            $userid = $usr[0]['contactid'];
          }
        }
        $auser = 0; $amanager = 0;
        
        $query = "SELECT * FROM npactivity WHERE activityid ='".$_GET['id']."'";
        $result = mysqli_query($conf, $query);

        if ($result) {
            if(mysqli_num_rows($result)>0){
               while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

                if($row['accesstype']=='External'){
                  $auser = $row['extemployee'];
                  $amanager = $row['extreportedto'];
                }else{
                  $auser = $row['employee'];
                  $amanager = $row['reportedto'];
                }

                //echo 'User : '.$userid." *** manager : ".$amanager." *** Employee : ".$auser;
                
            if($row['result']=='Completed'){
        ?>

         <button type="button" class="action inprogress_btn1" onclick="window.location.href='https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=editbyactivity&id=<?php echo $_GET['id'] ?>'" style="background-color: #f17422;padding: 5px 17px 5px 17px;"><?php echo ($jsonData['language']=='PT BR')? 'Modificar':'Modify' ?></button>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php
            if($amanager==$userid){
        ?>
                    
      <!-- <button type="button" class="action close_btn1" onclick="updateresult('<?php echo $_GET['id'] ?>_Close')" style="background-color: #222222;padding: 5px 17px 5px 17px;">Close</button>&nbsp;&nbsp;&nbsp;&nbsp; -->
      <button type="submit" class="action reject_btn1" onclick="updateresult('<?php echo $_GET['id'] ?>_Reject')" style="background-color: #CC0000;padding: 5px 17px 5px 17px;"><span><?php echo ($jsonData['language']=='PT BR')? 'Rejeitar':'Reject' ?></span></button>
      <button type="submit" class="action inprogress_btn1" onclick="updateresult('<?php echo $_GET['id'] ?>_Inprogress')" style="background-color: #0c9e04;padding: 5px 17px 5px 17px;"><span><?php echo ($jsonData['language']=='PT BR')? 'Aprovar':'Approve' ?></span></button> 
      <?php } } ?>
      <?php  if($row['result']=='Inprogress'){ ?>
        
      <button type="button" class="action inprogress_btn1" onclick="window.location.href='https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=editbyactivity&id=<?php echo $_GET['id'] ?>'" style="background-color: #f17422;padding: 5px 17px 5px 17px;"><?php echo ($jsonData['language']=='PT BR')? 'Modificar':'Modify' ?></button>&nbsp;&nbsp;&nbsp;&nbsp;  
      <button type="button" class="action close_btn1" onclick="updateresult('<?php echo $_GET['id'] ?>_Close')" style="background-color: #222222;padding: 5px 17px 5px 17px;"><?php echo ($jsonData['language']=='PT BR')? 'Fechar':'Close' ?></button>&nbsp;&nbsp;&nbsp;&nbsp;
      <!-- <button type="submit" class="action reject_btn1" onclick="updateresult('<?php //echo $_GET['id'] ?>_Reject')" style="background-color: #CC0000;padding: 5px 17px 5px 17px;"><span>Reject</span></button><br> -->
      <?php } ?>
      
      <?php  } } }  ?>
   <div id="tabs">
        <ul>
          <li><a href="#tabs-001"><?php echo ($jsonData['language']=='PT BR')? 'Informações sobre NDR':'NDR Info' ?></a></li>
          <li><a href="#tabs-site"><?php echo ($jsonData['language']=='PT BR')? 'Site afetado':'Affected Site' ?></a></li>
          <li><a href="#tabs-002"><?php echo ($jsonData['language']=='PT BR')? 'História':'History' ?></a></li>
          
        </ul>
  <!-- ************* Tab 1 Start ***********-->
  <div id="tabs-001">
 <!-- <div class="table-responsive status">
      <form action="#" method="">-->
      <?php  $query = "SELECT npactivity.activityid,npactivity.description,ntsiteprovince.province,ntsitemunciple.munciple,nplocation.locationname,npreason.reason_name,npactivity.reason,npactivity.fuel_found,npactivity.fuel_filled,npactivity.accesstype,(ntcontract.name) as service,(ntservice.name) as provider,(ng2.name) as intarea,np1.first_name,nc1.name,npactivity.result,npactivity.created_date FROM npactivity join ntsiteprovince on npactivity.province=ntsiteprovince.province_id left join ntsitemunciple on npactivity.munciple=ntsitemunciple.munciple_id left join nplocation on npactivity.location=nplocation.locationid left join npreason on npactivity.selectedreason=npreason.npreasonid 
                left join ntservice on npactivity.provider=ntservice.id 
                left join ntcontract on npactivity.service=ntcontract.id
                left join ntorganization on npactivity.provider=ntorganization.id 
                left join ntorganization ng2 on npactivity.movicelarea=ng2.id  
                left JOIN ntcontact nc1 on npactivity.extemployee=nc1.id OR  npactivity.employee=nc1.id 
                left JOIN ntperson np1 on npactivity.extemployee=np1.id OR npactivity.employee=np1.id 
                where npactivity.status='1' AND npactivity.activityid ='".$_GET['id']."' GROUP BY npactivity.activityid ORDER BY npactivity.activityid DESC";
            if($query!=''){
      $result = mysqli_query($conf, $query);
      
      if ($result) {
   
            if(mysqli_num_rows($result)>0){
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
    <!--<table class="table">
    <tr>
    <td>
    </td>
   
    
    <td><?php if($row['result']=='Completed'){ ?>
       <h4>Current Status:</h4> <button class="approval_btn1" style="pointer-events: none;">Waiting for Approval</button>
    </td>
    <td>   
       <h4>Change Status:</h4> <button class="inprogress_btn1" onclick="updateresult('<?php echo $row['activityid'] ?>_Inprogress')">Inprogress</button>
       <button class="reject_btn1" onclick="updateresult('<?php echo $row['activityid'] ?>_Reject')">Reject</button>
    <?php } ?>
    <?php if($row['result']=='Inprogress'){ ?>
      <h4> Current Status:</h4><button class="inprogress_btn1" style="pointer-events: none;">Inprogress</button><br>
       <h4>Change Status:</h4> <button class="close_btn1" onclick="updateresult('<?php echo $row['activityid'] ?>_Close')">Close</button>
       <button onclick="updateresult('<?php echo $row['activityid'] ?>_Reject')" class="reject_btn1">Reject</button>
    <?php } ?>
    <?php if($row['result']=='Close'){ ?>
      <h4> Current Status:</h4> <button style="pointer-events: none;" class="close_btn1">Close</button>
    <?php } ?>
    <?php if($row['result']=='Reject'){ ?>
      <h4> Current Status:</h4> <button style="pointer-events: none;" class="reject_btn1">Reject</button>
    <?php } ?></td>
    
    </div>
    </div>
    
    
    </tr>
    </table>
      
        </form>
        </div> -->
        <div class="table-responsive" id="siteContent">
                    <table class="table" style="vertical-align:top;width:100%">
                        <tbody>
                            <tr>
                            <td style="vertical-align:top; width:50%">
                            <div class="details">
                            </div>
                        <fieldset>
                        <legend><?php echo ($jsonData['language']=='PT BR')? 'Status da notificação de falha na entrega':'NDR Status' ?></legend>
                        <div class="details">
                          <?php  if($row['result']=='Completed'){ ?>
                        <div class="field_container field_small">
                        <div class="field_label label">
                        <span title=""><?php echo ($jsonData['language']=='PT BR')? 'Status atual':'Current Status' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                            <span style="float:left;color: #00CC00;"><?php echo ($jsonData['language']=='PT BR')? 'Esperando aprovação':'Waiting for Approval' ?></span>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <?php } ?>
                         <?php  if($row['result']=='Inprogress'){ ?>
                        <div class="field_container field_small">
                        <div class="field_label label">
                        <span title=""><?php echo ($jsonData['language']=='PT BR')? 'Status atual':'Current Status' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                            <span style="float:left;color: #f9ac3a;"><?php echo ($jsonData['language']=='PT BR')? 'Em progresso':'Inprogress' ?> </span>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <?php } ?>
                         <?php  if($row['result']=='Close'){ ?>
                        <div class="field_container field_small">
                        <div class="field_label label">
                        <span title=""><?php echo ($jsonData['language']=='PT BR')? 'Status atual':'Current Status' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                            <span style="float:left;color: #222222;"><?php echo ($jsonData['language']=='PT BR')? 'Fechar':'Close' ?> </span>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <?php } ?>
                         <?php  if($row['result']=='Reject'){ ?>
                        <div class="field_container field_small">
                        <div class="field_label label">
                        <span title=""><?php echo ($jsonData['language']=='PT BR')? 'Status atual':'Current Status' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                            <span style="float:left;color: #CC0000;"><?php echo ($jsonData['language']=='PT BR')? 'Rejeitar':'Reject' ?> </span>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <?php } ?>
                        </fieldset>

                        <fieldset>
                        <legend><?php echo ($jsonData['language']=='PT BR')? 'Detalhes da notificação de falha na entrega':'NDR Details' ?></legend>
                        <div class="details">
                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Descrição':'Description' ?>  : </span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                            <span style="float:left;"><?php echo $row['description']; ?></span>
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?>  :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php echo''.utf8_encode($row['province']).'' ; ?></span>
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>


                        <!-- <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php // echo ($jsonData['language']=='PT BR')? 'Municipal':'Municipal' ?>  :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                            <span style="float:left;"><?php // echo''.utf8_encode($row['munciple']).'' ; ?></span> 
                            </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>

                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php // echo ($jsonData['language']=='PT BR')? 'Localização':'Location' ?>  :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                            <span style="float:left;"><?php // echo''.utf8_encode($row['locationname']).'' ; ?></span> 
                            </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div> -->



                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Razão':'Reason' ?>  :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                            <span style="float:left;"><?php echo''.utf8_encode($row['reason_name']).'' ; ?></span> 
                            </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Outra razão':'Other Reason' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                            <span style="float:left;"><?php echo''.utf8_encode($row['reason']).'' ; ?></span> 
                            </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
 <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Combustivel Encontrado (Litros)':'Fuel Found' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                            <span style="float:left;"><?php echo''.utf8_encode($row['fuel_found']).'' ; ?></span> 
                            </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                         <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Combustivel Abastecido (Litros)':'Fuel Filled' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                            <span style="float:left;"><?php echo''.utf8_encode($row['fuel_filled']).'' ; ?></span> 
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
                        <legend><?php echo ($jsonData['language']=='PT BR')? 'Informações sobre o tipo de acesso':'Access Type Info' ?></legend>
                        <div class="details">
                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Tipo de acesso':'Access Type ' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey" style="width: auto;">
                           <span style="float:left;"><?php echo'<p>'.$row['accesstype'].'</p>' ; ?></span>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                      </div>
                        
                        </fieldset>
<?php  if($row['accesstype']=='Internal'){ ?>

                        <fieldset>
                        <legend><?php echo ($jsonData['language']=='PT BR')? 'Acesso Interno':'Internal Access' ?></legend>
                        <div class="details">
                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Área':'Area' ?>  :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php echo''.utf8_encode($row['intarea']).'' ; ?></span>
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Empregada':'Employee' ?> :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php echo''. ($row['accesstype']==='Internal'? utf8_encode($row['first_name'].' '.$row['name']):utf8_encode($row['first_name'].' '.$row['name'])) .'' ; ?></span>
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                       <!--  <div class="field_container field_small">
                        <div class="field_label label"><span title="">Reported To  : </span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php //echo'<p>'. ($row['Internal']==='External'?$row['first_name'].' '.$row['name']:$row['first_name'].' '.$row['name']) .'</p>' ; ?></span>
                            </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div> -->
                        </fieldset>
                        <?php } if($row['accesstype']=='External'){ ?>
                        <fieldset>
                        <legend><?php echo ($jsonData['language']=='PT BR')? 'Acesso externo':'External Access' ?></legend>
                        <div class="details">
                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Fornecedor':'Provider' ?>  :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php echo''.utf8_encode($row['provider']).'' ; ?></span>
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Serviço':'Service' ?>  :</span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php echo''.utf8_encode($row['service']).'' ; ?></span>
                        </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                        <div class="field_container field_small">
                        <div class="field_label label"><span title=""><?php echo ($jsonData['language']=='PT BR')? 'Modificar':'Employee' ?>   : </span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php echo''. ($row['accesstype']==='External'? utf8_encode($row['first_name'].' '.$row['name']):utf8_encode($row['first_name'].' '.$row['name'])) .'' ; ?></span>
                            </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div>
                       <!--  <div class="field_container field_small">
                        <div class="field_label label"><span title="">Reported To : </span></div>
                        <div class="field_data">
                        <div class="field_value">
                        <div class="field_value_container">
                        <div class="attribute-edit">
                        <div class="field_input_zone field_input_extkey">
                        <div class="field_select_wrapper">
                        <span style="float:left;"><?php //echo'<p>'. ($row['accesstype']==='External'?$row['first_name'].' '.$row['name']:$row['first_name'].' '.$row['name']) .'</p>' ; ?></span>
                            </div>
                        </div>
                        </div></div></div>
                        </div>
                        </div> -->
                        </fieldset>
<?php } ?>
                        </td>

                        </tr>
                        </tbody>
                    </table>
                </div>
  </div>  
  <?php } } } }  ?>
  <!-- *************End Tab 1  ***********-->
  
  <!-- *************Tab Site  ***********-->
  <div id="tabs-site">
    <div id="linkedset_sites_list">
    <table class="listResults siteTbl" id="siteTbl">
    <thead>
    <tr>
    <th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Código do site':'Site Code' ?></th>
    <th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Nome do site':'Site Name' ?></th>
    <th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Província':'Province' ?></th>
    <th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Área Responsável':'Responsible Area' ?></th>
    <th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Data de criação':'Created Date' ?></th>
    </tr>
    </thead>
    <tbody id="siteTBody">
    
    <?php
      $queryAgr = "SELECT ST.* FROM ntactivitysite ACSI LEFT JOIN ntsites ST ON ST.site_id=ACSI.site_id WHERE ACSI.is_active = 1 AND ACSI.activity_id =".$_GET['id'];

        $addedSitesList = $conf->query($queryAgr);

        if($addedSitesList){

            if($addedSitesList->num_rows>0){

                while($siteDet = mysqli_fetch_array($addedSitesList,MYSQLI_ASSOC)){

                    $query1 = "SELECT province FROM ntsiteprovince WHERE province_id = ".$siteDet['province'];
                    $result1 = mysqli_query($conf, $query1);
                    $temp = mysqli_fetch_all($result1, MYSQLI_ASSOC);

                    $siteList .= "<tr><td><a href='https://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=NewCI&c[feature]=SiteInformation&id=".$siteDet['site_id']."' target='__change' class='siteDetails' id='".$siteDet['site_id']."'>".$siteDet['site_code']."</a></td><td>".$siteDet['site_name']."</td><td>".$temp[0]['province']."</td><td>".$siteDet['responsible_area']."</td><td>".date('d M Y',strtotime($siteDet['created_date']))."</td></tr>";
                }
            }
            else{
                $siteList .= '<tr><td colspan="5" style="text-align: center;">'.($jsonData['language']=='PT BR'?'Nenhum site disponível':'No sites available').'</td></tr>';
            }
            echo $siteList;
        }
    ?>
          </tbody>
        </table>
      </div>
    </div>
<!-- ************* end Tab Site  ***********-->    
            
  <!-- ***************** Tab 2 Start ************** -->
  <div id="tabs-002">

      <table class="table listResults table-striped activtabl1" id="siteTbl">
            <thead>
            <tr> 
              <th class="header" style="display:none;"><?php echo ($jsonData['language']=='PT BR')? 'Eu iria':'Id' ?></th>
                <th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Encontro':'Date' ?></th>
                <th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Do utilizador':'User' ?></th>
                <th class="header"><?php echo ($jsonData['language']=='PT BR')? 'Alterar':'Changes' ?></th>
                
            </tr>
      </thead>
      <tbody>
      <?php  $query = "SELECT * FROM acthistory join npactivity on acthistory.activityid=npactivity.activityid where acthistory.activityid='".$_GET['id']."'";
      if($query!=''){
      $result = mysqli_query($conf, $query);
      //print_r($result); exit;
      if ($result) {
      //echo $query;
      if(mysqli_num_rows($result)>0){
      //print_r(mysqli_fetch_all($result, MYSQLI_ASSOC));
      //$data = array('flag'=>true,'msg'=>'Data found','info'=>mysqli_fetch_all($result, MYSQLI_ASSOC));
      /**/    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
       <?php   echo'
        <tr>
        <td style="display:none;">'.$row['acthistoryid'].'</td>
        <td>'.date('d M Y (h:i a)',strtotime($row['Date'])).'</td>
        <td>'.$row['user'].'</td>
        <td>'.$row['changes'].'</td>
        </tr>
        '; ?>
        <?php } } } }  ?>
      </tbody>
      </table>
    
    
  <script>
    function updateresult(activityid){
    var strArray = activityid.split('_');
    var actid=strArray[0];
    var result=strArray[1];
       //alert(actid);
       //alert(result);
    if(actid=='' && result==''){
    alert('Empty');
    window.location= "https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity";
    }else{
         $.ajax({
         type: 'POST',
         url: 'updateresult.php',
         dataType: "json",
         data: JSON.stringify({'actid': actid,'result':result}),
         success: function (data) {
           // alert(data);
    alert('Status Changed Successfully');
    window.location= "https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity";
         }
    });

    }
    }
    </script>
  </div>
  <!-- *************End Tab 2  ***********-->

</div> <!-- tabs div close -->
        
</div><!-- UI-layoutd iv close -->    


  <!-- <div class="formactivity">
    <a class="btn2" href="#" style="float: right;margin: 2px;padding: 5px 23px 5px 23px;border: none;border-radius: 4px;background-color: #422462;color: #ffffff;cursor: pointer;text-decoration: none;">History</a> 
    <a class="btn1" href="#" style="float: right;margin: 2px;padding: 5px 23px 5px 23px;border: none;border-radius: 4px;background-color: #0c9e04;color: #ffffff;text-decoration: none;">View Status</a>
      
    <script>
    $(document).ready(function(){
      
      $(".btn1").click(function(){
      $(".status").show();
      $(".history").hide();
      });
      $(".btn2").click(function(){
      $(".history").show();
      $(".status").hide();
      });
    });
    </script>  -->                                    
    
        

         <!----Mahesh Start Update Result------>

    
     <!----Mahesh End Update Result------>
