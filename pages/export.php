<?php
require_once('../webservices/wbdb.php');
//  if(isset($_POST["Export"])){
         
         header('Content-Type: text/csv; charset=utf-8');  
         header('Content-Disposition: attachment; filename=Site.csv');  
         $output = fopen("php://output", "w");  
         fputcsv($output, array('Site ID', 'Site Name', 'Province', 'Munciple', 'Locality','Latitude ','Longitude ','Site Code','Vendor','Responsible Area','Priority','Priority Comment','Element Type','Model','MSC','MGW','BSC','RNC','Phase','Service Date','Stage','Sub Stage','Start Date','End_Date','Created Date'));  
         $query = "SELECT ntsites.site_id,ntsites.site_name,ntsiteprovince.province,ntsitemunciple.munciple,nplocation.locationname,ntsites.lat,ntsites.lng,ntsites.site_code,ntsites.vendor,ntsites.responsible_area,ntsites.priority,ntsites.priority_comment,ntsites.element_type,ntsites.model,ntsites.msc,ntsites.mgw,ntsites.bsc,ntsites.rnc,ntsites.phase,ntsites.service_date,ntsites.stage,ntsites.sub_stage,ntsites.start_date,ntsites.end_date,ntsites.created_date FROM ntsites join ntsiteprovince on ntsites.province=ntsiteprovince.province_id left join ntsitemunciple on ntsites.munciple=ntsitemunciple.munciple_id left join nplocation on ntsites.locality=nplocation.locationid WHERE ntsites.is_active=1 ORDER BY ntsites.site_id ASC";  
         $result = mysqli_query($conf, $query);  
         while($row = mysqli_fetch_assoc($result))  
         {  
            $row['site_name'] = utf8_encode($row['site_name']);
            fputcsv($output, $row);  
         }  
         fclose($output);  
    // } 
    ?>