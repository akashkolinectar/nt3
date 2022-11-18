<?php
require_once('../webservices/wbdb.php');

$data = array("flag"=>false,"msg"=>"");

if (isset($_POST["import"])) {
    
    $fileName = $_FILES["site_info_file"]["tmp_name"];
    if ($_FILES["site_info_file"]["size"] > 0) {
        
        $file = fopen($fileName, "r");
        $i = 1; $msg = '';
        $csvData = array();

        while (($columnCSV = fgetcsv($file, 10000, ",")) !== FALSE) {
          array_push($csvData,$columnCSV);
          $i++;
        }

        $myfile = fopen("RemainingSiteInfo.txt", "w") or die("Unable to open file!");
        $myExlfile = fopen("RemainingSiteInfoExcel.csv", "w") or die("Unable to open file!");

        $ntTicketIds = array();
        foreach ($csvData as $column) {
            
            $siteName1 = explode('-', $column[4]);
            $siteName1 = isset($siteName1[1])? $siteName1[1]:$siteName1[0];
            $siteName2 = str_replace('_', ' ', $siteName1);
            $siteName3 = str_replace(' ', '_', $siteName1);
            $siteName4 = explode('_', $column[4]);
            $siteName4 = isset($siteName4[1])? $siteName4[1]:$siteName4[0];
            $siteName5 = str_replace('-', ' ', $siteName4);
            $siteName6 = str_replace(' ', '-', $siteName4);

            
            $q1 = "SELECT site_name,site_id FROM ntsites WHERE (site_name='".utf8_encode($column[4])."' OR site_name = '".utf8_encode($siteName1)."' OR site_name = '".utf8_encode($siteName2)."' OR site_name = '".utf8_encode($siteName3)."' OR site_name='".utf8_encode($siteName4)."' OR site_name='".utf8_encode($siteName5)."' OR site_name='".utf8_encode($siteName6)."') AND site_name!='' AND is_active=1";

            $queryResult = mysqli_query($conf,$q1);

            if($queryResult){

                if($queryResult->num_rows>0){
                    
                    $siteArr = mysqli_fetch_all($queryResult,MYSQLI_ASSOC);
                        $queryPart = '';
                        if($column[1]!=''){
                            $query = "SELECT bsc_id as id FROM ntsitebsc WHERE bsc='".$column[1]."'";
                            $result = mysqli_query($conf, $query);
                            if ($result) {
                                if(mysqli_num_rows($result)<=0){
                                    $query = "INSERT INTO ntsitebsc (bsc) VALUES ('".$column[1]."')";
                                    $result = mysqli_query($conf, $query);
                                }
                            }
                            $queryPart .= "bsc='".$column[1]."', ";
                        }
                        if($column[2]!=''){
                            $query = "SELECT rnc_id as id FROM ntsiternc WHERE rnc='".$column[2]."'";
                            $result = mysqli_query($conf, $query);
                            if ($result) {
                                if(mysqli_num_rows($result)>0){
                                    $query = "INSERT INTO ntsiternc (rnc) VALUES ('".$column[2]."')";
                                    $result = mysqli_query($conf, $query);
                                }
                            }
                            $queryPart .= "rnc='".$column[2]."', ";
                        }

                        if($column[15]!=''){
                            $query = "SELECT vendor_id as id FROM ntsitevendor WHERE vendor='".$column[15]."'";
                            $result = mysqli_query($conf, $query);
                            if ($result) {
                                if(mysqli_num_rows($result)>0){
                                    $query = "INSERT INTO ntsitevendor (vendor) VALUES ('".$column[15]."')";
                                    $result = mysqli_query($conf, $query);
                                }
                            }
                            $queryPart .= "vendor='".$column[15]."'";
                        }

                        $updateQuery = "UPDATE ntsites SET $queryPart WHERE site_id=".$siteArr[0]['site_id'];
                        $result = mysqli_query($conf, $updateQuery);
                        if($result){
                            echo "******".$column[4]."*****Added";
                        }else{
                            $txt = "ID : ".$column[0]." Site: ".$column[4]." \n";
                            fwrite($myfile, $txt);
                            fputcsv($myExlfile, $column);
                        }
                        echo "<br/>";
                    }else{
                        $txt = "ID : ".$column[0]." Site: ".$column[6]." TicketId: ".$column[5]." \n";
                        fwrite($myfile, $txt);
                        fputcsv($myExlfile, $column);
                    }
                }else{
                    $txt = "ID : ".$column[0]." Site: ".$column[6]." TicketId: ".$column[5]." \n";
                    fwrite($myfile, $txt);
                    fputcsv($myExlfile, $column);
                }
            } // For Loop 
        fclose($myfile);
        fclose($myExlfile);
        echo "Done";
    }
}
?>