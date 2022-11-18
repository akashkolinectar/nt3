<?php 
require_once('approot.inc.php');
require_once(APPROOT.'application/utils.inc.php');
require_once('webservices/wbdb.php');
if(isset($_POST['action']) && isset($_POST['id']) && isset($_POST['uid'])){
  
  $data = array('flag'=>false,'msg'=>"No data found");
  $qry = CMDBSource::Query("UPDATE ntchange_approver SET status=".$_POST['action']." WHERE user_id=".$_POST['uid']." AND ticket_id=".$_POST['id']);
  if($qry){
    $data = array('flag'=>true,'msg'=>'Change '.($_POST['action']==2? "Approved":"Rejected"));
  }
  echo json_encode($data);
}else{
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/light-grey.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<style type="text/css">
	
  html { overflow-y: scroll; }

  .field_container > div.field_label {
    width: 55%;
  }
	
.tblTit{
  color: #422462;
    font-weight: bold;
    font-size: 12pt;
    font-family: Verdana, Arial, Helvetica, Sans-Serif;
}
.container {
    width: 100%;
    padding-right: 0px;
    padding-left: 0px;
    }
    td{
      padding-top:11px
    }
    legend{
      width: 40%;
      margin-left: 10px;
    }
    .field_label span,.field_value table>tbody>tr>td,.field_label span p{
      padding-left: 5px;
    }
    .actions_button .approve{
      background-color: #028202;
      margin: 10px;
    }
    .actions_button .reject{
      background-color: #d30606;
      margin: 10px;
    }
   /* .table td, .table th {
    padding: 5px;
    vertical-align: middle;
  }*/
  /*td {
    font-family: Tahoma, Verdana, Arial, Helvetica;
    font-size: 13px;
    color: #696969;
    padding-top: 11px;
  }
  th {
    font-family: Tahoma, Verdana, Arial, Helvetica;
    font-size: 11pt;
    color: #422462;
    height: 25px;
    background: #e2e2e2 bottom repeat-x;
    text-align: left;
  }
  td a, td a:visited {
    text-decoration: none;
    color: #8d63b9;
}*/
/*td span {
    color: white;
    padding: 4px 8px 4px 8px;
    font-size: 12px;
    border-radius: 3px;
}*/
ul {
  list-style-type: none;
 }
.ui-widget-content {
    border: 1px solid #dddddd;
    background: #ffffff;
    color: #696969;
}
fieldset{
    border: 1px solid #d0d0d0;
    border-radius: 4px;
    margin: 0px 5px 15px 5px;
    width: auto;
}
</style>
<body>
<div class="container">
<br><br>
<div class="table-responsive">
<table class="table">
   <tr>
    <td style="vertical-align: middle;border: none;width: 34%;">
      <h2 class="tblTit">Aprovação de mudança</h2>
    </td>
     <td style="border: none;text-align: center;" colspan="3">
       <img src="https://nt3dg.nectarinfotel.com/images/nt3-logo.png?t=1538568981.6184" title="NT3" style="border:0; width:100px;text-align: center;">
    </td>
     <td style="border: none;text-align: right;" colspan="2">
    
        <img src="https://nt3dg.nectarinfotel.com/images/movicel.jpg?t=1538568981.6184" title="movicel" style="border:0; margin-top:0px;width:120px;">
    </td>
  </tr>

</table>
</div>

<?php

$changeData = array();
if(isset($_GET['id']) && $_GET['id']!='' && isset($_GET['ud']) && $_GET['ud']!=''){

  $checkUsr = CMDBSource::QueryToArray("SELECT * FROM ntchange_approver WHERE user_id=".$_GET['ud']." AND ticket_id=".$_GET['id']);
  if(empty($checkUsr)){
    echo "<h5 style='text-align:center;padding-top:100px;'>Unauthorized User</h5>";
    exit();
  }
  $action = $checkUsr[0]['status'];
  $changeData = CMDBSource::QueryToArray("SELECT tk.*,org.name as orgname,pr.province,rs.reason,srs.sub_reason,ev.event,ct.category,IF(callerper.first_name!='',CONCAT(callerper.first_name,' ',callercnt.name),'') as callername,IF(per.first_name!='',CONCAT(per.first_name,' ',cnt.name),'') as agent,IF(perchsup.first_name!='',CONCAT(perchsup.first_name,' ',cntchsup.name),'') as supervisor,IF(perchman.first_name!='',CONCAT(perchman.first_name,' ',cntchman.name),'') as manager,ch.status as status
                  FROM ntticket tk 
                  LEFT JOIN ntchange ch ON ch.id=tk.id 
                  LEFT JOIN ntorganization org ON org.id=tk.org_id 
                  LEFT JOIN ntsiteprovince pr ON pr.province_id=tk.province_id 
                  LEFT JOIN ntreason rs ON rs.reason_id=tk.reason_id 
                  LEFT JOIN ntsubreason srs ON srs.sub_reason_id=tk.sub_reason_id 
                  LEFT JOIN ntevent ev ON ev.event_id=tk.event_id
                  LEFT JOIN ntcategory ct ON ct.category_id=tk.category_id
                  LEFT JOIN ntcontact cnt ON (cnt.id=tk.agent_id AND cnt.finalclass='Person')
                  LEFT JOIN ntperson per ON per.id=cnt.id
                  LEFT JOIN ntcontact callercnt ON (callercnt.id=tk.caller_id AND callercnt.finalclass='Person')
                  LEFT JOIN ntperson callerper ON callerper.id=callercnt.id
                  LEFT JOIN ntcontact cntchsup ON (cntchsup.id=ch.supervisor_id AND cntchsup.finalclass='Person')
                  LEFT JOIN ntperson perchsup ON perchsup.id=cntchsup.id
                  LEFT JOIN ntcontact cntchman ON (cntchman.id=ch.manager_id AND cntchman.finalclass='Person')
                  LEFT JOIN ntperson perchman ON perchman.id=cntchman.id
                  WHERE tk.id=".$_GET['id']);
}
if(!empty($changeData)){
  /*echo "<pre>";
  print_r($changeData);*/
  $change = $changeData[0];
  $caller = $change['callername'];
?>
<br/><br/>
<?php 
if($action==1){ ?>
<div class="actions_button">
  <button class="action reject">reject</button>
  <button class="action approve">Approve</button>
</div>
<?php }else{
  echo ($action==2)? "<h5 style='float:right;color:green;padding: 10px;'>Approved</h5>":"<h5 style='float:right;color:red;padding: 10px;'>Rejected</h5>";
} ?>

<div class="ui-widget-content">
  <table style="vertical-align:top" class="n-cols-details 3-cols-details" data-mode="view">
    <tbody><tr><td style="vertical-align:top; width:33%" class=""><div class="details" id="search-widget-results-outer">
</div>
<fieldset><div class="details" id="search-widget-results-outer">
<div class="field_container field_small" data-attcode="change_type">
<div class="field_label label" id="lbltipAddedComment"><span> Change Type </span></div>
<div class="field_data">
<div class="field_value"><label style="color:#f17422;padding-top:5px;"><?php echo $change['finalclass']; ?></label></div>
</div>
</div>
</div>
</fieldset><fieldset><legend>General Information</legend><div class="details" id="search-widget-results-outer">
<div class="field_container field_small">
<div class="field_label label" id="lbltipAddedComment"><span>Ref</span></div>
<div class="field_data">
<div class="field_value"><?php echo $change['ref']; ?></div>
</div>
</div>
<div class="field_container field_small" data-attcode="org_id">
<div class="field_label label" id="lbltipAddedComment"><span>Area</span></div>
<div class="field_data">
<div class="field_value"><?php echo $change['orgname']; ?></div>
</div>
</div>
<div class="field_container field_small" data-attcode="status">
<div class="field_label label" id="lbltipAddedComment"><span>Status</span></div>
<div class="field_data">
<div class="field_value"><?php echo $change['status'] ?></div>
</div>
</div>
<div class="field_container field_small" data-attcode="title">
<div class="field_label label" id="lbltipAddedComment"><span title="">Title</span></div>
<div class="field_data">
<div class="field_value"><?php echo $change['title'] ?></div>
</div>
</div>
<div class="field_container field_large" data-attcode="description">
<div class="field_label label" id="lbltipAddedComment"><span title=""><?php echo $change['description'] ?></span></div>
<div class="field_data">
<div class="field_value"><div class="HTML"></div></div>
</div>
</div>
</div>
</fieldset>

<fieldset><legend>Approver</legend>
  <div class="details" id="search-widget-results-outer">
    <?php 

    $approvers = CMDBSource::QueryToArray("SELECT apr.status,CONCAT(per.first_name,' ',cnt.name) as approver FROM ntchange_approver apr LEFT JOIN ntcontact cnt ON (cnt.id=apr.user_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id WHERE apr.is_active = 1 AND apr.ticket_id=".$_GET['id']);
    if(!empty($approvers)){
      foreach ($approvers as $rows) {
    ?>

    <div class="field_container field_small">
      <div class="field_label label" id="lbltipAddedComment"><span><?php echo $rows['approver'] ?></span></div>
        <div class="field_data">
        <div class="field_value">
          <?php
            $stat = ($rows['status']==1)? "<span style='color:gray'>Pending</span>":(($rows['status']==2)? "<span style='color:green'>Approved</span>":"<span style='color:red'>Rejected</span>");
            echo $stat;
          ?>
        </div>
      </div>
    </div>

    <?php
      }
    }
    ?>
    
  </div>
</fieldset>

</td><td style="vertical-align:top; width:33%" class=""><div class="details" id="search-widget-results-outer">
</div>
<fieldset>
  <legend>Network</legend>
  <div class="details" id="search-widget-results-outer">
  <div class="field_container field_small" data-attcode="network_type">
    <div class="field_label label" id="lbltipAddedComment"><span>Technologies </span></div>
      <div class="field_data">
        <div class="field_value">
          <div id="network_dv">
            <table>
              <tbody>
                <tr>
                  <?php 
                    $nwData = CMDBSource::QueryToArray("SELECT nw.network FROM ntticketnetworks nw WHERE nw.ticket_id=".$_GET['id']." AND nw.is_active=1");
                    if(!empty($nwData)){
                      foreach ($nwData as $rows) {
                  ?>
                    <td style="border-radius: 0px 5px 5px 0px;"><?php echo $rows['network']; ?></td>
                  <?php
                      }
                    }else{
                      echo "<td style=\"border-radius: 0px 5px 5px 0px;\"> NA </td>";
                    }
                  ?>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
  </div>
</div>
<div class="details" id="search-widget-results-outer">
<div class="field_container field_large" data-attcode="affected_services">
<div class="field_label label" id="lbltipAddedComment"><span> Affected Services </span></div>
<div class="field_data">
<div class="field_value">
  <table>
    <tbody>
      <?php 
          $service_aftd = CMDBSource::QueryToArray("SELECT sr.service_aftd FROM ntticketserviceaffected ts LEFT JOIN ntserviceaftd sr ON sr.service_aftd_id=ts.service_aftd_id  WHERE ts.ticket_id=".$_GET['id']." AND ts.is_active=1");
          if(!empty($service_aftd)){
            foreach ($service_aftd as $rows) {
        ?>
         <td style="width:180px;"><?php echo $rows['service_aftd']; ?></td>
        <?php
            }
          }else{
            echo "<td style=\"width:180px;\"> NA </td>";
          }
        ?>
    </tbody>
  </table>
</div>
</div>
</div>
</div>
<div class="details" id="search-widget-results-outer">
<div class="field_container field_small" data-attcode="province">
<div class="field_label label" id="lbltipAddedComment"><span>Province </span></div>
<div class="field_data">
<div class="field_value"><?php echo $change['province']; ?></div>
</div>
</div>
</div>
</fieldset><fieldset><legend>Reason</legend><div class="details" id="search-widget-results-outer">
<div class="field_container field_small" data-attcode="reason">
<div class="field_label label" id="lbltipAddedComment"><span>Reason </span></div>
<div class="field_data">
<div class="field_value"><?php echo $change['reason']; ?></div>
</div>
</div>
</div>
<div class="details" id="search-widget-results-outer">
<div class="field_container field_small" data-attcode="sub_reason">
<div class="field_label label" id="lbltipAddedComment"><span>Sub Reason </span></div>
<div class="field_data">
<div class="field_value"><?php echo $change['sub_reason']; ?></div>
</div>
</div>
</div>
<div class="details" id="search-widget-results-outer">
<div class="field_container field_small" data-attcode="event">
<div class="field_label label" id="lbltipAddedComment"><span>Event </span></div>
<div class="field_data">
<div class="field_value"><?php echo $change['event']; ?></div>
</div>
</div>
</div>
<div class="details" id="search-widget-results-outer">
<div class="field_container field_small" data-attcode="category">
<div class="field_label label" id="lbltipAddedComment"><span>Category </span></div>
<div class="field_data">
<div class="field_value"><?php echo $change['category']; ?><br></div>
</div>
</div>
</div>
</fieldset>
</td>
<td style="vertical-align:top; width:33%" class=""><div class="details" id="search-widget-results-outer">
</div>

<fieldset>
  <legend>Contacts</legend>
  <div class="details" id="search-widget-results-outer">
  
<div class="details" id="search-widget-results-outer">
<div class="field_container field_small">
<div class="field_label label" id="lbltipAddedComment"><span>Reported By </span></div>
<div class="field_data">
<div class="field_value"><?php echo $change['callername']; ?><br></div>
</div>
</div>
</div>

<div class="field_container field_small" data-attcode="team_id">
<div class="field_label label" id="lbltipAddedComment"><span title="">Agent</span></div>
<div class="field_data">
<div class="field_value"><span class="object-ref " title="Team::194"><?php echo $change['agent']; ?></span></div>
</div>
</div>
<div class="field_container field_small" data-attcode="supervisor_group_id">
<div class="field_label label" id="lbltipAddedComment"><span title="">Supervisor</span></div>
<div class="field_data">
<div class="field_value"><span class="object-ref " title="Team::148"><?php echo $change['supervisor']; ?></span></div>
</div>
</div>
<div class="field_container field_small" data-attcode="manager_group_id">
<div class="field_label label" id="lbltipAddedComment"><span title="">Manager</span></div>
<div class="field_data">
<div class="field_value"><span class="object-ref " title="Team::172"><?php echo $change['manager']; ?></span></div>
</div>
</div>
</div>
</fieldset>

<fieldset><legend>Dates</legend><div class="details" id="search-widget-results-outer">
<div class="field_container field_small" data-attcode="creation_date">
<div class="field_label label" id="lbltipAddedComment"><span title="">Creation date</span></div>
<div class="field_data">
<div class="field_value"><?php echo $change['start_date']; ?></div>
</div>
</div>
<div class="field_container field_small" data-attcode="last_update">
<div class="field_label label" id="lbltipAddedComment"><span title="">Last update</span></div>
<div class="field_data">
<div class="field_value"><?php echo date('d-m-Y h:i a',strtotime($change['last_update'])); ?></div>
</div>
</div>
</div>
</fieldset>
</td>
</tr>
<tr>
  <td colspan="3">
    <fieldset>
      <legend style="width: 20% !important;">Documents</legend>
      <div class="details" id="search-widget-results-outer">
        <div class="field_container field_small">
          <?php
            $docQuery = CMDBSource::QueryToArray("SELECT at.*,chat.filename FROM ntattachment at LEFT JOIN ntpriv_changeop_attachment_added chat ON chat.attachment_id=at.id WHERE at.item_class='".$change['finalclass']."' && at.item_id=".$change['id']);
            if(!empty($docQuery)){
          ?>
            <ul class="list-group list-group-horizontal">
          <?php
              foreach ($docQuery as $rows) {
          ?>
              <li class="list-group-item">
              <?php
               /* $oPage = new ajax_page("");
                $oPage->no_cache();
                $sSecret = utils::ReadParam('s', '');
                ormDocument::DownloadDocument($oPage, 'InlineImage', $rows['id'], 'contents', 'inline', 'secret', $sSecret);*/
                //header("Content-Type:" . $a['mime']);
                //echo '<a href="https://nt3.nectarinfotel.com/pages/ajax.document.php?operation=download_document&amp;class=Attachment&amp;field=contents&amp;id='.$rows["id"].'" target="__change">'.$rows["filename"].'</a>';
                echo '<a href="https://nt3.nectarinfotel.com/webservices/changeDocuments.php?id='.$rows["id"].'" target="__change">'.$rows["filename"].'</a>';
              ?>
                <!-- <object data="data:application/pdf;base64,<?php //echo base64_encode($rows['contents_data']) ?>" type="application/pdf" style="height:200px;width:60%"></object> -->
              </li>
          <?php
              }
          ?>
            </ul>
          <?php
            }else{
              echo "<h5 style='padding-left:10px'> No Document Attached</h5>";
            }
          ?>
          
        </div>
      </div>
    </fieldset>
  </td>
</tr>
</tbody>
</table>
</div>
<script type="text/javascript">
  var id = "<?php echo $_GET['id'] ?>";
  var ud = "<?php echo $_GET['ud'] ?>";
  $(document).on('click','.approve',function(){
    actionFun('2');
  });
  $(document).on('click','.reject',function(){
    actionFun('3');
  });
  function actionFun(action){
    $.ajax({
      url: 'changeApproval.php',
      type: 'POST',
      data: {'action':action,'id':id,'uid':ud},
      dataType: 'json',
      success: function(res){
        if(res.flag){
          alert(res.msg);
          if(action==2){$(".actions_button").html("<h5 style='float:right;color:green;padding: 10px;'>Approved</h5>");}
          if(action==3){$(".actions_button").html("<h5 style='float:right;color:red;padding: 10px;'>Rejected</h5>");}
        }
      },
      error: function(xhr){
        console.log(xhr);
      }
    });
  }
</script>
<?php 
  }
  else{
    echo "<h5 style='text-align:center;padding-top:100px;'>Invalid Ticket</h5>";
  }
}
?>