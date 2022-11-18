<?php /********** Province ***********/
include('../webservices/wbdb.php');
?>

<!--<div id="addSiteModalNew" class="modal" style="padding-left: 89px;">-->
<div class="ui-layout-content" style="overflow: auto; position: relative; height: 461px; visibility: visible;">
  <h1><img src="https://nt3test.nectarinfotel.com/env-production/nt3-incident-mgmt-itil/images/incident.png" style="vertical-align:middle;">&nbsp;Modification of NDR</h1>
  <!-- <div>
  <h1>Registo de Controlo de Acesso aos Sites</h1></div> -->
<!--               <span class="close closeSiteNew" style="margin-top: 10px!important;font-size: 20px!important;">&times;</span>-->
              
<div class="addNewSite wizContainer">
                        
<div class="table-responsive">
 
    <table class="table listResults table-striped activtabl1" style="border: 1px solid #dcdcdc;">
     <tr> <th class="header">Description</th>
         <th class="header">Province</th>
         <th class="header">Location</th>
         <th class="header">Reason</th>
         <th class="header">Access Type</th>
         <th class="header">Company</th>
           <th class="header">Movicel Area</th>
           <th class="header">Service</th>
           <th class="header">Employee</th>
           <th class="header">Action</th>
         </tr>
         <?php  $query = "SELECT * FROM npactivity";
    if($query!=''){
    $result = mysqli_query($conf, $query);
    if ($result) {
//echo $query;
      if(mysqli_num_rows($result)>0){
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
         <tbody>
             <tr>
             <td style="border-bottom: 1px solid #dcdcdc;"><?php echo $row['description'] ?></td>
             <td style="border-bottom: 1px solid #dcdcdc;"><?php echo $row['province'] ?></td>
             <td style="border-bottom: 1px solid #dcdcdc;"><?php echo $row['location'] ?></td>
             <td style="border-bottom: 1px solid #dcdcdc;"><?php echo $row['reason'] ?></td>
             <td style="border-bottom: 1px solid #dcdcdc;"><?php echo $row['accesstype'] ?></td>
             <td style="border-bottom: 1px solid #dcdcdc;"><?php echo $row['company'] ?></td>
             <td style="border-bottom: 1px solid #dcdcdc;"><?php echo $row['movicelarea'] ?></td>
             <td style="border-bottom: 1px solid #dcdcdc;"><?php echo $row['service'] ?></td>
             <td style="border-bottom: 1px solid #dcdcdc;"><?php echo $row['employee'] ?></td>
             <td style="border-bottom: 1px solid #dcdcdc;"><a href="https://nt3test.nectarinfotel.com/application/editbyactivity.php?id=<?php echo $row['activityid'] ?>">Edit</a></td>
         </tr>
         </tbody>
         <?php    }
                        }
                }
                                                } ?>

</table>
     
</div>
  <br><br><br><br>
       
<!--  <input type="button" class="createSite" value="Create" style="padding: 6px 26px 6px 26px;background-color: #F17422;color: #ffffff;cursor: pointer;float:right;">-->
                        </div>
                      </div>
<!--                    </div>-->