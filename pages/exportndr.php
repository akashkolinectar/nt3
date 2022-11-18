<?php
//$flag = FALSE;
    //if(isset($_POST['siteList'])){
      require_once('../webservices/wbdb.php');
      require_once('../webservices/PHPExcel/Classes/PHPExcel.php');
      //$sites = implode(",", $_POST['siteList']);
      /*$data_remove = file_get_contents('php://input');
      $rowid = json_decode($data_remove, TRUE);*/

//echo $rowid['serviceid']; exit;
     /*header('Content-Type: text/csv; charset=utf-8');
         header('Content-Disposition: attachment; filename=Site.csv');  
         $output = fopen("php://output", "w");*/

         /*$outputPath = '../webservices/ndrexport.csv';
     $output = fopen($outputPath, "w");*/

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle("NDR");              
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', "NDR");
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', "Description");
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', "Site");
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', "Província");
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', "Municipio");
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', "Motivo");
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', "Provider");
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', "Service");
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', "Area");
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', "Resposible Team");
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', "Created Date");

        $styleArr = array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '422462')
                    ),
                    'font'  => array(
                        'bold'  => true,
                        'color' => array('rgb' => 'F17422'),
                        /*'size'  => 15,
                        'name'  => 'Verdana'*/
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
                    'borders' => array(
                        'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN,
                          'color' => array('rgb' => 'FFFFFF')
                        )
                    )
                );
        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleArr);
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(-1);

         /*fputcsv($output, array('Activity ID', 'Activity Code', 'Description','Province','Munciple','Locality','Reason','Other Reason','Access Type','Fuel Found','Fuel Filled','Provider','Service','Area','Employee Name','Employee Lastname','Created Date'));*/  

         $provider = "";
         if(isset($_GET['provider']) && $_GET['provider']!=""){
          $provider = " AND npactivity.provider = ".$_GET['provider']." AND npactivity.accesstype = 'External'";
         }         

        /* $service = "";
         if(isset($_GET['service']) && $_GET['service']!=""){
          $service = " AND npactivity.service = ".$_GET['service']." AND npactivity.accesstype = 'External'";
         } */        

         $from = "";
         if(isset($_GET['from']) && $_GET['from']!=""){
          $from = " AND DATE(npactivity.created_date) >= '".date('Y-m-d',strtotime($_GET['from']))."'";
         }         

         $to = "";
         if(isset($_GET['to']) && $_GET['to']!=""){
          $to = " AND DATE(npactivity.created_date) <= '".date('Y-m-d',strtotime($_GET['to']))."'";
         }

         $reason = "";
         if(isset($_GET['reason']) && $_GET['reason']!=""){
            //$reason =join(',',$_GET['reason']);

          $reason = " AND npactivity.selectedreason IN(".$_GET['reason'].")";
         }

/*echo "SELECT npactivity.activityid,npactivity.description,ntsiteprovince.province,ntsitemunciple.munciple,nplocation.locationname,npreason.reason_name,npactivity.reason,npactivity.accesstype,npactivity.fuel_found,npactivity.fuel_filled,(ntcontract.name) as provider,(ntservice.name) as service,(ng2.name) as intarea,np1.first_name,nc1.name,npactivity.result,npactivity.created_date FROM npactivity join ntsiteprovince on npactivity.province=ntsiteprovince.province_id left join ntsitemunciple on npactivity.munciple=ntsitemunciple.munciple_id left join nplocation on npactivity.location=nplocation.locationid left join npreason on npactivity.selectedreason=npreason.npreasonid left join ntservice on npactivity.service=ntservice.id 
                left join ntcontract on npactivity.provider=ntcontract.id
                left join ntorganization on npactivity.provider=ntorganization.id 
                left join ntorganization ng2 on npactivity.movicelarea=ng2.id  
                left JOIN ntcontact nc1 on npactivity.extemployee=nc1.id OR  npactivity.employee=nc1.id 
                left JOIN ntperson np1 on npactivity.extemployee=np1.id OR npactivity.employee=np1.id 
                where npactivity.status='1' ".$provider."".$service."".$from."".$to." GROUP BY npactivity.activityid ORDER BY npactivity.activityid DESC";*/



        /* $query = CMDBSource::QueryToArray("SELECT npactivity.activityid,npactivity.actvitycode,npactivity.description,ntsiteprovince.province,ntsitemunciple.munciple,nplocation.locationname,npreason.reason_name,npactivity.reason,npactivity.accesstype,npactivity.fuel_found,npactivity.fuel_filled,(ntcontract.name) as service,(ntservice.name) as provider,(ng2.name) as intarea,np1.first_name,nc1.name,npactivity.result,npactivity.created_date FROM npactivity join ntsiteprovince on npactivity.province=ntsiteprovince.province_id left join ntsitemunciple on npactivity.munciple=ntsitemunciple.munciple_id left join nplocation on npactivity.location=nplocation.locationid left join npreason on npactivity.selectedreason=npreason.npreasonid     left join ntservice on npactivity.provider=ntservice.id 
                left join ntcontract on npactivity.service=ntcontract.id
                left join ntorganization on npactivity.provider=ntorganization.id 
                left join ntorganization ng2 on npactivity.movicelarea=ng2.id  
                left JOIN ntcontact nc1 on npactivity.extemployee=nc1.id OR  npactivity.employee=nc1.id 
                left JOIN ntperson np1 on npactivity.extemployee=np1.id OR npactivity.employee=np1.id 
                where npactivity.status='1' ".$provider."".$service."".$from."".$to." GROUP BY npactivity.activityid ORDER BY npactivity.activityid DESC");*/
        
        $query = "SELECT npactivity.activityid,npactivity.actvitycode,npactivity.description,ntsiteprovince.province,ntsitemunciple.munciple,nplocation.locationname,npreason.reason_name,npactivity.reason,npactivity.accesstype,npactivity.fuel_found,npactivity.fuel_filled,(ntcontract.name) as service,(ntservice.name) as provider,(ng2.name) as intarea,np1.first_name,nc1.name,npactivity.result,npactivity.created_date,st.site_name FROM npactivity join ntsiteprovince on npactivity.province=ntsiteprovince.province_id left join ntsitemunciple on npactivity.munciple=ntsitemunciple.munciple_id left join nplocation on npactivity.location=nplocation.locationid left join npreason on npactivity.selectedreason=npreason.npreasonid     left join ntservice on npactivity.provider=ntservice.id 
                left join ntcontract on npactivity.service=ntcontract.id
                left join ntorganization on npactivity.provider=ntorganization.id 
                left join ntactivitysite actst on actst.activity_id=npactivity.activityid 
                left join ntsites st on st.site_id=actst.site_id 
                left join ntorganization ng2 on npactivity.movicelarea=ng2.id  
                left JOIN ntcontact nc1 on npactivity.extemployee=nc1.id OR  npactivity.employee=nc1.id 
                left JOIN ntperson np1 on npactivity.extemployee=np1.id OR npactivity.employee=np1.id 
                where npactivity.status='1' ".$provider."".$reason."".$from."".$to." GROUP BY npactivity.activityid ORDER BY npactivity.activityid DESC";
                /*where npactivity.status='1' ".$provider."".$service."".$from."".$to." GROUP BY npactivity.activityid ORDER BY npactivity.activityid DESC";*/

          /*$query = CMDBSource::QueryToArray("SELECT npactivity.activityid,npactivity.actvitycode,npactivity.description,ntsiteprovince.province,ntsitemunciple.munciple,nplocation.locationname,npreason.reason_name,npactivity.reason,npactivity.accesstype,npactivity.fuel_found,npactivity.fuel_filled,(ntcontract.name) as service,(ntservice.name) as provider,(ng2.name) as intarea,np1.first_name,nc1.name,npactivity.result,npactivity.created_date FROM npactivity join ntsiteprovince on npactivity.province=ntsiteprovince.province_id left join ntsitemunciple on npactivity.munciple=ntsitemunciple.munciple_id left join nplocation on npactivity.location=nplocation.locationid left join npreason on npactivity.selectedreason=npreason.npreasonid     left join ntservice on npactivity.provider=ntservice.id 
                left join ntcontract on npactivity.service=ntcontract.id
                left join ntorganization on npactivity.provider=ntorganization.id 
                left join ntorganization ng2 on npactivity.movicelarea=ng2.id  
                left JOIN ntcontact nc1 on npactivity.extemployee=nc1.id OR  npactivity.employee=nc1.id 
                left JOIN ntperson np1 on npactivity.extemployee=np1.id OR npactivity.employee=np1.id 
                where npactivity.status='1' ".$provider."".$reason."".$from."".$to." GROUP BY npactivity.activityid ORDER BY npactivity.activityid DESC");*/

          $result = mysqli_query($conf,$query);  
         //if(!empty($query)){
         if($result){
            $rowCount = 2;
            if($result->num_rows>0){
                //foreach ($query as $aDBInfo) { 
                while ($aDBInfo=mysqli_fetch_array($result,MYSQLI_ASSOC)) { 
                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $aDBInfo['actvitycode']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $aDBInfo['description']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $aDBInfo['site_name']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $aDBInfo['province']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $aDBInfo['munciple']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $aDBInfo['reason_name']);
                    /*$objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $aDBInfo['reason']);*/
                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $aDBInfo['provider']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $aDBInfo['service']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $aDBInfo['intarea']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, ucwords($aDBInfo['first_name'].' '.$aDBInfo['name']));
                    $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, date('Y-m-d',strtotime($aDBInfo['created_date'])));
                    $rowCount++;
                }
            }
         }
         //print_r($result); exit();
         //$result = mysqli_query($conf, $query);  
         /*while($row =mysqli_fetch_array($result, MYSQLI_ASSOC))  
         {  
              fputcsv($output, $row);  
         }  
         fclose($output);  
         if($result){
        $flag = TRUE;
      }
    
    header('Content-Type: application/json');
    echo json_encode(
        [
            "filename" => basename($outputPath),
        ]
    );*/
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
    
    $objPHPExcel->getActiveSheet()->freezePane('B2');

    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="NDRReport.xls"');
    $objWriter->save('php://output');
?>