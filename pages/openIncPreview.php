<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<style type="text/css">
	.table thead tr th{
		background-color: #e2e2e2;
		color:#422462;
    text-align: left;
    border-bottom: 1px solid #422462;
	}
	.table thead th {
    vertical-align: middle;
    border-bottom: none;
	}
 .table-bordered td, .table-bordered th {
    border: none;
    border-bottom: 1px solid #dee2e6;
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
    .table td, .table th {
    padding: 5px;
    vertical-align: middle;
  }
  td {
    font-family: Tahoma, Verdana, Arial, Helvetica;
    font-size: 13px;
    color: #696969;
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
}
td span {
    color: white;
    padding: 4px 8px 4px 8px;
    font-size: 9px;
    border-radius: 3px;
}
ul {
  list-style-type: none;
 }

</style>
<body>
<?php require_once('../webservices/wbdb.php'); ?>
<div class="container">
<br><br>
<div class="table-responsive">
<table class="table">
   <tr>
    <td style="vertical-align: middle;border: none;width: 34%;">
      <h2 class="tblTit">Painel de Gerência</h2>
    </td>
     <td style="border: none;text-align: center;" colspan="3">
       <img src="https://nt3.nectarinfotel.com/images/nt3-logo.png?t=1538568981.6184" title="NT3" style="border:0; width:100px;text-align: center;">
    </td>
     <td style="border: none;text-align: right;" colspan="2">
    
        <img src="https://nt3.nectarinfotel.com/images/movicel.jpg?t=1538568981.6184" title="movicel" style="border:0; margin-top:0px;width:120px;">
    </td>
  </tr>

</table>
</div>
  
  
  <div class="table-responsive">
    <table class="table table-bordered tcls">
      
      <thead>
        <tr>
          <th style="width: 8%;">Número do Ticket</th>
          <th>Título</th>
          <th>Província</th>
          <th style="width: 21%;">Site Principal</th>
          <th style="width: 10%;">Dependente</th>
          <th style="width: 21%;">Motivo</th>
          <th>2G</th>
          <th>3G</th>
          <th>4G</th>
          <th style="width: 9%;">Duração</th>
        </tr>
      </thead>
      <tbody>

        <?php

          $openTickets = CMDBSource::QueryToArray("SELECT tk.id,tk.ref,tk.title,pr.province,tk.start_date,rs.reason,sbrs.sub_reason FROM ntticket tk LEFT JOIN ntticket_incident inc ON inc.id=tk.id LEFT JOIN ntreason rs ON rs.reason_id=tk.reason_id LEFT JOIN ntsubreason sbrs ON sbrs.sub_reason_id=tk.sub_reason_id LEFT JOIN ntsiteprovince pr ON pr.province_id = tk.province_id WHERE tk.operational_status = 'ongoing' AND tk.finalclass='Incident' ORDER BY tk.id DESC");

          if(!empty($openTickets)){

            foreach ($openTickets as $aDBInfo) {

              $date = new DateTime($aDBInfo['start_date']);
              $now = new DateTime();

              $ageData = $now->diff($date);

              if($ageData->format('%y')!=0){
                  $age = $ageData->format('%a Dia %h Hr');
              }else if($ageData->format('%d')!=0){
                  $age = $ageData->format('%a Dia %h Hr %i Min');
              }else if($ageData->format('%d')!=0){
                  $age = $ageData->format('%a Dia %h Hr %i Min');
              }else if($ageData->format('%d')==0){
                  if($ageData->format('%h')==0){
                      $age = $ageData->format('%i Min');
                  }else{
                      $age = $ageData->format('%h Hr %i Min');
                  }
              }

              $allSites = array();
              $dependantSites = "<ul>"; $sitePricipal = "<ul>";
              
              $sites = CMDBSource::QueryToArray("SELECT st.site_name,st.site_id,st.parent_site FROM ntticketsites ts LEFT JOIN ntsites st ON st.site_id=ts.site_id WHERE ts.ticket_id=".$aDBInfo['id']." AND ts.is_active = 1");

              if(!empty($sites)){
                  $allSites = array_column($sites, 'site_id');
                  foreach ($sites as $rows) {                                  
                      if($rows['parent_site']!=0){
                          if(!in_array($rows['parent_site'], $allSites)){
                              $sitePricipal .= "<li>".$rows['site_name']."</li>";
                          }else{
                              $dependantSites .= "<li>".$rows['site_name']."</li>";
                          }
                      }else{
                          $sitePricipal .= "<li>".$rows['site_name']."</li>";
                      }

                      /************** Check Site Network is Available Or Not ****************/
                      $availableNw = array();
                      $siteNetwork = CMDBSource::QueryToArray("SELECT sn.network,sn.site_id FROM ntsitenetwork sn WHERE sn.site_id=".$rows['site_id']." AND sn.is_active = 1");
                      if(!empty($siteNetwork)){
                        foreach ($siteNetwork as $nRows) {
                          array_push($availableNw, $nRows['network']);
                        }
                      }
                    /************** EOF Check Site Network is Available Or Not ****************/
                  }
              }

               $sitePricipal .= "</ul>";
               $dependantSites .= "</ul>";

              $TWG = (in_array('2G', $availableNw))? "UP":"NA"; $TWGCLR = (in_array('2G', $availableNw))? "#006400":"gray";
              $TRG = (in_array('3G', $availableNw))? "UP":"NA"; $TRGCLR = (in_array('3G', $availableNw))? "#006400":"gray";
              $FRG = (in_array('4G', $availableNw))? "UP":"NA"; $FRGCLR = (in_array('4G', $availableNw))? "#006400":"gray";
              $tech = CMDBSource::QueryToArray("SELECT * FROM ntticketnetworks WHERE ticket_id=".$aDBInfo['id']);

              $threeTech = 0;
              if(!empty($tech)){
                  foreach ($tech as $rows) {
                      switch (true) {
                          case ($rows['network']=='2G' && in_array('2G', $availableNw)): $TWG = "DOWN"; $TWGCLR = "#FF0000"; $threeTech++; break;
                          case ($rows['network']=='3G' && in_array('3G', $availableNw)): $TRG = "DOWN"; $TRGCLR = "#FF0000"; $threeTech++; break;
                          case ($rows['network']=='4G' && in_array('4G', $availableNw)): $FRG = "DOWN"; $FRGCLR = "#FF0000"; $threeTech++; break;
                      }
                  }
              }

            if($threeTech==3){  
          ?>

          <tr>
            <td style="vertical-align: middle;"> <?php echo "<a href='https://nt3.nectarinfotel.com/pages/UI.php?operation=details&class=Incident&id=".$aDBInfo['id']."&c[menu]=Incident%3AOpenIncidents'>".$aDBInfo['ref']."</a>" ?> </td>
            <td> <?php echo $aDBInfo['title'] ?> </td>
            <td> <?php echo $aDBInfo['province'] ?> </td>
            <td style="display: table-cell;"> <?php echo $sitePricipal ?> </td>
            <td style="display: table-cell;"> <?php echo $dependantSites ?> </td>
            <td> <?php echo $aDBInfo['reason']."-".$aDBInfo['sub_reason']; ?> </td>
            <td> <?php echo "<span style='background-color:".$TWGCLR."'>".$TWG."</span>"; ?> </td>
            <td> <?php echo "<span style='background-color:".$TRGCLR."'>".$TRG."</span>"; ?> </td>
            <td> <?php echo "<span style='background-color:".$FRGCLR."'>".$FRG."</span>"; ?> </td>
            <td> <?php echo $age ?> </td>
          </tr>

        <?php
              }
            }// EOF Foreach Open Incidents
          }else{
            echo "<tr colspan='10'><td>No Open Incident Available</td></tr>";
          }
        ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
