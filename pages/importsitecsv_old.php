<?php
require_once('../webservices/wbdb.php');
if (isset($_POST["import"])) {
    
    $fileName = $_FILES["file"]["tmp_name"];
    if ($_FILES["file"]["size"] > 0) {
        
        $file = fopen($fileName, "r");
        STATIC $header = 1;
        $i = 2; $msg = '';
        $csvData = array();

        while (($columnCSV = fgetcsv($file, 10000, ",")) !== FALSE) {
          if($columnCSV[0]==""){
            $msg = "Site name should not be empty at row $i";
            break;
          } if($columnCSV[7]==""){
            $msg = "Site Code should not be empty at row $i";
            break;
          } if($columnCSV[8]==""){
            $msg = "Vendor should not be empty at row $i";
            break;
          } if(is_numeric($columnCSV[7])){
            $msg = "Site code should be numeric at row $i";
            break;
          }
          array_push($csvData,$columnCSV);
          $i++;
        }
        
        if($msg!=''){
            echo "<script type='text/javascript'>alert('$msg');location.href='http://localhost/nt3live/pages/UI.php?operation=cancel&c[menu]=department&c[feature]=addSite'</script>";
            exit();
        }else{
        $j = 2;
        foreach ($csvData as $column) {
                
            if($header==1){
              // continue;
            }else{
                
                $sqlInsert = "INSERT into ntsites (site_name,province,munciple,locality,lat,lng,site_code,vendor,responsible_area,priority,priority_comment,element_type,model,msc,mgw,bsc,rnc,phase,service_date,stage,sub_stage,start_date,end_date,created_date,is_active) values ('" . $column[0] . "','" . $column[1] . "','" . $column[2] . "','" . $column[3] . "','" . $column[4] . "','" . $column[5] . "','" . $column[6] . "','" . $column[7] . "','" . $column[8] . "','" . $column[9] . "','" . $column[10] . "','" . $column[11] . "','" . $column[12] . "','" . $column[13] . "','" . $column[14] . "','" . $column[15] . "','" . $column[16] . "','" . $column[17] . "','" . date('Y-m-d',strtotime($column[18])) . "','" . $column[19] . "','" . $column[20] . "','" . date('Y-m-d',strtotime($column[21])) . "','" . date('Y-m-d',strtotime($column[22])) . "','" .date('Y-m-d H:i:s'). "',1)";
          
                $result = mysqli_query($conf, $sqlInsert);

                $sql = "select * from ntsiteresponsible where responsible_area='" . $column[8] . "'";

                $result = $conf->query($sql);

                if ($result->num_rows==0) {
                    if ($column[8] != '') {
                        $query1 = "INSERT INTO ntsiteresponsible(responsible_area)VALUES('" . $column[8] . "')";

                        $result1 = mysqli_query($conf, $query1);

                    }
                }

                //Priority
                $sql = "select * from ntsitepriority where priority='" . $column[9] . "'";
                $result = $conf->query($sql);
                if ($result->num_rows==0) {
                    if ($column[9] != '') {
                        $query1 = "INSERT INTO ntsitepriority(priority)VALUES('" . $column[9] . "')";
                        $result1 = mysqli_query($conf, $query1);
                    }
                }
              
                //Element Type
                $sql = "select * from ntsiteelementtype where element_type='" . $column[11] . "'";
                $result = $conf->query($sql);
                if ($result->num_rows==0) {
                    if ($column[11] != '') {
                        $query1 = "INSERT INTO ntsiteelementtype(element_type)VALUES('" . $column[11] . "')";
                        $result1 = mysqli_query($conf, $query1);
                    }
                }
                  
                //Vendor
                $sql = "select * from ntsitevendor where vendor='" . $column[7] . "'";
                $result = $conf->query($sql);
                if ($result->num_rows==0) {
                    if ($column[7] != '') {
                        $query1 = "INSERT INTO ntsitevendor(vendor)VALUES('" . $column[7] . "')";
                        $result1 = mysqli_query($conf, $query1);
                    }
                }


                //Model
                $sql = "select * from ntsitemodel where model='" . $column[12] . "'";
                $result = $conf->query($sql);
                if ($result->num_rows==0) {
                    if ($column[12] != '') {
                        $query1 = "INSERT INTO ntsitemodel(model)VALUES('" . $column[12] . "')";
                        $result1 = mysqli_query($conf, $query1);
                    }
                }

                //MSC
                $sql = "select * from ntsitemsc where msc='" . $column[13] . "'";
                $result = $conf->query($sql);
                if ($result->num_rows==0) {
                    if ($column[13] != '') {
                        $query1 = "INSERT INTO ntsitemsc(msc)VALUES('" . $column[13] . "')";
                        $result1 = mysqli_query($conf, $query1);
                    }
                }

                //MGW
                $sql = "select * from ntsitemgw where mgw='" . $column[14] . "'";
                $result = $conf->query($sql);
                if ($result->num_rows==0) {
                    if ($column[14] != '') {
                        $query1 = "INSERT INTO ntsitemgw(mgw)VALUES('" . $column[14] . "')";
                        $result1 = mysqli_query($conf, $query1);
                    }
                }

                //BSC
                $sql = "select * from ntsitebsc where bsc='" . $column[17] . "'";
                $result = $conf->query($sql);
                if ($result->num_rows==0) {
                    if ($column[17] != '') {
                        $query1 = "INSERT INTO ntsitebsc(bsc)VALUES('" . $column[17] . "')";
                        $result1 = mysqli_query($conf, $query1);
                    }
                }

                //RNC
                $sql = "select * from ntsiternc where rnc='" . $column[18] . "'";
                $result = $conf->query($sql);
                if ($result->num_rows==0) {
                    if ($column[18] != '') {
                        $query1 = "INSERT INTO ntsiternc(rnc)VALUES('" . $column[18] . "')";
                        $result1 = mysqli_query($conf, $query1);
                    }
                }
                       
                //Province 
                $sql = "select * from ntsiteprovince where province='" . $column[1] . "'";
                $result = $conf->query($sql);
                $province_id = 0;
                if ($result->num_rows==0) {
                    if ($column[1] != '') {
                        $query1 = "INSERT INTO ntsiteprovince(province)VALUES('" . $column[1] . "')";
                        $result1 = mysqli_query($conf, $query1);
                        $province_id = $conf->insert_id;
                    }
                    //var_dump($result); exit;
                }else{
                    $info1 = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    if(!empty($info1)){
                        $province_id = $info1[0]['province_id'];
                    }
                }

                //Munciple  
                $sql = "select * from ntsitemunciple where munciple='" . $column[2] . "'";
                $result = $conf->query($sql);
                $munciple_id = 0;
                if ($result->num_rows==0) {
                    if ($column[2] != '') {
                        $query1 = "INSERT INTO ntsitemunciple(province_id,munciple)VALUES(".$province_id.",'" . $column[2] . "')";
                        $result1 = mysqli_query($conf, $query1);
                        $munciple_id = $conf->insert_id;
                    }
                }else{
                    $info2 = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    if(!empty($info2)){
                        $munciple_id = $info2[0]['munciple_id'];
                    }
                }

                //Locality
                $sql = "select * from nplocation where locationname='" . $column[3] . "'";
                $result = $conf->query($sql);
                if ($result->num_rows==0) {
                    if ($column[3] != '') {
                        $query1 = "INSERT INTO nplocation(munciple_id,locationname)VALUES(".$munciple_id.",'" . $column[3] . "')";
                        $result1 = mysqli_query($conf, $query1);
                    }
                }

                //Phase   
                $sql = "select * from ntsitephase where phase='" . $column[17] . "'";
                $result = $conf->query($sql);
                if ($result->num_rows==0) {
                    if ($column[16] != '') {
                        $query1 = "INSERT INTO ntsitephase(phase)VALUES('" . $column[17] . "')";
                        $result1 = mysqli_query($conf, $query1);
                    }
                }

                //Stage    
                $sql = "select * from ntsitestage where stage='" . $column[19] . "'";
                $result = $conf->query($sql);
                if ($result->num_rows==0) {
                    if ($column[19] != '') {
                        $query1 = "INSERT INTO ntsitestage(stage)VALUES('" . $column[19] . "')";
                        $result1 = mysqli_query($conf, $query1);
                    }
                }

                //Sub Stage    
                $sql = "select * from ntsitesubstage where sub_stage='" . $column[20] . "'";
                $result = $conf->query($sql);
                if ($result->num_rows==0) {
                    if ($column[20] != '') {
                        $query1 = "INSERT INTO ntsitesubstage(sub_stage)VALUES('" . $column[20] . "')";
                        $result1 = mysqli_query($conf, $query1);
                    }
                }

                $j++;
            } // EOF else (check if not header)
            $header++;
          } // EOF While loop
         
          if (! empty($result)) {
                $type = "success";
                echo "<script type='text/javascript'>alert('All sites added successfully');location.href='http://localhost/nt3live/pages/UI.php?operation=cancel&c[menu]=department&c[feature]=addSite'</script>";
               // header("Location: https://nt3test.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=addsite"); 
            } else {
                $type = "error";
                echo "<script type='text/javascript'>alert('Unable to add site.There is problem detected at row $j');location.href='http://localhost/nt3live/pages/UI.php?operation=cancel&c[menu]=department&c[feature]=addSite'</script>";
               // header("Location: https://nt3test.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=addsite"); 
            }

        } // EOF else (msg is blank)
    
    } // EOF file check

} // EOF import post check
?>