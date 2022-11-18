<?php
require_once('../webservices/wbdb.php');

$data = array("flag"=>false,"msg"=>"");

if (isset($_POST["import"])) {
    
    $fileName = $_FILES["Net_file"]["tmp_name"];
    if ($_FILES["Net_file"]["size"] > 0) {
        
        $file = fopen($fileName, "r");
        $i = 1; $msg = '';
        $csvData = array();

        while (($columnCSV = fgetcsv($file, 10000, ",")) !== FALSE) {
            if(array('') !== $columnCSV) { 
                array_push($csvData,$columnCSV);
                $i++;
            }
        }
        
        /*echo "<pre>";
        print_r($csvData);
        exit();*/

        $csvData = array_map("unserialize", array_unique(array_map("serialize", $csvData)));

        $siteCheck = array();
        $myfile = fopen("RemainingNet.txt", "w") or die("Unable to open file!");
        foreach ($csvData as $column) {
            
            $siteName1 = explode('-', $column[0]);
            $siteName1 = isset($siteName1[1])? $siteName1[1]:$siteName1[0];
            $siteName2 = str_replace('_', ' ', $siteName1);
            $siteName3 = str_replace(' ', '_', $siteName1);

            $siteName4 = explode('_', $rows['SiteName']);
            $siteName4 = isset($siteName4[1])? $siteName4[1]:$siteName4[0];
            $siteName5 = str_replace('_', ' ', $siteName4);
            $siteName6 = str_replace(' ', '_', $siteName4);


           if($column[0]!=''){

            if(!in_array($column[0], $siteCheck)){ 

                array_push($siteCheck,$column[0]);
                $q1 = "SELECT site_name,site_id FROM ntsites WHERE (site_name='".$column[0]."' OR site_name = '".$siteName1."' OR site_name = '".$siteName2."' OR site_name = '".$siteName3."' OR site_name='".$siteName4."' OR site_name='".$siteName5."' OR site_name='".$siteName6."') AND site_name!=''";

                /*echo "<pre>";
                print_r($column);

                echo $q1.'<br>';*/
                //exit();

                $result1 = mysqli_query($conf,$q1);
                if($result1){
                    if($result1->num_rows>0){
                        $siteArr = mysqli_fetch_all($result1,MYSQLI_ASSOC);
                        
                        $queryAddSite = "UPDATE ntsites SET nettype='".$column[1]."',network='".$column[2]."' WHERE site_id = ".$siteArr[0]['site_id'];
                       
                        $resultAddSite = $conf->query($queryAddSite);
                        echo "Site : ".$column[0]." / Site ID : ".$siteArr[0]['site_id']." AND NetType: ".$column[1]." AND Network : ".$column[2]."<br/>";
                    }else{
                        $txt = "Site : ".$column[0]." AND NetType: ".$column[1]." AND Network : ".$column[2]."***".PHP_EOL ;
                        fwrite($myfile, $txt);
                    }
                }       
            }
          }

        }
        fclose($myfile);
        echo "Done";
    }
}
?>